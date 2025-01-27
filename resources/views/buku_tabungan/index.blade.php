@extends('layouts.layout')

@section('title', 'Daftar Buku Tabungan')

@section('content')
<div class="container-fluid">
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0">Daftar Buku Tabungan</h5>
                        <a href="{{ route('buku-tabungan.create') }}" class="btn btn-sm btn-success ms-auto">
                            <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Buku
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Nomor Buku</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bukuTabungans as $index => $bukuTabungan)
                                    <tr>
                                        <td>{{ $index + 1 + ($bukuTabungans->currentPage() - 1) * $bukuTabungans->perPage() }}</td>
                                        <td>{{ $bukuTabungan->siswa->name }}</td>
                                        <td>{{ $bukuTabungan->kelas->name ?? '-' }}</td>
                                        <td>
                                            {{ $bukuTabungan->tahunAjaran->year_name }}
                                            @if($bukuTabungan->tahunAjaran->is_active)
                                                <span class="badge bg-success-subtle text-success">Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ str_pad($bukuTabungan->nomor_urut, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('buku-tabungan.edit', $bukuTabungan->id) }}" 
                                               class="btn btn-icon btn-sm bg-warning-subtle me-1" 
                                               data-bs-toggle="tooltip" title="Edit">
                                                <i class="mdi mdi-pencil-outline fs-14 text-warning"></i>
                                            </a>
                                            <form action="{{ route('buku-tabungan.destroy', $bukuTabungan->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Hapus buku tabungan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-icon btn-sm bg-danger-subtle" 
                                                        data-bs-toggle="tooltip" 
                                                        title="Hapus">
                                                    <i class="mdi mdi-delete fs-14 text-danger"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data buku tabungan</td>
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
                                @if ($bukuTabungans->total() > 0)
                                <span class="fw-medium">
                                    Menampilkan {{ $bukuTabungans->firstItem() }} - {{ $bukuTabungans->lastItem() }} dari {{ $bukuTabungans->total() }} data
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-auto mt-3 mt-sm-0">
                            {{ $bukuTabungans->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection