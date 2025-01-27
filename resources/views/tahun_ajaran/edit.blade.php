@extends('layouts.layout')

@section('title', 'Edit Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tahun-ajaran.index') }}">Tahun Ajaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Tahun Ajaran</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Main Card -->
            <div class="card">
                <!-- Card Header -->
                <div class="card-header">
                    <h4 class="card-title">Edit Tahun Ajaran</h4>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('tahun-ajaran.update', $tahunAjaran->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Error Messages -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Nama Tahun Ajaran -->
                        <div class="mb-3">
                            <label for="year_name" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="year_name" 
                                   id="year_name" 
                                   class="form-control @error('year_name') is-invalid @enderror" 
                                   placeholder="Contoh: 2023/2024"
                                   value="{{ old('year_name', $tahunAjaran->year_name) }}"
                                   required
                                   pattern="\d{4}/\d{4}"
                                   title="Format harus YYYY/YYYY"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\d{4})(\d{4})/, '$1/$2')"
                                   maxlength="9">
                            @error('year_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Aktif -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" value="1"
                                       {{ $tahunAjaran->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Tahun Ajaran Aktif
                                    <small class="text-muted d-block">
                                        (Centang untuk mengaktifkan tahun ini. 
                                        <span class="text-danger">Jika diaktifkan:</span>
                                        <ul class="mt-1">
                                            <li>Siswa Aktif akan naik kelas sesuai tingkat</li>
                                            <li>Siswa di kelas tertinggi (misal: 6) akan diubah statusnya menjadi Lulus</li>
                                        </ul>
                                    </small>
                                </label>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="{{ route('tahun-ajaran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection