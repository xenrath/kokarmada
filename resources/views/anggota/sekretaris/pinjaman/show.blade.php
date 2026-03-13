@extends('layout.app')

@section('title', 'Detail Pinjaman')

@section('style')
    <style>
        .card-select:hover {
            background: #f8f9fa;
        }
    </style>
@endsection

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('anggota/sekretaris/pinjaman') }}" class="btn btn-secondary rounded-0">
                <i class="mdi mdi-arrow-left"></i>
            </a>
            <div class="page-title-box">
                <h4 class="page-title">Detail Pinjaman</h4>
            </div>
        </div>
        <!-- end page title -->

        @if ($pinjaman->status == 'diajukan')
            <div class="card mb-2 rounded-0">
                <div class="card-body">
                    <div class="alert alert-warning rounded-0 mb-0" role="alert">
                        <i class="dripicons-clock me-2"></i>
                        Pengajuan ini sedang menunggu proses pengecekan dan analisis oleh <strong>Manajer Analis</strong>.
                    </div>
                </div>
            </div>
        @endif
        @if ($pinjaman->status == 'disetujui_manajer')
            <div class="card mb-2 rounded-0">
                <div class="card-body">
                    <div class="alert alert-warning rounded-0 mb-0" role="alert">
                        <i class="dripicons-clock me-2"></i>
                        Pengajuan pinjaman ini sedang menunggu persetujuan dari <strong>Ketua</strong>.
                    </div>
                </div>
            </div>
        @endif
        @if ($pinjaman->status == 'disetujui_ketua')
            <div class="card mb-2 rounded-0">
                <div class="card-body">
                    <div class="alert alert-success rounded-0 mb-0" role="alert">
                        <i class="dripicons-checkmark me-2"></i>
                        Anda telah menyetujui pengajuan pinjaman ini dengan nominal
                        <strong>@rupiah($pinjaman->nominal_disetujui)</strong>
                    </div>
                </div>
            </div>
        @endif
        @if ($pinjaman->status == 'ditolak')
            <div class="card mb-2 rounded-0">
                <div class="card-body">
                    <div class="alert alert-danger rounded-0 mb-0" role="alert">
                        <i class="dripicons-wrong me-2"></i>
                        Pengajuan pinjaman ini telah Anda tolak. Pengajuan tidak akan diproses ke tahap selanjutnya.
                    </div>
                </div>
            </div>
        @endif
        <div class="card mb-4 rounded-0">
            <div class="card-body px-0 pt-2 pb-0">
                <ul class="nav nav-tabs nav-justified nav-bordered">
                    <li class="nav-item">
                        <a href="#nasabah-b1" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                            <i class="mdi mdi-account-details-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">DETAIL NASABAH</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#pinjaman-b1" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                            <i class="mdi mdi-file-document-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">DETAIL PINJAMAN</span>
                        </a>
                    </li>
                    @if ($pinjaman_analis ?? false)
                        <li class="nav-item">
                            <a href="#analisis-b1" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                <i class="mdi mdi-clipboard-search-outline d-md-none d-block"></i>
                                <span class="d-none d-md-block">ANALISIS PINJAMAN</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane show active" id="nasabah-b1">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="{{ asset('storage/uploads/' . $user_detail->foto_diri) }}" alt=""
                                    class="img-fluid mb-2">
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>Nama Lengkap</strong>
                                    <br>
                                    {{ $user->nama }}
                                </div>
                                <div class="mb-2">
                                    <strong>Nama Panggilan</strong>
                                    <br>
                                    {{ $user->panggilan }}
                                </div>
                                <div class="mb-2">
                                    <strong>Jenis Kelamin</strong>
                                    <br>
                                    {{ $user->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </div>
                                <div class="mb-2">
                                    <strong>No. HP / WhatsApp</strong>
                                    <br>
                                    {{ $user->telp }}
                                </div>
                                <div class="mb-2">
                                    <strong>Tempat Lahir</strong>
                                    <br>
                                    {{ $user_detail->tempat_lahir ?? '-' }}
                                </div>
                                <div class="mb-2">
                                    <strong>Tanggal Lahir</strong>
                                    <br>
                                    {{ $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->translatedFormat('d F Y') : null }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body border-top">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>No. KTP</strong>
                                <br>
                                {{ $user_detail->no_ktp ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Masa Berlaku KTP</strong>
                                <br>
                                {{ $user_detail->masa_berlaku_ktp ?? '-' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>File KTP</strong>
                                <br>
                                @if ($user_detail->file_ktp ?? null)
                                    <a href="{{ asset('storage/uploads/' . $user_detail->file_ktp) }}"
                                        class="btn btn-sm btn-outline-secondary rounded-0 mt-1" target="_blank">
                                        Lihat File KTP
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>File KK</strong>
                                <br>
                                @if ($user_detail->file_kk ?? null)
                                    <a href="{{ asset('storage/uploads/' . $user_detail->file_kk) }}"
                                        class="btn btn-sm btn-outline-secondary rounded-0 mt-1" target="_blank">
                                        Lihat File KK
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Alamat Lengkap</strong>
                                <br>
                                {{ $user_detail->alamat ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Kode POS</strong>
                                <br>
                                {{ $user_detail->kode_pos ?? '-' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Pekerjaan</strong>
                                <br>
                                {{ $user_detail->pekerjaan ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>No. NPWP</strong>
                                <br>
                                {{ $user_detail->no_npwp ?? '-' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Nama Gadis Ibu Kandung</strong>
                                <br>
                                {{ $user_detail->nama_ibu ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Tinggal Bersama</strong>
                                <br>
                                {{ $user_detail->tinggal_bersama ?? '-' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>
                                    Nama
                                    @if ($user->gender == 'L')
                                        Istri
                                    @else
                                        Suami
                                    @endif
                                </strong>
                                <br>
                                {{ $user_detail->nama_pasangan ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>
                                    Pekerjaan
                                    @if ($user->gender == 'L')
                                        Istri
                                    @else
                                        Suami
                                    @endif
                                </strong>
                                <br>
                                {{ $user_detail->pekerjaan_pasangan ?? '-' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Nama Bank</strong>
                                <br>
                                {{ $user_detail->bank_nama ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Nomor Rekening Bank</strong>
                                <br>
                                {{ $user_detail->bank_rekening ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="pinjaman-b1">
                    <div class="card-body">
                        <div class="mb-2 text-end">
                            <a href="{{ url('anggota/pinjaman/print/' . $pinjaman->id) }}"
                                class="btn btn-sm btn-dark rounded-0" target="_blank">
                                <i class="mdi mdi-printer"></i>
                                Print
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Kode Pinjaman</strong>
                                <br>
                                {{ $pinjaman->kode }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Nama Anggota</strong>
                                <br>
                                {{ $pinjaman->user->nama }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Tanggal Pengajuan</strong>
                                <br>
                                {{ Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->translatedFormat('d F Y') }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Tanggal Disetujui</strong>
                                <br>
                                @if ($pinjaman->tanggal_disetuju != null)
                                    {{ Carbon\Carbon::parse($pinjaman->tanggal_disetuju)->translatedFormat('d F Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body border-top">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Nominal Pengajuan</strong>
                                <br>
                                @rupiah($pinjaman->nominal)
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Bunga Pinjaman</strong>
                                <br>
                                {{ $pinjaman->bunga_persen }}% / Tahun
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Jangka Waktu</strong>
                                <br>
                                {{ $pinjaman->jangka_waktu }} Tahun /
                                {{ $pinjaman->jangka_waktu * 12 }} Bulan
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Total Pinjaman</strong>
                                <br>
                                @rupiah($pinjaman->nominal * (1 + ($pinjaman->bunga_persen / 100) * $pinjaman->jangka_waktu))
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Tujuan Pengajuan</strong>
                                <br>
                                {{ $pinjaman->tujuan }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Usaha yang akan dibiayai</strong>
                                <br>
                                @if ($pinjaman->usaha == 'lainnya')
                                    {{ $pinjaman->usaha_lainnya }}
                                @else
                                    {{ ucfirst($pinjaman->usaha) }}
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Tipe Angsuran</strong>
                                <br>
                                {{ ucfirst($pinjaman->tipe_angsuran) }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Progres Angsuran</strong>
                                <br>
                                {{ $pinjaman->tipe_angsuran }}
                            </div>
                        </div>
                    </div>
                    @if ($pinjaman->pinjaman_agunan)
                        <div class="card-body border-top">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Jenis Agunan Tambahan</strong>
                                    <br>
                                    @if ($pinjaman->pinjaman_agunan->jenis_agunan == 'lainnya')
                                        {{ $pinjaman->pinjaman_agunan->jenis_agunan_lainnya }}
                                    @else
                                        {{ ucfirst($pinjaman->pinjaman_agunan->jenis_agunan) }}
                                    @endif
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Bukti Kepemilikan Agunan</strong>
                                    <br>
                                    {{ $pinjaman->pinjaman_agunan->bukti_agunan == 'hak_pakai' ? 'Hak Pakai' : ucfirst($pinjaman->pinjaman_agunan->bukti_agunan) }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Penguasaan Bukti Kepemilikan</strong>
                                    <br>
                                    @if ($pinjaman->pinjaman_agunan->bukti_kepemilikan == 'milik_nasabah')
                                        Milik Nasabah
                                    @else
                                        Bukan Milik Nasabah
                                    @endif
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>File Bukti Agunan</strong>
                                    <br>
                                    <a href="{{ asset('storage/uploads/' . $pinjaman->pinjaman_agunan->bukti_file) }}"
                                        class="btn btn-sm btn-outline-secondary rounded-0 mt-1" target="_blank">
                                        Lihat Bukti
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card-body border-top">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Dinas / Instansi Tempat Bekerja</strong>
                                <br>
                                {{ $pinjaman->tempat_kerja }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Jabatan Terakhir</strong>
                                <br>
                                {{ $pinjaman->jabatan_terakhir }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Lama Bekerja pada Dinas / Instansi</strong>
                                <br>
                                {{ $pinjaman->lama_kerja }} Tahun
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Pendapatan Kotor</strong>
                                <br>
                                @rupiah($pinjaman->pendapatan_kotor)
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Pendapatan Bersih</strong>
                                <br>
                                @rupiah($pinjaman->pendapatan_bersih)
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>File Slip Gaji</strong>
                                <br>
                                <a href="{{ asset('storage/uploads/' . $pinjaman->slip_gaji) }}"
                                    class="btn btn-sm btn-outline-secondary rounded-0 mt-1" target="_blank">
                                    Lihat Slip
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($pinjaman_analis ?? false)
                    <div class="tab-pane" id="analisis-b1">
                        <div class="card-body">
                            <div class="mb-2">
                                <strong>Nominal Rekomendasi Analis</strong>
                                <br>
                                @rupiah($pinjaman_analis->nominal)
                            </div>
                            <div class="mb-2">
                                <strong>Catatan Hasil Analisis</strong>
                                <br>
                                {!! nl2br(e($pinjaman_analis->catatan)) !!}
                            </div>
                            <div class="mb-2">
                                <strong>Tanggal Analisis</strong>
                                <br>
                                {{ Carbon\Carbon::parse($pinjaman_analis->updated_at)->translatedFormat('d F Y') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- container -->
@endsection

@section('script')
    <script>
        function format_rupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        $('#nominal').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            $(this).val(format_rupiah(value));
        });
    </script>
    <script>
        function form_edit() {
            $('#btn-edit').prop('disabled', true);
            $('#btn-edit-text').hide();
            $('#btn-edit-load').show();
            $('#form-edit').submit();
        }

        function form_submit() {
            $('#btn-submit').prop('disabled', true);
            $('#btn-submit-text').hide();
            $('#btn-submit-load').show();
            $('#form-submit').submit();
        }
    </script>
@endsection
