@extends('layouts.layout')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
                
                </div>
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0">Daftar Transaksi</h5>
                        <div class="ms-auto">
                            <a href="{{ request()->fullUrlWithQuery(['jenis' => 'simpanan']) }}" 
                               class="btn btn-sm btn-success me-2 {{ request()->input('jenis') === 'simpanan' ? 'active' : '' }}">
                                <i class="mdi mdi-cash me-1"></i> Simpanan
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['jenis' => 'cicilan']) }}" 
                               class="btn btn-sm btn-info me-2 {{ request()->input('jenis') === 'cicilan' ? 'active' : '' }}">
                                <i class="mdi mdi-cash-multiple me-1"></i> Cicilan
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['jenis' => 'penarikan']) }}" 
                               class="btn btn-sm btn-warning me-2 {{ request()->input('jenis') === 'penarikan' ? 'active' : '' }}">
                                <i class="mdi mdi-cash-remove me-1"></i> Penarikan
                            </a>
                            <a href="{{ route('transaksi.create') }}" class="btn btn-sm btn-primary me-2">
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
                                    <th>ID</th>
                                    <th>Buku Tabungan</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksis as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->id }}</td>
                                    <td>{{ $transaksi->bukuTabungan->nomor_urut }}</td>
                                    <td>{{ $transaksi->bukuTabungan->siswa->name }}</td>
                                    <td>{{ $transaksi->bukuTabungan->siswa->kelas->name }}</td>
                                    <td>{{ ucfirst($transaksi->jenis) }}</td>
                                    <td>{{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
                                    <td>{{ $transaksi->tanggal->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $transaksi->keterangan }}</td>
                                    <td>
                                        <a href="{{ route('transaksi.edit', $transaksi->id) }}" 
                                           class="btn btn-icon btn-sm bg-primary-subtle me-1" 
                                           data-bs-toggle="tooltip" title="Edit">
                                            <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                        </a>
                                        <form action="{{ route('transaksi.destroy', $transaksi->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-icon btn-sm bg-danger-subtle" 
                                                    data-bs-toggle="tooltip" title="Delete">
                                                <i class="mdi mdi-delete fs-14 text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data transaksi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer py-0 border-top">
                    <div class="row align-items-center">
                        <div class="col-sm">
                            <div class="text-block text-center text-sm-start">
                                @if ($transaksis->count() > 0)
                                <span class="fw-medium">
                                    Showing {{ $transaksis->count() }} of {{ $transaksis->total() }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-auto mt-3 mt-sm-0">
                            {{ $transaksis->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
