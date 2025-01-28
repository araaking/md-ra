@extends('layouts.layout')

@section('title', 'Tambah Biaya Sekolah')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tambah Biaya Sekolah</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('biaya-sekolah.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="jenis_biaya" class="form-label">Jenis Biaya</label>
                                <select name="jenis_biaya" id="jenis_biaya" class="form-select" required>
                                    <option value="">Pilih Jenis Biaya</option>
                                    <option value="SPP">SPP</option>
                                    <option value="IKK">IKK</option>
                                    <option value="THB">THB</option>
                                    <option value="UAM">UAM</option>
                                    <option value="Wisuda">Wisuda</option>
                                    <option value="Uang Pangkal">Uang Pangkal</option>
                                    <option value="Raport">Raport</option>
                                    <option value="Seragam">Seragam</option>
                                    <option value="Foto">Foto</option>
                                </select>
                            </div>

                            <div class="mb-3" id="kategoriField" style="display: none;">
                                <label for="kategori_siswa" class="form-label">Kategori Siswa</label>
                                <select name="kategori_siswa" id="kategori_siswa" class="form-select">
                                    <option value="">Pilih Kategori</option>
                                    <option value="Anak Guru">Anak Guru</option>
                                    <option value="Anak Yatim">Anak Yatim</option>
                                    <option value="Kakak Beradik">Kakak Beradik</option>
                                    <option value="Anak Normal">Anak Normal</option>
                                </select>
                            </div>

                            <div class="mb-3" id="tingkatField" style="display: none;">
                                <label for="tingkat" class="form-label">Tingkat Kelas</label>
                                <select name="tingkat" id="tingkat" class="form-select">
                                    <option value="">Pilih Tingkat</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <!-- Remove the duplicate tingkatField div here -->

                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                                <input type="number" name="jumlah" class="form-control" step="0.01" required>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Simpan Biaya
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('jenis_biaya').addEventListener('change', function() {
    const jenis = this.value;
    const kategoriField = document.getElementById('kategoriField');
    const tingkatField = document.getElementById('tingkatField');
    const tingkatSelect = document.getElementById('tingkat');
    
    // Reset fields
    kategoriField.style.display = 'none';
    tingkatField.style.display = 'none';
    
    // Show category field for SPP, IKK, and Uang Pangkal
    if (['SPP', 'IKK', 'Uang Pangkal'].includes(jenis)) {
        kategoriField.style.display = 'block';
    }
    
    // Handle class levels for specific fee types
    if (['THB', 'UAM', 'Foto'].includes(jenis)) {
        tingkatField.style.display = 'block';
        tingkatSelect.innerHTML = '<option value="">Pilih Tingkat</option>';
        
        // Set available classes based on fee type
        let classes = [];
        if (jenis === 'THB') {
            classes = [
                {value: '2', label: 'Kelas 1'},
                {value: '3', label: 'Kelas 2'},
                {value: '4', label: 'Kelas 3'},
                {value: '5', label: 'Kelas 4'},
                {value: '6', label: 'Kelas 5'},
                {value: '7', label: 'Kelas 6'}
            ];
        } else if (jenis === 'UAM') {
            classes = [{value: '7', label: 'Kelas 6'}];
        } else if (jenis === 'Foto') {
            classes = [
                {value: '1', label: 'TK'},
                {value: '7', label: 'Kelas 6'}
            ];
        }
        
        classes.forEach(kelas => {
            tingkatSelect.add(new Option(kelas.label, kelas.value));
        });
    }
});
</script>
@endsection