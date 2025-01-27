@extends('layouts.layout')

@section('title', 'Penarikan Tabungan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Penarikan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Form Filter Kelas -->
                        <form action="{{ route('transaksi.penarikan.create') }}" method="GET">
                            <div class="mb-3">
                                <label for="kelas_id" class="form-label">Filter Kelas</label>
                                <select name="kelas_id" id="kelas_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Kelas</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        <!-- Form Penarikan -->
                        <form action="{{ route('transaksi.penarikan.store') }}" method="POST" id="formPenarikan">
                            @csrf
                            <div class="mb-3">
                                <label for="buku_tabungan_id" class="form-label">Buku Tabungan</label>
                                <select name="buku_tabungan_id" id="buku_tabungan_id" class="form-select" required>
                                    <option value="">Pilih Buku Tabungan</option>
                                    @foreach ($bukuTabungans as $buku)
                                        <option value="{{ $buku->id }}" 
                                            data-simpanan="{{ $buku->totalSimpanan - $buku->totalPenarikanSimpanan }}"
                                            data-cicilan="{{ $buku->totalCicilan - $buku->totalPenarikanCicilan }}">
                                            {{ $buku->nomor_urut }} - {{ $buku->siswa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah Penarikan (Rp)</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" step="0.01" required>
                                <div class="invalid-feedback" id="saldoError"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sumber Penarikan</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sumber_penarikan" 
                                        id="simpanan" value="simpanan" required>
                                    <label class="form-check-label" for="simpanan">Simpanan</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sumber_penarikan" 
                                        id="cicilan" value="cicilan">
                                    <label class="form-check-label" for="cicilan">Cicilan</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-coins me-2"></i> Proses Penarikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPenarikan');
    const bukuTabunganSelect = document.getElementById('buku_tabungan_id');
    const jumlahInput = document.getElementById('jumlah');
    const sumberInputs = document.getElementsByName('sumber_penarikan');
    const saldoError = document.getElementById('saldoError');

    function checkBalance() {
        const selectedOption = bukuTabunganSelect.options[bukuTabunganSelect.selectedIndex];
        const saldoSimpanan = parseFloat(selectedOption.dataset.simpanan) || 0;
        const saldoCicilan = parseFloat(selectedOption.dataset.cicilan) || 0;
        const jumlah = parseFloat(jumlahInput.value) || 0;
        let sumberPenarikan = '';

        sumberInputs.forEach(input => {
            if (input.checked) {
                sumberPenarikan = input.value;
            }
        });

        const saldo = sumberPenarikan === 'simpanan' ? saldoSimpanan : saldoCicilan;
        
        if (jumlah > saldo) {
            jumlahInput.classList.add('is-invalid');
            saldoError.textContent = `Saldo ${sumberPenarikan} tidak mencukupi. Saldo tersedia: Rp ${new Intl.NumberFormat('id-ID').format(saldo)}`;
            return false;
        }

        jumlahInput.classList.remove('is-invalid');
        saldoError.textContent = '';
        return true;
    }

    // Check balance when amount or withdrawal source changes
    jumlahInput.addEventListener('input', checkBalance);
    sumberInputs.forEach(input => {
        input.addEventListener('change', checkBalance);
    });
    bukuTabunganSelect.addEventListener('change', checkBalance);

    // Prevent form submission if balance is insufficient
    form.addEventListener('submit', function(e) {
        if (!checkBalance()) {
            e.preventDefault();
        }
    });
});
</script>
@endpush