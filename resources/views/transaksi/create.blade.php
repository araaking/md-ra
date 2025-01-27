@extends('layouts.layout')

@section('title', 'Tambah Transaksi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tambah Simpanan/Cicilan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Form Filter Kelas -->
                        <form action="{{ route('transaksi.create') }}" method="GET">
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

                        <!-- Form Transaksi -->
                        <form action="{{ route('transaksi.store') }}" method="POST">
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
                                <label for="simpanan" class="form-label">Simpanan (Rp)</label>
                                <input type="number" name="simpanan" class="form-control" step="0.01" placeholder="0">
                            </div>
                    </div>

                    <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="cicilan" class="form-label">Cicilan (Rp)</label>
                                <input type="number" name="cicilan" class="form-control" step="0.01" placeholder="0">
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Simpan Transaksi
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