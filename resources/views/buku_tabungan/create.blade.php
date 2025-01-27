@extends('layouts.layout')

@section('title', 'Tambah Buku Tabungan')

@section('content')
<div class="container-fluid">
    <div class="card mt-4">
        <div class="card-header">
            <h4 class="card-title">Tambah Buku Tabungan</h4>
        </div>
        <div class="card-body">
            <!-- Form Filter Kelas -->
            <form action="{{ route('buku-tabungan.create') }}" method="GET">
                <div class="mb-3">
                    <label for="kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                    <select id="kelas_id" name="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" required>
                        <option value="">- Pilih Kelas -</option>
                        @foreach($kelasList as $kelas)
                            <option 
                                value="{{ $kelas->id }}" 
                                {{ old('kelas_id', $selectedKelasId) == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->name }} (Tingkat {{ $kelas->tingkat }})
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-info mb-4">Filter Siswa</button>
            </form>

            <!-- Form Utama (Muncul setelah kelas dipilih) -->
            @if($selectedKelasId)
                <form action="{{ route('buku-tabungan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">

                    <!-- Pilih Siswa -->
                    <div class="mb-3">
                        <label for="siswa_id" class="form-label">Siswa <span class="text-danger">*</span></label>
                        <select id="siswa_id" name="siswa_id" 
                            class="form-select @error('siswa_id') is-invalid @enderror" 
                            required>
                            <option value="">- Pilih Siswa -</option>
                            @foreach($siswas as $siswa)
                                <option 
                                    value="{{ $siswa->id }}" 
                                    {{ old('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                    {{ $siswa->name }} 
                                    @if($siswa->status != 'Aktif')
                                        ({{ $siswa->status }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('siswa_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($siswas->isEmpty())
                            <small class="text-danger">Tidak ada siswa yang tersedia di kelas ini.</small>
                        @endif
                    </div>

                    <!-- Tahun Ajaran (Auto) -->
                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <input type="text" class="form-control" 
                            value="{{ $tahunAktif->year_name }}" 
                            readonly>
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAktif->id }}">
                    </div>

                    <!-- Nomor Urut -->
                    <div class="mb-3">
                        <label for="nomor_urut" class="form-label">Nomor Buku <span class="text-danger">*</span></label>
                        <input type="number" id="nomor_urut" name="nomor_urut" 
                            class="form-control @error('nomor_urut') is-invalid @enderror" 
                            value="{{ old('nomor_urut') }}" 
                            required
                            placeholder="Contoh: 1"
                            min="1">
                        @error('nomor_urut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Harus unik untuk kelas {{ \App\Models\Kelas::find($selectedKelasId)->name }} 
                            dan tahun ajaran {{ $tahunAktif->year_name }}
                        </small>
                    </div>

                    <!-- Tombol -->
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Simpan
                        </button>
                        <a href="{{ route('buku-tabungan.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection