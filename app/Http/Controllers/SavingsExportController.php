<?php

namespace App\Http\Controllers;

use App\Models\BukuTabungan;
use App\Models\BiayaSekolah;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Pembayaran;

class SavingsExportController extends Controller
{
    public function preview($id)
    {
        $data = $this->prepareData($id);
        return view('exports.savings-withdrawal', $data);
    }

    public function exportPDF($id)
    {
        $data = $this->prepareData($id);
        $pdf = PDF::loadView('exports.savings-withdrawal', $data);
        
        $filename = 'savings-withdrawal-' . $data['bukuTabungan']->nomor_urut . '.pdf';
        return $pdf->download($filename);
    }

    private function prepareData($id)
    {
        $bukuTabungan = BukuTabungan::with(['siswa.kelas'])->findOrFail($id);
        
        // Calculate total savings
        $totalSimpanan = $bukuTabungan->transaksis()
            ->where('jenis', 'simpanan')
            ->sum('jumlah');
    
        // Calculate remaining installments
        $totalCicilan = $bukuTabungan->transaksis()
            ->where('jenis', 'cicilan')
            ->sum('jumlah');
        $totalPenarikanCicilan = $bukuTabungan->transaksis()
            ->where('jenis', 'penarikan')
            ->where('sumber_penarikan', 'cicilan')
            ->sum('jumlah');
        $sisaCicilan = $totalCicilan - $totalPenarikanCicilan;
    
        // Add remaining installments to total savings
        $totalSimpanan += $sisaCicilan;
        $bukuTabungan->total_simpanan = $totalSimpanan;
    
        // Get admin percentage based on student category
        $adminPercentage = $this->getAdminPercentage($bukuTabungan->siswa->category);
        $adminFee = ($totalSimpanan * $adminPercentage) / 100;
    
        // Calculate deductions
        $deductions = $this->calculateDeductions($bukuTabungan);
        
        // Calculate remaining savings
        $remainingSavings = ($totalSimpanan - $adminFee) - $deductions['total'];
    
        return [
            'bukuTabungan' => $bukuTabungan,
            'adminPercentage' => $adminPercentage,
            'adminFee' => $adminFee,
            'deductions' => $deductions,
            'remainingSavings' => $remainingSavings,
            'sisaCicilan' => $sisaCicilan // Added to show in the PDF
        ];
    }

    private function getAdminPercentage($category)
    {
        // Early withdrawal is handled separately if needed
        $isEarlyWithdrawal = false;
    
        return match($category) {
            'Anak Guru' => 5,
            'Anak Yatim', 'Kakak Beradik', 'Anak Normal' => $isEarlyWithdrawal ? 10 : 8,
            default => 8,
        };
    }

    private function calculateDeductions($bukuTabungan)
    {
        $siswa = $bukuTabungan->siswa;
        $category = $siswa->category;
        
        // Get BiayaSekolah for current academic year
        $biayaSekolah = BiayaSekolah::where('tahun_ajaran_id', $siswa->academic_year_id)
            ->where('kategori_siswa', $category)
            ->get()
            ->keyBy('jenis_biaya');
    
        // Get paid fees
        $paidFees = Pembayaran::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $siswa->academic_year_id)
            ->where('is_processed', true)
            ->get()
            ->groupBy('jenis_biaya');
    
        // Calculate unpaid SPP months
        $unpaidMonths = $this->calculateUnpaidMonths($siswa);
        
        $deductions = [
            'ikk' => $this->calculateUnpaidFee('IKK', $biayaSekolah, $paidFees, $category),
            'spp' => $this->calculateUnpaidFee('SPP', $biayaSekolah, $paidFees, $category) * $unpaidMonths,
            'initial_fee' => $this->calculateUnpaidFee('Uang Pangkal', $biayaSekolah, $paidFees, $category),
            'uam' => $this->calculateUnpaidFee('UAM', $biayaSekolah, $paidFees, $category),
            'thb' => $this->calculateUnpaidFee('THB', $biayaSekolah, $paidFees, $category),
            'photo' => $this->calculateUnpaidFee('Foto', $biayaSekolah, $paidFees, $category),
            'report_card' => $this->calculateUnpaidFee('Raport', $biayaSekolah, $paidFees, $category),
            'previous_arrears' => $bukuTabungan->previous_arrears ?? 0,
            'loan' => $bukuTabungan->transaksis()
                ->where('jenis', 'penarikan')
                ->where('sumber_penarikan', 'cicilan')
                ->sum('jumlah'),
        ];
        
        $deductions['total'] = array_sum($deductions);
        return $deductions;
    }

    private function calculateUnpaidFee($jenisBiaya, $biayaSekolah, $paidFees, $category)
    {
        // If student category doesn't need to pay this fee
        if ($this->isExemptFromFee($category, $jenisBiaya)) {
            return 0;
        }
    
        // Get the standard fee amount
        $standardFee = $biayaSekolah->get($jenisBiaya)->jumlah ?? 0;
    
        // Get total paid amount for this fee type
        $paidAmount = $paidFees->get($jenisBiaya, collect())->sum('jumlah') ?? 0;
    
        // Apply category-based discount if applicable
        $requiredAmount = $this->applyDiscount($standardFee, $category, $jenisBiaya);
    
        // Return remaining unpaid amount
        return max($requiredAmount - $paidAmount, 0);
    }

    private function calculateUnpaidMonths($siswa)
    {
        $tahunAjaran = $siswa->academicYear;
        $startDate = \Carbon\Carbon::parse($tahunAjaran->year_start);
        $endDate = \Carbon\Carbon::parse($tahunAjaran->year_end);
        
        // Total months in academic year
        $totalMonths = $startDate->diffInMonths($endDate) + 1;
        
        // Count paid months
        $paidMonths = Pembayaran::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('jenis_biaya', 'SPP')
            ->where('is_processed', true)
            ->count();
        
        return max($totalMonths - $paidMonths, 0);
    }

    private function isExemptFromFee($category, $jenisBiaya)
    {
        $exemptions = [
            'Anak Guru' => ['SPP', 'IKK', 'Uang Pangkal'],
            'Anak Yatim' => ['SPP'],
        ];
    
        return isset($exemptions[$category]) && in_array($jenisBiaya, $exemptions[$category]);
    }

    private function applyDiscount($amount, $category, $jenisBiaya)
    {
        if ($category === 'Kakak Beradik' && in_array($jenisBiaya, ['SPP', 'IKK'])) {
            return $amount * 0.8; // 20% discount
        }
        return $amount;
    }
}