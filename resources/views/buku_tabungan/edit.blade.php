@extends('layouts.layout')

@section('title', 'Edit Buku Tabungan')

@section('content')
<div class="container-fluid">
    <div class="card mt-4">
        <div class="card-header">
            <h4 class="card-title">Edit Buku Tabungan</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('buku-tabungan.update', $bukuTabungan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Pilih Siswa -->
                <div class="mb-3">
                    <label for="siswa_id" class="form-label">Siswa <span class="text-danger">*</span></label>
                    <select id="siswa_id" name="siswa_id" class="form-select @error('siswa_id') is-invalid @enderror" required>
                        <option value="">- Pilih Siswa -</option>
                        @foreach($siswas as $siswa)
                            <option value="{{ $siswa->id }}" 
                                {{ $bukuTabungan->siswa_id == $siswa->id ? 'selected' : '' }}
                                {{ old('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                {{ $siswa->name }} (Kelas: {{ $siswa->kelas->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('siswa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tahun Ajaran -->
                <div class="mb-3">
                    <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                    <select id="tahun_ajaran_id" name="tahun_ajaran_id" class="form-select @error('tahun_ajaran_id') is-invalid @enderror" required>
                        <option value="">- Pilih Tahun Ajaran -</option>
                        @foreach($tahunAjarans as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}" 
                                {{ $bukuTabungan->tahun_ajaran_id == $tahunAjaran->id ? 'selected' : '' }}
                                {{ old('tahun_ajaran_id') == $tahunAjaran->id ? 'selected' : '' }}>
                                {{ $tahunAjaran->year_name }} 
                                @if($tahunAjaran->is_active) <small>(Aktif)</small> @endif
                            </option>
                        @endforeach
                    </select>
                    @error('tahun_ajaran_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Info Kelas (Auto) -->
                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <input type="text" class="form-control" 
                        value="{{ $bukuTabungan->kelas->name ?? '-' }}" 
                        id="kelas-display"
                        readonly>
                </div>

                <!-- Nomor Urut -->
                <div class="mb-3">
                    <label for="nomor_urut" class="form-label">Nomor Urut <span class="text-danger">*</span></label>
                    <input type="number" id="nomor_urut" name="nomor_urut" 
                        class="form-control @error('nomor_urut') is-invalid @enderror" 
                        value="{{ old('nomor_urut', $bukuTabungan->nomor_urut) }}" 
                        required
                        placeholder="Contoh: 001">
                    @error('nomor_urut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Harus unik dalam satu kelas dan tahun ajaran</small>
                </div>

                <!-- Tombol -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('buku-tabungan.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-update info kelas saat siswa dipilih
    document.getElementById('siswa_id').addEventListener('change', function() {
        const siswaId = this.value;
        if (siswaId) {
            fetch(`/siswa/${siswaId}/kelas`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('kelas-display').value = data.kelas_name || '-';
                });
        }
    });

    // Inisialisasi kelas saat pertama kali load
    window.addEventListener('DOMContentLoaded', (event) => {
        const initialSiswaId = document.getElementById('siswa_id').value;
        if (initialSiswaId) {
            fetch(`/siswa/${initialSiswaId}/kelas`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('kelas-display').value = data.kelas_name || '-';
                });
        }
    });
</script>
@endpush