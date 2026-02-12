@extends('layout.app')

@section('title', 'Buat Pinjaman')

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
        <div class="card mb-4 rounded-0">
            <form action="{{ url('anggota/pinjaman/') }}" method="POST" autocomplete="off" id="form-submit"
                enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label for="nominal" class="form-label">Nominal pengajuan permohonan kredit *</label>
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
                                    <small class="text-muted ms-md-2">
                                        (% per Tahun)
                                    </small>
                                </label>
                                <input type="text" class="form-control rounded-0"
                                    value="{{ $pengaturan->bunga_pinjaman }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label for="total" class="form-label">Total Pinjaman *</label>
                                <input type="text" id="total" name="total"
                                    class="form-control rounded-0 @error('total') is-invalid @enderror"
                                    value="{{ old('total') }}" readonly>
                                @error('total')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label for="tujuan" class="form-label">Tujuan Pengajuan Kredit *</label>
                        <textarea class="form-control rounded-0 @error('tujuan') is-invalid @enderror" id="tujuan" name="tujuan"
                            rows="3">{{ old('tujuan') }}</textarea>
                        @error('tujuan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <div class="">
                                    <label for="usaha" class="form-label">Usaha yang akan dibiayai *</label>
                                    <select class="form-select rounded-0 @error('usaha') is-invalid @enderror"
                                        id="usaha" name="usaha" onchange="usaha_check()">
                                        <option value="">- Pilih -</option>
                                        <option value="perdagangan" {{ old('usaha') == 'perdagangan' ? 'selected' : '' }}>
                                            Perdagangan
                                        </option>
                                        <option value="pertanian" {{ old('usaha') == 'pertanian' ? 'selected' : '' }}>
                                            Pertanian
                                        </option>
                                        <option value="jasa" {{ old('usaha') == 'jasa' ? 'selected' : '' }}>
                                            Jasa
                                        </option>
                                        <option value="lainnya" {{ old('usaha') == 'lainnya' ? 'selected' : '' }}>
                                            Lainnya
                                        </option>
                                    </select>
                                </div>
                                <div class="mt-1" id="usaha-lainnya-div"
                                    style="display: {{ old('usaha') == 'lainnya' ? 'block' : 'none' }};">
                                    <input type="text" id="usaha_lainnya" name="usaha_lainnya"
                                        class="form-control rounded-0 @error('usaha_lainnya') is-invalid @enderror"
                                        placeholder="sebutkan usaha lainnya" value="{{ old('usaha_lainnya') }}">
                                    @error('usaha_lainnya')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                @error('usaha')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                                @error('usaha_lainnya')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="jangka_waktu" class="form-label">Jangka Waktu Pinjaman *</label>
                            <select class="form-select rounded-0 @error('jangka_waktu') is-invalid @enderror"
                                id="jangka_waktu" name="jangka_waktu">
                                <option value="1"
                                    {{ old('jangka_waktu', $pengaturan->jangka_waktu_pinjaman) == '1' ? 'selected' : '' }}>
                                    12 Bulan (1 Tahun)
                                </option>
                                <option value="2"
                                    {{ old('jangka_waktu', $pengaturan->jangka_waktu_pinjaman) == '2' ? 'selected' : '' }}>
                                    24 Bulan (2 Tahun)
                                </option>
                                <option value="3"
                                    {{ old('jangka_waktu', $pengaturan->jangka_waktu_pinjaman) == '3' ? 'selected' : '' }}>
                                    36 Bulan (3 Tahun)
                                </option>
                                <option value="4"
                                    {{ old('jangka_waktu', $pengaturan->jangka_waktu_pinjaman) == '4' ? 'selected' : '' }}>
                                    48 Bulan (4 Tahun)
                                </option>
                                <option value="5"
                                    {{ old('jangka_waktu', $pengaturan->jangka_waktu_pinjaman) == '5' ? 'selected' : '' }}>
                                    60 Bulan (5 Tahun)
                                </option>
                            </select>
                            @error('jangka_waktu')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="tipe_angsuran" class="form-label">Tipe Angsuran *</label>
                            <select class="form-select rounded-0 @error('tipe_angsuran') is-invalid @enderror"
                                id="tipe_angsuran" name="tipe_angsuran">
                                <option value="bulanan" {{ old('tipe_angsuran') == 'bulanan' ? 'selected' : '' }}>
                                    Tiap Bulan
                                </option>
                                <option value="sekaligus" {{ old('tipe_angsuran') == 'sekaligus' ? 'selected' : '' }}>
                                    Sekaligus
                                </option>
                            </select>
                            @error('tipe_angsuran')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-body border-top d-none" id="layout-agunan">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <div>
                                    <label for="jenis_agunan" class="form-label">Jenis Agunan Tambahan *</label>
                                    <select class="form-select rounded-0 @error('jenis_agunan') is-invalid @enderror"
                                        id="jenis_agunan" name="jenis_agunan" onchange="jenis_agunan_check()">
                                        <option value="">- Pilih -</option>
                                        <option value="kendaraan" {{ old('jenis_agunan') == 'kendaraan' ? 'selected' : '' }}>
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
                                        <option value="sawah" {{ old('jenis_agunan') == 'sawah' ? 'selected' : '' }}>
                                            Sawah
                                        </option>
                                        <option value="lainnya" {{ old('jenis_agunan') == 'lainnya' ? 'selected' : '' }}>
                                            Lainnya
                                        </option>
                                    </select>
                                </div>
                                <div class="mt-1" id="jenis-agunan-lainnya-div"
                                    style="display: {{ old('jenis_agunan') == 'lainnya' ? 'block' : 'none' }};">
                                    <input type="text" id="jenis_agunan_lainnya" name="jenis_agunan_lainnya"
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
                                <label for="bukti_agunan" class="form-label">Bukti Kepemilikan Agunan *</label>
                                <select class="form-select rounded-0 @error('bukti_agunan') is-invalid @enderror"
                                    id="bukti_agunan" name="bukti_agunan">
                                    <option value="">- Pilih -</option>
                                    <option value="shm" {{ old('bukti_agunan') == 'shm' ? 'selected' : '' }}>
                                        SHM
                                    </option>
                                    <option value="hgb" {{ old('bukti_agunan') == 'hgb' ? 'selected' : '' }}>
                                        HGB
                                    </option>
                                    <option value="hgu" {{ old('bukti_agunan') == 'hgu' ? 'selected' : '' }}>
                                        HGU
                                    </option>
                                    <option value="hak_pakai" {{ old('bukti_agunan') == 'hak_pakai' ? 'selected' : '' }}>
                                        Hak Pakai
                                    </option>
                                    <option value="bpkb" {{ old('bukti_agunan') == 'bpkb' ? 'selected' : '' }}>
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
                                <label for="bukti_kepemilikan" class="form-label">Penguasaan Bukti Kepemilikan *</label>
                                <select class="form-select rounded-0 @error('bukti_kepemilikan') is-invalid @enderror"
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
                                    <small class="text-muted ms-md-2">
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
                <div class="card-body border-top">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="tempat_kerja" class="form-label">Dinas / Instansi Tempat Bekerja *</label>
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
                                <label for="jabatan_terakhir" class="form-label">Jabatan Terakhir *</label>
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
                                    Lama Bekerja pada Dinas / Instansi *
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
                                <label for="pendapatan_kotor" class="form-label">Pendapatan Kotor dalam 1 Bulan *</label>
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
                                    <small class="text-muted ms-md-2">
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
                <div class="card-footer text-end">
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
            </form>
        </div>
    </div>
    <!-- container -->
@endsection

@section('script')
    <script>
        function usaha_check(params) {
            if ($('#usaha').val() == 'lainnya') {
                $('#usaha-lainnya-div').show();
            } else {
                $('#usaha-lainnya-div').hide();
            }
        }

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

            function get_jangka_waktu() {
                return parseInt($('#jangka_waktu').val()) || 0;
            }

            function cek_agunan() {
                let nominal_int = get_nominal_int();

                if (nominal_int > 25000000) {
                    $('#layout-agunan').removeClass('d-none');
                } else {
                    $('#layout-agunan').addClass('d-none');
                    $('#agunan').val('');
                }
            }

            function hitung_total() {
                let nominal_int = get_nominal_int();
                let jangka_waktu = get_jangka_waktu();

                if (nominal_int === 0 || jangka_waktu === 0) {
                    $('#total').val('');
                    return;
                }

                let total = nominal_int + (nominal_int * bunga_tahun * jangka_waktu);
                $('#total').val(format_rupiah(total));
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
                hitung_total();
            });

            $('#jangka_waktu').on('change', function() {
                hitung_total();
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
            hitung_total();
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
