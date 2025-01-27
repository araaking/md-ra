@extends('layouts.layout')

@section('title', 'Dashboard')

@section('content')
<!-- Start Content -->
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <!-- Start Row -->
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <!-- Total Tabungan -->
            <div class="col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="widget-first">
                            <div class="d-flex align-items-center mb-1">
                                <span class="avatar-md rounded-circle bg-gray d-flex justify-content-center align-items-center me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 14 14">
                                        <path fill="#108dff" d="M13.463 9.692C13.463 12.664 10.77 14 7 14S.537 12.664.537 9.713c0-3.231 1.616-4.868 4.847-6.505L4.24 1.077A.7.7 0 0 1 4.843 0H9.41a.7.7 0 0 1 .603 1.023L8.616 3.208c3.23 1.615 4.847 3.252 4.847 6.484M7.625 4.887a.625.625 0 1 0-1.25 0v.627a1.74 1.74 0 0 0-.298 3.44l1.473.322a.625.625 0 0 1-.133 1.236h-.834a.625.625 0 0 1-.59-.416a.625.625 0 1 0-1.178.416a1.877 1.877 0 0 0 1.56 1.239v.636a.625.625 0 1 0 1.25 0v-.636a1.876 1.876 0 0 0 .192-3.696l-1.473-.322a.49.49 0 0 1 .105-.97h.968a.622.622 0 0 1 .59.416a.625.625 0 0 0 1.178-.417a1.874 1.874 0 0 0-1.56-1.238z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="mb-2 text-dark fs-15 fw-medium">Total Tabungan</p>
                                    <h3 class="mb-0 fs-22 text-dark me-3">{{ number_format($totalSimpanan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 justify-content-between">
                                <p class="mb-0 text-dark mt-1 fs-14 fw-medium">Tabungan 7 hari terakhir</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Cicilan -->
            <div class="col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="widget-first">
                            <div class="d-flex align-items-center mb-1">
                                <span class="avatar-md rounded-circle bg-gray d-flex justify-content-center align-items-center me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 640 512">
                                        <path fill="#287F71" d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64s-64 28.7-64 64s28.7 64 64 64m448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64s-64 28.7-64 64s28.7 64 64 64m32 32h-64c-17.6 0-33.5 7.1-45.1 18.6c40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64m-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32S208 82.1 208 144s50.1 112 112 112m76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="mb-2 text-dark fs-15 fw-medium">Total Cicilan</p>
                                    <h3 class="mb-0 fs-22 text-dark me-3">{{ number_format($totalCicilan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 justify-content-between">
                                <p class="mb-0 text-dark mt-1 fs-14 fw-medium">Cicilan 7 hari terakhir</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pendapatan -->
            <div class="col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="widget-first">
                            <div class="d-flex align-items-center mb-1">
                                <span class="avatar-md rounded-circle bg-gray d-flex justify-content-center align-items-center me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24">
                                        <path fill="#E77636" d="M7 20V8.975q0-.825.6-1.4T9.025 7H20q.825 0 1.413.587T22 9v8l-5 5H9q-.825 0-1.412-.587T7 20M2.025 6.25q-.15-.825.325-1.487t1.3-.813L14.5 2.025q.825-.15 1.488.325t.812 1.3L17.05 5H9Q7.35 5 6.175 6.175T5 9v9.55q-.4-.225-.687-.6t-.363-.85zM20 16h-4v4z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="mb-2 text-dark fs-15 fw-medium">Pendapatan</p>
                                    <h3 class="mb-0 fs-22 text-dark me-3">{{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 justify-content-between">
                                <p class="mb-0 text-dark mt-1 fs-14 fw-medium">Total pendapatan sekolah</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Siswa -->
            <div class="col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="widget-first">
                            <div class="d-flex align-items-center mb-1">
                                <span class="avatar-md rounded-circle bg-gray d-flex justify-content-center align-items-center me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24">
                                        <path fill="#963b68" d="M5.574 4.691c-.833.692-1.052 1.862-1.491 4.203l-.75 4c-.617 3.292-.926 4.938-.026 6.022C4.207 20 5.88 20 9.23 20h5.54c3.35 0 5.025 0 5.924-1.084c.9-1.084.591-2.73-.026-6.022l-.75-4c-.439-2.34-.658-3.511-1.491-4.203C17.593 4 16.403 4 14.02 4H9.98c-2.382 0-3.572 0-4.406.691"/>
                                        <path fill="#988D4D" d="M12 9.25a2.251 2.251 0 0 1-2.122-1.5a.75.75 0 1 0-1.414.5a3.751 3.751 0 0 0 7.073 0a.75.75 0 1 0-1.414-.5A2.251 2.251 0 0 1 12 9.25"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="mb-2 text-dark fs-15 fw-medium">Total Siswa</p>
                                    <h3 class="mb-0 fs-22 text-dark me-3">{{ $totalSiswa }} Siswa</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 justify-content-between">
                                <p class="mb-0 text-dark mt-1 fs-14 fw-medium">Siswa aktif saat ini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Row -->

    <!-- Data Table & Filter/Search -->
    <div class="row">
        <div class="col-md-12">
            <div class="card overflow-hidden">
                <!-- Bagian Card Header yang sebelumnya ketinggalan -->
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0">Data Tabungan</h5>
                        <!-- Form Filter dan Search -->
                        <form action="{{ route('dashboard') }}" method="GET" class="ms-auto d-flex align-items-center gap-2">
                            <!-- Filter Kelas -->
                            <select name="kelas" class="form-select form-select-sm">
                                <option value="">Show All Kelas</option>
                                <option value="3A" {{ request('kelas') == '3A' ? 'selected' : '' }}>3A</option>
                                <option value="4A" {{ request('kelas') == '4A' ? 'selected' : '' }}>4A</option>
                                <!-- Tambahkan opsi kelas lain sesuai kebutuhan -->
                            </select>
                            <!-- Search: Nama Siswa / Nomor Tabungan -->
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        </form>
                    </div>
                </div>
                <!-- End Card Header -->

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead>
                                <tr>
                                    <th>Nomor Tabungan</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Total Tabungan</th>
                                    <th>Total Cicilan</th>
                                    <th>Total Keseluruhan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksis as $transaksi)
                                <tr>
                                    <td>
                                        <a href="javascript:void(0);" class="text-dark">
                                            {{ $transaksi->nomor_tabungan ?? '-' }}
                                        </a>
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-medium fs-14">
                                            {{ $transaksi->nama ?? '-' }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted">
                                            {{ $transaksi->kelas ?? '-' }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted">
                                            {{ number_format($transaksi->total_tabungan, 0, ',', '.') }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted">
                                            {{ number_format($transaksi->total_cicilan, 0, ',', '.') }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted">
                                            {{ number_format($transaksi->total_keseluruhan, 0, ',', '.') }}
                                        </p>
                                    </td>
                                    <td>
                                        <a href="{{ route('transaksi.edit', ['transaksi' => $transaksi->id]) }}"
                                           class="btn btn-icon btn-sm bg-primary-subtle me-1"
                                           data-bs-toggle="tooltip" title="Edit">
                                            <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                        </a>
                                        <form action="{{ route('transaksi.destroy', ['transaksi' => $transaksi->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-icon btn-sm bg-danger-subtle"
                                                    data-bs-toggle="tooltip" title="Delete">
                                                <i class="mdi mdi-delete fs-14 text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data tabungan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer py-0 border-top">
                    <div class="row align-items-center">
                        <div class="col-sm">
                            <div class="text-block text-center text-sm-start">
                                @if ($transaksis->count() > 0)
                                <span class="fw-medium">
                                    Showing {{ $transaksis->count() }} of {{ $transaksis->total() }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-auto mt-3 mt-sm-0">
                            {{ $transaksis->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Data Table -->
</div>
<!-- End Content -->
@endsection
