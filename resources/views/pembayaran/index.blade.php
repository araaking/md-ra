@extends('layouts.layout')

@section('title', 'Daftar Pembayaran Biaya Sekolah')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Daftar Pembayaran Biaya Sekolah</h5>
                <a href="{{ route('pembayaran.create') }}" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-plus me-1"></i> Tambah Pembayaran
                </a>
            </div>
            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Biaya</th>
                                    <th>Bulan</th>
                                    <th>Jumlah (Rp)</th>
                                    <th>Metode</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                    <th>exportPDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pembayarans as $pembayaran)
                                <tr>
                                    <td>{{ $pembayaran->id }}</td>
                                    <td>{{ $pembayaran->siswa->name }}</td>
                                    <td>{{ $pembayaran->siswa->kelas->name }}</td>
                                    <td>{{ $pembayaran->getLabelJenisBiaya() }}</td>
                                    <td>{{ $pembayaran->bulan_hijri ?? '-' }}</td>
                                    <td>{{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($pembayaran->metode_pembayaran) }}</td>
                                    <td>{{ $pembayaran->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $pembayaran->keterangan }}</td>
                                    <td>
                                        <span class="badge {{ $pembayaran->is_processed ? 'bg-success' : 'bg-warning' }}">
                                            {{ $pembayaran->is_processed ? 'Diproses' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('pembayaran.edit', $pembayaran->id) }}" 
                                           class="btn btn-icon btn-sm bg-primary-subtle me-1" 
                                           data-bs-toggle="tooltip" title="Edit">
                                            <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                        </a>
                                        <form action="{{ route('pembayaran.destroy', $pembayaran->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-icon btn-sm bg-danger-subtle" 
                                                    data-bs-toggle="tooltip" title="Delete">
                                                <i class="mdi mdi-delete fs-14 text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="{{ route('pembayaran.export-pdf', $pembayaran->id) }}" 
                                           class="btn btn-icon btn-sm bg-primary-subtle me-1" 
                                           data-bs-toggle="tooltip" title="Export PDF">
                                            <i class="mdi mdi-file-pdf fs-14 text-primary"></i>
                                        </a>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">Tidak ada data pembayaran.</td>
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
                                @if ($pembayarans->count() > 0)
                                <span class="fw-medium">
                                    Showing {{ $pembayarans->count() }} of {{ $pembayarans->total() }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-auto mt-3 mt-sm-0">
                            {{ $pembayarans->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection