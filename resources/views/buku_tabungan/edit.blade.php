@extends('layouts.layout')

@section('title', 'Edit Buku Tabungan')

@section('content')
<div class="container-fluid">
    <div class="card mt-4">
        <div class="card-header">
            <h4 class="card-title">Edit Buku Tabungan</h4>
        </div>
        <div class="card-body">
            <!-- Fix: Ensure correct form method and route -->
            <form action="{{ route('buku-tabungan.update', $bukuTabungan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Nomor Urut -->
                <div class="mb-3">
                    <label for="nomor_urut" class="form-label">Nomor Buku <span class="text-danger">*</span></label>
                    <input type="number" id="nomor_urut" name="nomor_urut" 
                        class="form-control @error('nomor_urut') is-invalid @enderror" 
                        value="{{ old('nomor_urut', $bukuTabungan->nomor_urut) }}" 
                        required
                        min="1">
                    @error('nomor_urut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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