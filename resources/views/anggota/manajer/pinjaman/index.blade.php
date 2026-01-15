@extends('layout.app')

@section('title', 'Data Pinjaman')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="page-title-box">
            <h4 class="page-title">Data Pinjaman</h4>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <div class="card-body pb-0">
                <div class="row mb-2">
                    <div class="col-xl-8">
                        <form action="{{ url('anggota/pinjaman') }}" method="get" autocomplete="off" id="form-filter"
                            class="row gy-2 gx-2 align-items-center justify-content-xl-start justify-content-between">
                            <div class="col-auto">
                                <div class="d-flex align-items-center">
                                    <select class="form-select rounded-0" name="status"
                                        onchange="$('#form-filter').submit();">
                                        <option value="">- Pilih Status -</option>
                                        <option value="menunggu"
                                            {{ request()->get('status') == 'menunggu' ? 'selected' : '' }}>
                                            Menunggu
                                        </option>
                                        <option value="proses" {{ request()->get('status') == 'proses' ? 'selected' : '' }}>
                                            Proses
                                        </option>
                                        <option value="selesai"
                                            {{ request()->get('status') == 'selesai' ? 'selected' : '' }}>
                                            Selesai
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <label for="keyword" class="visually-hidden">Cari</label>
                                <div class="input-group">
                                    <input type="search" class="form-control rounded-0" id="keyword" name="keyword"
                                        placeholder="Cari..." value="{{ request()->get('keyword') }}">
                                    <button class="btn btn-primary rounded-0" type="submit">
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 10px">No</th>
                                <th>Tanggal Pinjaman</th>
                                <th>Nama Nasabah</th>
                                <th>Nominal</th>
                                <th>Jangka Waktu</th>
                                <th>Status</th>
                                <th style="width: 120px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pinjamans as $pinjaman)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        {{ Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->translatedFormat('d F Y') }}
                                    </td>
                                    <td>{{ $pinjaman->user->nama }}</td>
                                    <td>@rupiah($pinjaman->nominal)</td>
                                    <td>
                                        @if ($pinjaman->tipe_angsuran == 'bulanan')
                                            {{ $pinjaman->jangka_waktu * 12 }} Bulan
                                        @else
                                            {{ $pinjaman->jangka_waktu }} Tahun
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info-lighten rounded-0">
                                            {{ ucfirst($pinjaman->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ url('anggota/manajer/pinjaman/print/' . $pinjaman->id) }}"
                                            class="action-icon" target="_blank">
                                            <i class="mdi mdi-printer"></i>
                                        </a>
                                        <a href="{{ url('anggota/manajer/pinjaman/' . $pinjaman->id) }}" class="action-icon">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="{{ url('admin/anggota/edit') }}" class="action-icon">
                                            <i class="mdi mdi-square-edit-outline"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <span class="text-muted">- Data tidak ditemukan -</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- container -->
@endsection
