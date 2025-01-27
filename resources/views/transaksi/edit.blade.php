@extends('layouts.layout')

@section('title', 'Edit Transaksi')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Edit Transaksi</h1>
    <form action="{{ route('transaksi.update', $transaksi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="buku_tabungan_id" class="form-label">Buku Tabungan</label>
            <select name="buku_tabungan_id" id="buku_tabungan_id" class="form-control" required>
                @foreach ($bukuTabungans as $bukuTabungan)
                    <option value="{{ $bukuTabungan->id }}" {{ $transaksi->buku_tabungan_id == $bukuTabungan->id ? 'selected' : '' }}>
                        {{ $bukuTabungan->nomor_urut }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="jenis" class="form-label">Jenis</label>
            <select name="jenis" id="jenis" class="form-control" required>
                <option value="simpanan" {{ $transaksi->jenis == 'simpanan' ? 'selected' : '' }}>Simpanan</option>
                <option value="penarikan" {{ $transaksi->jenis == 'penarikan' ? 'selected' : '' }}>Penarikan</option>
                <option value="cicilan" {{ $transaksi->jenis == 'cicilan' ? 'selected' : '' }}>Cicilan</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" name="jumlah" id="jumlah" class="form-control" step="0.01" value="{{ $transaksi->jumlah }}" required>
        </div>

        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ $transaksi->keterangan }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
