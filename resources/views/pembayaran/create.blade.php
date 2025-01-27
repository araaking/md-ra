@extends('layouts.layout')

@section('title', 'Tambah Pembayaran Biaya Sekolah')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tambah Pembayaran Biaya Sekolah</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Form Filter Kelas -->
                        <form action="{{ route('pembayaran.create') }}" method="GET">
                            <div class="mb-3">
                                <label for="kelas_id" class="form-label">Filter Kelas</label>
                                <select name="kelas_id" id="kelas_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->name }} (Tingkat {{ $kelas->tingkat }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        <!-- Form Pembayaran -->
                        <form action="{{ route('pembayaran.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="siswa_id" class="form-label">Siswa</label>
                                <select name="siswa_id" id="siswa_id" class="form-select" required>
                                    <option value="">Pilih Siswa</option>
                                    @foreach ($siswas as $siswa)
                                        <option value="{{ $siswa->id }}">
                                            {{ $siswa->name }} - {{ $siswa->category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="jenis_biaya" class="form-label">Jenis Biaya</label>
                                <select name="jenis_biaya" id="jenis_biaya" class="form-select" required>
                                    @foreach (\App\Models\Pembayaran::JENIS_BIAYA as $jenis)
                                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Bulan Hijriah (Hanya untuk SPP) -->
                            <div class="mb-3" id="bulan_hijri_container" style="display: none;">
                                <label for="bulan_hijri" class="form-label">Bulan Hijriah</label>
                                <select name="bulan_hijri" id="bulan_hijri" class="form-select">
                                    <option value="">Pilih Bulan</option>
                                    @foreach (\App\Models\Pembayaran::BULAN_HIJRI as $bulan)
                                        <option value="{{ $bulan }}">{{ $bulan }}</option>
                                    @endforeach
                                </select>
                            </div>
                    </div>

                    <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                                <input type="number" name="jumlah" class="form-control" step="0.01" placeholder="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                                    <option value="cash">Cash</option>
                                    <option value="cicilan">Cicilan</option>
                                    <option value="tabungan">Tabungan</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Simpan Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisBiaya = document.getElementById('jenis_biaya');
    const bulanContainer = document.getElementById('bulan_hijri_container');
    const bulanSelect = document.getElementById('bulan_hijri');

    function toggleBulanField() {
        const isSPP = jenisBiaya.value === 'SPP';
        bulanContainer.style.display = isSPP ? 'block' : 'none';
        bulanSelect.required = isSPP;
        if (!isSPP) bulanSelect.value = '';
    }

    // Initial check
    toggleBulanField();
    
    // Event listener
    jenisBiaya.addEventListener('change', toggleBulanField);
});
</script>
@endsection