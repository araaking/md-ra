@extends('layouts.layout')

@section('title', 'Penarikan Tabungan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Penarikan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Form Filter Kelas -->
                        <form action="{{ route('transaksi.penarikan.create') }}" method="GET">
                            <div class="mb-3">
                                <label for="kelas_id" class="form-label">Filter Kelas</label>
                                <select name="kelas_id" id="kelas_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Kelas</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        <!-- Form Penarikan -->
                        <form action="{{ route('transaksi.penarikan.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="buku_tabungan_id" class="form-label">Buku Tabungan</label>
                                <select name="buku_tabungan_id" id="buku_tabungan_id" class="form-select" required>
                                    <option value="">Pilih Buku Tabungan</option>
                                    @foreach ($bukuTabungans as $buku)
                                        <option value="{{ $buku->id }}">
                                            {{ $buku->nomor_urut }} - {{ $buku->siswa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah Penarikan (Rp)</label>
                                <input type="number" name="jumlah" class="form-control" step="0.01" required>
                            </div>
                    </div>

                    <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Sumber Penarikan</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sumber_penarikan" 
                                        id="simpanan" value="simpanan" required>
                                    <label class="form-check-label" for="simpanan">Simpanan</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sumber_penarikan" 
                                        id="cicilan" value="cicilan">
                                    <label class="form-check-label" for="cicilan">Cicilan</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-coins me-2"></i> Proses Penarikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection