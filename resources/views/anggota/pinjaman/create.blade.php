@extends('layout.app')

@section('title', 'Buat Pinjaman')

@section('style')
    <style>
        #basicwizard .nav-link {
            pointer-events: none;
            cursor: default;
        }
    </style>
@endsection

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('anggota/pinjaman') }}" class="btn btn-secondary rounded-0">
                <i class="mdi mdi-arrow-left"></i>
            </a>
            <div class="page-title-box">
                <h4 class="page-title">Buat Pinjaman</h4>
            </div>
        </div>
        <!-- end page title -->
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Form Pinjaman</h4>
                <form action="{{ url('anggota/pinjaman') }}" method="POST" autocomplete="off" id="form-submit"
                    enctype="multipart/form-data">
                    @csrf
                    <div id="basicwizard">
                        <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
                            <li class="nav-item">
                                <a href="#basictab1" data-bs-toggle="tab" data-toggle="tab"
                                    class="nav-link rounded-0 pt-2 pb-2 active">
                                    <i class="mdi mdi-cash me-1"></i>
                                    <span class="d-none d-sm-inline">Pinjaman</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#basictab2" data-bs-toggle="tab" data-toggle="tab"
                                    class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-briefcase me-1"></i>
                                    <span class="d-none d-sm-inline">Pekerjaan</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#basictab3" data-bs-toggle="tab" data-toggle="tab"
                                    class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-shield-home me-1"></i>
                                    <span class="d-none d-sm-inline">Agunan</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content b-0 mb-0">
                            <div class="tab-pane show active" id="basictab1">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="nominal" class="form-label">Nominal pengajuan permohonan kredit
                                                *</label>
                                            <input type="text" id="nominal" name="nominal"
                                                class="form-control rounded-0 @error('nominal') is-invalid @enderror"
                                                value="{{ old('nominal') }}">
                                            @error('nominal')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="bunga" class="form-label">
                                                Bunga
                                                <small class="text-muted">
                                                    (% per Tahun)
                                                </small>
                                            </label>
                                            <input type="text" class="form-control rounded-0"
                                                value="{{ $pengaturan->bunga_pinjaman }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="angsuran_tiap_bulan" class="form-label">
                                                Nominal angsuran tiap bulan
                                            </label>
                                            <input type="text" id="angsuran_tiap_bulan" name="angsuran_tiap_bulan"
                                                class="form-control rounded-0 @error('angsuran_tiap_bulan') is-invalid @enderror"
                                                value="{{ old('angsuran_tiap_bulan') }}" readonly>
                                            @error('angsuran_tiap_bulan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="jangka_waktu_pertahun" class="form-label">
                                            Jangka waktu pinjaman *
                                        </label>
                                        <select
                                            class="form-select rounded-0 @error('jangka_waktu_pertahun') is-invalid @enderror"
                                            id="jangka_waktu_pertahun" name="jangka_waktu_pertahun">
                                            <option value="1"
                                                {{ old('jangka_waktu_pertahun', $pengaturan->jangka_waktu_pinjaman) == '1' ? 'selected' : '' }}>
                                                12 bulan (1 tahun)
                                            </option>
                                            <option value="2"
                                                {{ old('jangka_waktu_pertahun', $pengaturan->jangka_waktu_pinjaman) == '2' ? 'selected' : '' }}>
                                                24 bulan (2 tahun)
                                            </option>
                                            <option value="3"
                                                {{ old('jangka_waktu_pertahun', $pengaturan->jangka_waktu_pinjaman) == '3' ? 'selected' : '' }}>
                                                36 bulan (3 tahun)
                                            </option>
                                            <option value="4"
                                                {{ old('jangka_waktu_pertahun', $pengaturan->jangka_waktu_pinjaman) == '4' ? 'selected' : '' }}>
                                                48 bulan (4 tahun)
                                            </option>
                                            <option value="5"
                                                {{ old('jangka_waktu_pertahun', $pengaturan->jangka_waktu_pinjaman) == '5' ? 'selected' : '' }}>
                                                60 bulan (5 tahun)
                                            </option>
                                        </select>
                                        @error('jangka_waktu_pertahun')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="tujuan_kategori" class="form-label">
                                                Usaha yang akan dibiayai *
                                            </label>
                                            <select
                                                class="form-select rounded-0 @error('tujuan_kategori') is-invalid @enderror"
                                                id="tujuan_kategori" name="tujuan_kategori">
                                                <option value="">- Pilih -</option>
                                                <option value="usaha"
                                                    {{ old('tujuan_kategori') == 'usaha' ? 'selected' : '' }}>
                                                    Usaha
                                                </option>
                                                <option value="konsumtif"
                                                    {{ old('tujuan_kategori') == 'konsumtif' ? 'selected' : '' }}>
                                                    Konsumtif
                                                </option>
                                            </select>
                                            @error('usaha')
                                                <small class="text-danger">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="tujuan_keterangan" class="form-label">Tujuan pengajuan kredit *</label>
                                    <textarea class="form-control rounded-0 @error('tujuan_keterangan') is-invalid @enderror" id="tujuan_keterangan"
                                        name="tujuan_keterangan" rows="3">{{ old('tujuan_keterangan') }}</textarea>
                                    @error('tujuan_keterangan')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="jenis_angsuran" class="form-label">Jenis angsuran *</label>
                                        <select class="form-select rounded-0 @error('jenis_angsuran') is-invalid @enderror"
                                            id="jenis_angsuran" name="jenis_angsuran">
                                            <option value="bulanan"
                                                {{ old('jenis_angsuran') == 'bulanan' ? 'selected' : '' }}>
                                                Tiap bulan
                                            </option>
                                            <option value="sekaligus"
                                                {{ old('jenis_angsuran') == 'sekaligus' ? 'selected' : '' }}>
                                                Sekaligus
                                            </option>
                                        </select>
                                        @error('jenis_angsuran')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-2" id="layout-jenis_angsuran_sekaligus"
                                        style="display: none;">
                                        <label for="jenis_angsuran_sekaligus" class="form-label">
                                            Angsuran sekaligus berapa bulan *
                                        </label>
                                        <select
                                            class="form-select rounded-0 @error('jenis_angsuran_sekaligus') is-invalid @enderror"
                                            id="jenis_angsuran_sekaligus" name="jenis_angsuran_sekaligus">
                                            <option value="bulanan"
                                                {{ old('jenis_angsuran_sekaligus') == 'bulanan' ? 'selected' : '' }}>
                                                1 bulan
                                            </option>
                                            <option value="sekaligus"
                                                {{ old('jenis_angsuran_sekaligus') == 'sekaligus' ? 'selected' : '' }}>
                                                2 bulan
                                            </option>
                                            <option value="sekaligus"
                                                {{ old('jenis_angsuran_sekaligus') == 'sekaligus' ? 'selected' : '' }}>
                                                3 bulan
                                            </option>
                                            <option value="sekaligus"
                                                {{ old('jenis_angsuran_sekaligus') == 'sekaligus' ? 'selected' : '' }}>
                                                4 bulan
                                            </option>
                                            <option value="sekaligus"
                                                {{ old('jenis_angsuran_sekaligus') == 'sekaligus' ? 'selected' : '' }}>
                                                5 bulan
                                            </option>
                                            <option value="sekaligus"
                                                {{ old('jenis_angsuran_sekaligus') == 'sekaligus' ? 'selected' : '' }}>
                                                6 bulan
                                            </option>
                                        </select>
                                        @error('jenis_angsuran_sekaligus')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="basictab2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="tempat_kerja" class="form-label">Dinas / instansi tempat bekerja
                                                *</label>
                                            <input type="text" id="tempat_kerja" name="tempat_kerja"
                                                class="form-control rounded-0 @error('tempat_kerja') is-invalid @enderror"
                                                value="{{ old('tempat_kerja', 'Universitas Bhamada Slawi') }}">
                                            @error('tempat_kerja')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="jabatan_terakhir" class="form-label">Jabatan terakhir *</label>
                                            <input type="text" id="jabatan_terakhir" name="jabatan_terakhir"
                                                class="form-control rounded-0 @error('jabatan_terakhir') is-invalid @enderror"
                                                value="{{ old('jabatan_terakhir') }}">
                                            @error('jabatan_terakhir')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="lama_kerja" class="form-label">
                                                Lama bekerja pada dinas / instansi *
                                            </label>
                                            <div class="input-group">
                                                <input type="number" id="lama_kerja" name="lama_kerja"
                                                    class="form-control rounded-0 @error('lama_kerja') is-invalid @enderror"
                                                    min="0" step="1"
                                                    value="{{ old('lama_kerja', 'Universitas Bhamada Slawi') }}">
                                                <span class="input-group-text rounded-0">Tahun</span>
                                            </div>
                                            @error('lama_kerja')
                                                <small class="text-danger">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="pendapatan_kotor" class="form-label">Pendapatan kotor dalam 1
                                                bulan *</label>
                                            <input type="text" id="pendapatan_kotor" name="pendapatan_kotor"
                                                class="form-control rounded-0 @error('pendapatan_kotor') is-invalid @enderror"
                                                value="{{ old('pendapatan_kotor') }}">
                                            @error('pendapatan_kotor')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="pendapatan_bersih" class="form-label">
                                                Pendapatan Bersih yang Diterima *
                                            </label>
                                            <input type="text" id="pendapatan_bersih" name="pendapatan_bersih"
                                                class="form-control rounded-0 @error('pendapatan_bersih') is-invalid @enderror"
                                                value="{{ old('pendapatan_bersih') }}">
                                            @error('pendapatan_bersih')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="slip_gaji"
                                                class="form-label d-flex flex-column flex-md-row align-items-md-center">
                                                File Slip Gaji Terakhir *
                                                <small class="text-muted">
                                                    (format file: pdf, jpg, png. maksimal 2 mb)
                                                </small>
                                            </label>
                                            <input type="file" id="slip_gaji" name="slip_gaji"
                                                class="form-control rounded-0 @error('slip_gaji') is-invalid @enderror"
                                                accept=".pdf,image/*">
                                            @error('slip_gaji')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="basictab3">
                                <div id="agunan-aman" class="alert alert-info rounded-0" role="alert">
                                    <i class="dripicons-information me-2"></i>
                                    Karena nominal pengajuan Anda di bawah Rp25.000.000, Anda tidak perlu menyertakan
                                    agunan. Silakan tekan tombol <strong>Lanjut</strong> untuk melanjutkan proses pengajuan.
                                </div>
                                <div class="d-none" id="layout-agunan">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <div>
                                                    <label for="jenis_agunan" class="form-label">Jenis Agunan Tambahan
                                                        *</label>
                                                    <select
                                                        class="form-select rounded-0 @error('jenis_agunan') is-invalid @enderror"
                                                        id="jenis_agunan" name="jenis_agunan"
                                                        onchange="jenis_agunan_check()">
                                                        <option value="">- Pilih -</option>
                                                        <option value="kendaraan"
                                                            {{ old('jenis_agunan') == 'kendaraan' ? 'selected' : '' }}>
                                                            Kendaraan
                                                        </option>
                                                        <option value="tanah_bangunan"
                                                            {{ old('jenis_agunan') == 'tanah_bangunan' ? 'selected' : '' }}>
                                                            Tanah & Bangunan
                                                        </option>
                                                        <option value="pekarangan"
                                                            {{ old('jenis_agunan') == 'pekarangan' ? 'selected' : '' }}>
                                                            Pekarangan
                                                        </option>
                                                        <option value="sawah"
                                                            {{ old('jenis_agunan') == 'sawah' ? 'selected' : '' }}>
                                                            Sawah
                                                        </option>
                                                        <option value="lainnya"
                                                            {{ old('jenis_agunan') == 'lainnya' ? 'selected' : '' }}>
                                                            Lainnya
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="mt-1" id="jenis-agunan-lainnya-div"
                                                    style="display: {{ old('jenis_agunan') == 'lainnya' ? 'block' : 'none' }};">
                                                    <input type="text" id="jenis_agunan_lainnya"
                                                        name="jenis_agunan_lainnya"
                                                        class="form-control rounded-0 @error('jenis_agunan_lainnya') is-invalid @enderror"
                                                        value="{{ old('jenis_agunan_lainnya') }}"
                                                        placeholder="sebutkan jenis agunan lainnya">
                                                </div>
                                                @error('jenis_agunan')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                                @error('jenis_agunan_lainnya')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label for="bukti_agunan" class="form-label">Bukti Kepemilikan Agunan
                                                    *</label>
                                                <select
                                                    class="form-select rounded-0 @error('bukti_agunan') is-invalid @enderror"
                                                    id="bukti_agunan" name="bukti_agunan">
                                                    <option value="">- Pilih -</option>
                                                    <option value="shm"
                                                        {{ old('bukti_agunan') == 'shm' ? 'selected' : '' }}>
                                                        SHM
                                                    </option>
                                                    <option value="hgb"
                                                        {{ old('bukti_agunan') == 'hgb' ? 'selected' : '' }}>
                                                        HGB
                                                    </option>
                                                    <option value="hgu"
                                                        {{ old('bukti_agunan') == 'hgu' ? 'selected' : '' }}>
                                                        HGU
                                                    </option>
                                                    <option value="hak_pakai"
                                                        {{ old('bukti_agunan') == 'hak_pakai' ? 'selected' : '' }}>
                                                        Hak Pakai
                                                    </option>
                                                    <option value="bpkb"
                                                        {{ old('bukti_agunan') == 'bpkb' ? 'selected' : '' }}>
                                                        BPKB
                                                    </option>
                                                </select>
                                                @error('bukti_agunan')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label for="bukti_kepemilikan" class="form-label">Penguasaan Bukti
                                                    Kepemilikan
                                                    *</label>
                                                <select
                                                    class="form-select rounded-0 @error('bukti_kepemilikan') is-invalid @enderror"
                                                    id="bukti_kepemilikan" name="bukti_kepemilikan">
                                                    <option value="">- Pilih -</option>
                                                    <option value="milik_nasabah"
                                                        {{ old('bukti_kepemilikan') == 'milik_nasabah' ? 'selected' : '' }}>
                                                        Milik Nasabah
                                                    </option>
                                                    <option value="bukan_milik_nasabah"
                                                        {{ old('bukti_kepemilikan') == 'bukan_milik_nasabah' ? 'selected' : '' }}>
                                                        Bukan Milik Nasabah
                                                    </option>
                                                </select>
                                                @error('bukti_kepemilikan')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label for="bukti_file"
                                                    class="form-label d-flex flex-column flex-md-row align-items-md-center">
                                                    File Bukti Agunan *
                                                    <small class="text-muted">
                                                        (format file: pdf, jpg, png. maksimal 2 mb)
                                                    </small>
                                                </label>
                                                <input type="file" id="bukti_file" name="bukti_file"
                                                    class="form-control rounded-0 @error('bukti_file') is-invalid @enderror"
                                                    accept=".pdf,image/*">
                                                @error('bukti_file')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab-content -->
                    </div>
                    <!-- end #basicwizard-->
                </form>
            </div>
            <!-- end card-body -->
            <div class="card-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary rounded-0" id="prevBtn">Kembali</button>
                <button type="button" class="btn btn-primary rounded-0" id="nextBtn">Lanjut</button>
                <button type="submit" class="btn btn-primary rounded-0" id="btn-submit" onclick="form_submit()">
                    <span id="btn-submit-text">
                        <i class="mdi mdi-cash-plus"></i>
                        Buat Pinjaman
                    </span>
                    <span id="btn-submit-load" style="display: none;">
                        <i class="mdi mdi-spin mdi-loading"></i>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
        <!-- end card-->
    </div>
    <!-- container -->
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#basicwizard .nav-link').on('click', function(e) {
                e.preventDefault();
                return false;
            });
        });

        $(document).ready(function() {
            var tabs = $('#basicwizard .nav-link');
            var currentTab = 0;

            update_buttons();

            function showTab(index) {
                var tab = new bootstrap.Tab(tabs[index]);
                tab.show();
                update_buttons();
            }

            function update_buttons() {
                // Disable tombol kembali di step pertama
                if (currentTab === 0) {
                    $('#prevBtn').prop('disabled', true);
                } else {
                    $('#prevBtn').prop('disabled', false);
                }
                // Jika step terakhir
                if (currentTab === tabs.length - 1) {
                    $('#nextBtn').hide();
                    $('#btn-submit').show();
                } else {
                    $('#nextBtn').show();
                    $('#btn-submit').hide();
                }
            }

            $('#nextBtn').click(function() {
                if (currentTab < tabs.length - 1) {
                    currentTab++;
                    showTab(currentTab);
                }
            });

            $('#prevBtn').click(function() {
                if (currentTab > 0) {
                    currentTab--;
                    showTab(currentTab);
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function jenis_angsuran_toggle() {
                if ($('#jenis_angsuran').val() === 'sekaligus') {
                    $('#layout-jenis_angsuran_sekaligus').slideDown(200);
                } else {
                    $('#layout-jenis_angsuran_sekaligus').slideUp(200);
                }
            }
            jenis_angsuran_toggle();

            $('#jenis_angsuran').on('change', function() {
                jenis_angsuran_toggle();
            });

        });

        function jenis_agunan_check(params) {
            if ($('#jenis_agunan').val() == 'lainnya') {
                $('#jenis-agunan-lainnya-div').show();
            } else {
                $('#jenis-agunan-lainnya-div').hide();
            }
        }

        $('#lama_kerja').on('input', function() {
            if (this.value < 0) {
                this.value = 0;
            }
        });
    </script>
    <script>
        $(document).ready(function() {

            const bunga_tahun = {{ $pengaturan->bunga_pinjaman / 100 }};

            function format_rupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function get_nominal_int() {
                let nominal = $('#nominal').val().replace(/\D/g, '');
                return parseInt(nominal) || 0;
            }

            function get_jangka_waktu_pertahun() {
                return parseInt($('#jangka_waktu_pertahun').val()) || 0;
            }

            function cek_agunan() {
                let nominal_int = get_nominal_int();

                if (nominal_int > 25000000) {
                    $('#agunan-aman').addClass('d-none');
                    $('#layout-agunan').removeClass('d-none');
                } else {
                    $('#agunan-aman').removeClass('d-none');
                    $('#layout-agunan').addClass('d-none');
                    $('#agunan').val('');
                }
            }

            function hitung_angsuran_tiap_bulan() {
                let nominal_int = get_nominal_int();
                let jangka_waktu_pertahun = get_jangka_waktu_pertahun();
                let jangka_waktu_perbulan = jangka_waktu_pertahun * 12;
                let nominal_bunga_pertahun = nominal_int * bunga_tahun * jangka_waktu_pertahun;

                if (nominal_int === 0 || jangka_waktu_pertahun === 0) {
                    $('#angsuran_tiap_bulan').val('');
                    return;
                }

                let angsuran_tiap_bulan = Math.ceil(
                    (nominal_int + nominal_bunga_pertahun) / jangka_waktu_perbulan
                );

                console.log(angsuran_tiap_bulan);

                $('#angsuran_tiap_bulan').val(format_rupiah(angsuran_tiap_bulan));
            }

            $('#nominal').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');

                if (value === '') {
                    $(this).val('');
                    $('#total').val('');
                    cek_agunan();
                    return;
                }

                $(this).val(format_rupiah(value));

                cek_agunan();
                hitung_angsuran_tiap_bulan();
            });

            $('#jangka_waktu_pertahun').on('change', function() {
                hitung_angsuran_tiap_bulan();
            });

            $('#pendapatan_kotor').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                $(this).val(format_rupiah(value));
            });

            $('#pendapatan_bersih').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                $(this).val(format_rupiah(value));
            });

            // Saat reload (old input)
            cek_agunan();
            hitung_angsuran_tiap_bulan();
        });
    </script>
    <script>
        function form_submit() {
            $('#btn-submit').prop('disabled', true);
            $('#btn-submit-text').hide();
            $('#btn-submit-load').show();
            $('#form-submit').submit();
        }
    </script>
@endsection
