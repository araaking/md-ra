@extends('layouts.layout')

@section('title', 'Tambah Siswa')

@section('content')
<div class="container-fluid">
    <!-- Header & Breadcrumb -->
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Tambah Siswa Baru</h4>
        </div>
        <div class="text-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('siswa.index') }}">Siswa</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Form Tambah Siswa -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Formulir Pendaftaran Siswa</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('siswa.store') }}" method="POST">
                        @csrf

                        <!-- Error Messages -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <strong>Terjadi Kesalahan!</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Nama Siswa -->
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Contoh: Budi Santoso"
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- NIS -->
                        <div class="mb-3">
                            <label class="form-label">Nomor Induk Siswa (NIS)</label>
                            <input type="text" name="nis" 
                                   class="form-control @error('nis') is-invalid @enderror" 
                                   placeholder="Opsional"
                                   value="{{ old('nis') }}"
                                   maxlength="20">
                            @error('nis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kelas & Tahun Ajaran -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelas->sortBy('tingkat') as $item)
                                        <option value="{{ $item->id }}" {{ old('class_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} (Tingkat {{ $item->tingkat }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">Pilih Tahun Ajaran</option>
                                    @foreach ($tahunAjaran->sortByDesc('is_active') as $tahun)
                                        <option value="{{ $tahun->id }}" {{ old('academic_year_id') == $tahun->id ? 'selected' : '' }}>
                                            {{ $tahun->year_name }} @if($tahun->is_active) <span class="text-success">(Aktif)</span> @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status & Kategori -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Lulus" {{ old('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                    <option value="Keluar" {{ old('status') == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="Anak Normal" {{ old('category') == 'Anak Normal' ? 'selected' : '' }}>Anak Normal</option>
                                    <option value="Anak Guru" {{ old('category') == 'Anak Guru' ? 'selected' : '' }}>Anak Guru</option>
                                    <option value="Anak Yatim" {{ old('category') == 'Anak Yatim' ? 'selected' : '' }}>Anak Yatim</option>
                                    <option value="Kakak Beradik" {{ old('category') == 'Kakak Beradik' ? 'selected' : '' }}>Kakak Beradik</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="mb-4">
                            <label class="form-label">Catatan Khusus</label>
                            <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                      rows="3" placeholder="Contoh: Alergi makanan tertentu">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Simpan
                            </button>
                            <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-times me-2"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection