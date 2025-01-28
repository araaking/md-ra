@extends('layouts.layout')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Summary Cards -->
            <div class="row mb-4">
                <!-- Total Simpanan Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="widget-first">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="p-2 border border-success border-opacity-10 bg-success-subtle rounded-2 me-2">
                                        <div class="bg-success rounded-circle widget-size text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                <path fill="#ffffff" d="M5 6h14v2H5V6m0 4h14v2H5v-2m0 4h14v2H5v-2m0 4h14v2H5v-2Z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="mb-0 text-dark fs-15">Total Simpanan</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    @php
                                        $totalPenarikanSimpanan = App\Models\Transaksi::whereHas('bukuTabungan', function($query) use ($tahunAjaran) {
                                            $query->where('tahun_ajaran_id', $tahunAjaran->id);
                                        })
                                        ->where('jenis', 'penarikan')
                                        ->where('sumber_penarikan', 'simpanan')
                                        ->sum('jumlah');
                                        
                                        $saldoSimpanan = $totalSimpanan - $totalPenarikanSimpanan;
                                    @endphp
                                    <h3 class="mb-0 fs-22 text-dark me-3">Rp {{ number_format($saldoSimpanan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Cicilan Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="widget-first">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="p-2 border border-info border-opacity-10 bg-info-subtle rounded-2 me-2">
                                        <div class="bg-info rounded-circle widget-size text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                <path fill="#ffffff" d="M3 6h18v12H3V6m9 3a3 3 0 0 1 3 3a3 3 0 0 1-3 3a3 3 0 0 1-3-3a3 3 0 0 1 3-3M7 8a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2v-4a2 2 0 0 1-2-2H7Z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="mb-0 text-dark fs-15">Total Cicilan</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    @php
                                        $totalPenarikanCicilan = App\Models\Transaksi::whereHas('bukuTabungan', function($query) use ($tahunAjaran) {
                                            $query->where('tahun_ajaran_id', $tahunAjaran->id);
                                        })
                                        ->where('jenis', 'penarikan')
                                        ->where('sumber_penarikan', 'cicilan')
                                        ->sum('jumlah');
                                        
                                        $saldoCicilan = $totalCicilan - $totalPenarikanCicilan;
                                    @endphp
                                    <h3 class="mb-0 fs-22 text-dark me-3">Rp {{ number_format($saldoCicilan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Penarikan Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="widget-first">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="p-2 border border-warning border-opacity-10 bg-warning-subtle rounded-2 me-2">
                                        <div class="bg-warning rounded-circle widget-size text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                <path fill="#ffffff" d="M11 8c0 2.21-1.79 4-4 4s-4-1.79-4-4s1.79-4 4-4s4 1.79 4 4m0 6.72V20H0v-2c0-2.21 3.13-4 7-4c1.5 0 2.87.27 4 .72M24 20H13V3h11v17m-8-8.5a2.5 2.5 0 0 1 5 0a2.5 2.5 0 0 1-5 0M22 7a2 2 0 0 1-2-2h-3c0 1.11-.89 2-2 2v9a2 2 0 0 1 2 2h3c0-1.1.9-2 2-2V7Z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="mb-0 text-dark fs-15">Total Penarikan</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="mb-0 fs-22 text-dark me-3">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction List Card -->
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="d-flex flex-column flex-md-row align-items-md-center">
                        <h5 class="card-title mb-3 mb-md-0">Daftar Transaksi</h5>
                        <div class="ms-md-auto d-flex flex-wrap gap-2 align-items-center">
                            <!-- Academic Year Filter -->
                            <form action="{{ route('transaksi.index') }}" method="GET" class="d-flex align-items-center me-2">
                                <select name="tahun_ajaran_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach ($allTahunAjaran as $tahun)
                                        <option value="{{ $tahun->id }}" {{ $selectedTahun->id == $tahun->id ? 'selected' : '' }}>
                                            {{ $tahun->year_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>

                            <a href="{{ request()->fullUrlWithQuery(['jenis' => 'simpanan']) }}" 
                               class="btn btn-sm btn-success {{ request()->input('jenis') === 'simpanan' ? 'active' : '' }}">
                                <i class="mdi mdi-cash me-1"></i> Simpanan
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['jenis' => 'cicilan']) }}" 
                               class="btn btn-sm btn-info {{ request()->input('jenis') === 'cicilan' ? 'active' : '' }}">
                                <i class="mdi mdi-cash-multiple me-1"></i> Cicilan
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['jenis' => 'penarikan']) }}" 
                               class="btn btn-sm btn-warning {{ request()->input('jenis') === 'penarikan' ? 'active' : '' }}">
                                <i class="mdi mdi-cash-remove me-1"></i> Penarikan
                            </a>
                            <a href="{{ route('transaksi.create') }}" class="btn btn-sm btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Tambah Transaksi
                            </a>
                            <a href="{{ route('transaksi.penarikan.create') }}" class="btn btn-sm btn-danger">
                                <i class="mdi mdi-cash-remove me-1"></i> Tambah Penarikan
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Buku Tabungan</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis</th>
                                    <th>Sumber Penarikan</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksis as $index => $transaksi)
                                <tr>
                                    <td>{{ $transaksis->firstItem() + $index }}</td>
                                    <td>{{ $transaksi->bukuTabungan->nomor_urut }}</td>
                                    <td>{{ $transaksi->bukuTabungan->siswa->name }}</td>
                                    <td>{{ $transaksi->bukuTabungan->siswa->kelas->name }}</td>
                                    <td>{{ ucfirst($transaksi->jenis) }}</td>
                                    <td>
                                        @if($transaksi->jenis === 'penarikan')
                                            {{ ucfirst($transaksi->sumber_penarikan) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
                                    <td>{{ $transaksi->tanggal->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $transaksi->keterangan }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('transaksi.edit', $transaksi->id) }}" 
                                               class="btn btn-icon btn-sm bg-primary-subtle" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit">
                                                <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                            </a>
                                            <form action="{{ route('transaksi.destroy', $transaksi->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-icon btn-sm bg-danger-subtle" 
                                                        data-bs-toggle="tooltip" 
                                                        title="Delete">
                                                    <i class="mdi mdi-delete fs-14 text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data transaksi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer py-2">
                    <div class="row align-items-center">
                        <div class="col-sm">
                            @if ($transaksis->count() > 0)
                            <div class="text-muted">
                                Showing {{ $transaksis->count() }} of {{ $transaksis->total() }} entries
                            </div>
                            @endif
                        </div>
                        <div class="col-sm-auto">
                            {{ $transaksis->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
