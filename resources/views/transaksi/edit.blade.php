@extends('layouts.layout')

@section('title', 'Edit Transaksi')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Transaksi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('transaksi.update', $transaksi->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="buku_tabungan_id" class="form-label">Buku Tabungan</label>
                            <select class="form-select @error('buku_tabungan_id') is-invalid @enderror" 
                                    name="buku_tabungan_id" 
                                    id="buku_tabungan_id" 
                                    readonly disabled>
                                <option value="{{ $transaksi->buku_tabungan_id }}">
                                    {{ $transaksi->bukuTabungan->nomor_urut }} - {{ $transaksi->bukuTabungan->siswa->name }}
                                </option>
                            </select>
                            <input type="hidden" name="buku_tabungan_id" value="{{ $transaksi->buku_tabungan_id }}">
                        </div>

                        <div class="mb-3">
                            <label for="jenis" class="form-label">Jenis Transaksi</label>
                            <input type="text" class="form-control" value="{{ ucfirst($transaksi->jenis) }}" readonly disabled>
                            <input type="hidden" name="jenis" value="{{ $transaksi->jenis }}">
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       class="form-control @error('jumlah') is-invalid @enderror" 
                                       name="jumlah" 
                                       id="jumlah" 
                                       value="{{ $transaksi->jumlah }}" 
                                       required>
                            </div>
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="datetime-local" 
                                   class="form-control @error('tanggal') is-invalid @enderror" 
                                   name="tanggal" 
                                   id="tanggal" 
                                   value="{{ $transaksi->tanggal->format('Y-m-d\TH:i') }}" 
                                   required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      name="keterangan" 
                                      id="keterangan" 
                                      rows="3">{{ $transaksi->keterangan }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save me-1"></i> Simpan
                            </button>
                            <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
