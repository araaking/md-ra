@extends('layouts.layout')

@section('title', 'Daftar Kelas')

@section('content')
<div class="container-fluid">
    <div class="card overflow-hidden mt-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h5 class="card-title mb-0">Daftar Kelas</h5>
                <a href="{{ route('kelas.create') }}" class="btn btn-success ms-auto">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </a>
            </div>
        </div>
        <div class="card-body mt-0">
            <div class="table-responsive table-card mt-0">
                <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Tingkat</th>
                            <th>Kelas Berikutnya</th>
                            <th>Total Murid</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kelas as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->tingkat }}</td>
                                <td>
                                    @if($item->nextClass)
                                        {{ $item->nextClass->name }} (Tingkat {{ $item->nextClass->tingkat }})
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->siswa->count() }}</td>
                                <td>
                                    <a href="{{ route('kelas.edit', $item->id) }}" 
                                       class="btn btn-sm btn-warning me-1"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('kelas.destroy', $item->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus kelas ini akan menghapus semua siswa terkait. Lanjutkan?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3">Tidak ada data kelas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection