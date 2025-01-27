@extends('layouts.layout')

@section('title', 'Daftar Biaya Sekolah')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Daftar Biaya Sekolah</h5>
                <a href="{{ route('biaya-sekolah.create') }}" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-plus me-1"></i> Tambah Biaya
                </a>
            </div>
            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Tahun Ajaran</th>
                                    <th>Jenis Biaya</th>
                                    <th>Kategori Siswa</th>
                                    <th>Tingkat</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($biayas as $biaya)
                                <tr>
                                    <td>{{ $biaya->tahunAjaran->year_name }}</td>
                                    <td>{{ $biaya->jenis_biaya }}</td>
                                    <td>{{ $biaya->kategori_siswa ?? '-' }}</td>
                                    <td>{{ $biaya->tingkat ?? '-' }}</td>
                                    <td>Rp {{ number_format($biaya->jumlah, 0, ',', '.') }}</td>
                                    <td>{{ $biaya->keterangan ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('biaya-sekolah.edit', $biaya->id) }}" 
                                           class="btn btn-icon btn-sm bg-primary-subtle me-1" 
                                           data-bs-toggle="tooltip" title="Edit">
                                            <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                        </a>
                                        <form action="{{ route('biaya-sekolah.destroy', $biaya->id) }}" method="POST" class="d-inline">
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
                                    <td colspan="7" class="text-center">Tidak ada data biaya sekolah.</td>
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
                                @if ($biayas->count() > 0)
                                <span class="fw-medium">
                                    Menampilkan {{ $biayas->count() }} dari {{ $biayas->total() }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-auto mt-3 mt-sm-0">
                            {{ $biayas->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection