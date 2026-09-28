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
    <div class="container-fluid">

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('member.loans.index') }}" class="btn btn-secondary rounded-0">
                <i class="mdi mdi-arrow-left"></i>
            </a>

            <div class="page-title-box">
                <h4 class="page-title">Buat Pinjaman</h4>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <h4 class="header-title mb-3">Form Pinjaman</h4>

                <form action="{{ route('member.loans.store') }}" method="POST" autocomplete="off" id="loan-form"
                    enctype="multipart/form-data">
                    @csrf

                    <div id="basicwizard">

                        <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
                            <li class="nav-item">
                                <a href="#loan-tab" class="nav-link rounded-0 pt-2 pb-2 active">
                                    <i class="mdi mdi-cash me-1"></i>
                                    <span class="d-none d-sm-inline">Pinjaman</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#employment-tab" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-briefcase me-1"></i>
                                    <span class="d-none d-sm-inline">Pekerjaan</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#collateral-tab" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-shield-home me-1"></i>
                                    <span class="d-none d-sm-inline">Agunan</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content b-0 mb-0">

                            {{-- =====================================================
                                TAB 1
                            ====================================================== --}}
                            <div class="tab-pane show active" id="loan-tab">

                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="requested_amount" class="form-label">
                                                Nominal pengajuan permohonan kredit *
                                            </label>

                                            <input type="text" id="requested_amount" name="requested_amount"
                                                class="form-control rounded-0 @error('requested_amount') is-invalid @enderror"
                                                value="{{ old('requested_amount') }}" inputmode="numeric">

                                            @error('requested_amount')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-2">

                                            <label class="form-label">
                                                Bunga
                                                <small class="text-muted">
                                                    (% per Tahun)
                                                </small>
                                            </label>

                                            <input type="text" class="form-control rounded-0"
                                                value="{{ number_format((float) $interestRate, 2, ',', '.') }}%" readonly>

                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-2">

                                            <label for="monthly_installment" class="form-label">
                                                Nominal angsuran tiap bulan
                                            </label>

                                            <input type="text" id="monthly_installment" class="form-control rounded-0"
                                                value="{{ old('monthly_installment') }}" readonly>

                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6 mb-2">

                                        <label for="repayment_type" class="form-label">
                                            Jenis angsuran *
                                        </label>

                                        <select class="form-select rounded-0 @error('repayment_type') is-invalid @enderror"
                                            id="repayment_type" name="repayment_type">
                                            <option value="">- Pilih -</option>

                                            <option value="monthly"
                                                {{ old('repayment_type') === 'monthly' ? 'selected' : '' }}>
                                                Tiap bulan
                                            </option>

                                            <option value="lump_sum"
                                                {{ old('repayment_type') === 'lump_sum' ? 'selected' : '' }}>
                                                Sekaligus
                                            </option>
                                        </select>

                                        @error('repayment_type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    <div class="col-md-6 mb-2">

                                        <label for="term_months" class="form-label">
                                            Jangka waktu *
                                        </label>

                                        <select class="form-select rounded-0 @error('term_months') is-invalid @enderror"
                                            id="term_months" name="term_months">
                                            <option value="">- Pilih -</option>
                                        </select>

                                        @error('term_months')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6 mb-2">

                                        <label for="purpose_category" class="form-label">
                                            Usaha yang akan dibiayai *
                                        </label>

                                        <select
                                            class="form-select rounded-0 @error('purpose_category') is-invalid @enderror"
                                            id="purpose_category" name="purpose_category">
                                            <option value="">- Pilih -</option>

                                            <option value="business"
                                                {{ old('purpose_category') === 'business' ? 'selected' : '' }}>
                                                Usaha
                                            </option>

                                            <option value="consumer"
                                                {{ old('purpose_category') === 'consumer' ? 'selected' : '' }}>
                                                Konsumtif
                                            </option>
                                        </select>

                                        @error('purpose_category')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    <div class="col-md-6 mb-2" id="business-type-wrapper">

                                        <label for="business_type" class="form-label">
                                            Jenis usaha *
                                        </label>

                                        <select class="form-select rounded-0 @error('business_type') is-invalid @enderror"
                                            id="business_type" name="business_type">
                                            <option value="">- Pilih -</option>

                                            <option value="trade"
                                                {{ old('business_type') === 'trade' ? 'selected' : '' }}>
                                                Perdagangan
                                            </option>

                                            <option value="agriculture"
                                                {{ old('business_type') === 'agriculture' ? 'selected' : '' }}>
                                                Pertanian
                                            </option>

                                            <option value="service"
                                                {{ old('business_type') === 'service' ? 'selected' : '' }}>
                                                Jasa
                                            </option>

                                            <option value="other"
                                                {{ old('business_type') === 'other' ? 'selected' : '' }}>
                                                Lainnya
                                            </option>
                                        </select>

                                        @error('business_type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                </div>

                                <div class="form-group mb-2" id="business-type-other-wrapper">
                                    <label for="business_type_other" class="form-label">
                                        Jenis usaha lainnya *
                                    </label>

                                    <input type="text" id="business_type_other" name="business_type_other"
                                        class="form-control rounded-0 @error('business_type_other') is-invalid @enderror"
                                        value="{{ old('business_type_other') }}" placeholder="Sebutkan jenis usaha">

                                    @error('business_type_other')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-2">

                                    <label for="purpose_description" class="form-label">
                                        Tujuan pengajuan kredit *
                                    </label>

                                    <textarea class="form-control rounded-0 @error('purpose_description') is-invalid @enderror" id="purpose_description"
                                        name="purpose_description" rows="3">{{ old('purpose_description') }}</textarea>

                                    @error('purpose_description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- =====================================================
                                TAB 2
                            ====================================================== --}}
                            <div class="tab-pane" id="employment-tab">

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group mb-2">

                                            <label for="work_unit" class="form-label">
                                                Unit Kerja *
                                            </label>

                                            <input type="text" id="work_unit" name="work_unit"
                                                class="form-control rounded-0 @error('work_unit') is-invalid @enderror"
                                                value="{{ old('work_unit', 'Universitas Bhamada Slawi') }}">

                                            @error('work_unit')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-2">

                                            <label for="position" class="form-label">
                                                Jabatan *
                                            </label>

                                            <input type="text" id="position" name="position"
                                                class="form-control rounded-0 @error('position') is-invalid @enderror"
                                                value="{{ old('position') }}">

                                            @error('position')
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

                                            <label for="employment_duration_years" class="form-label">
                                                Lama bekerja *
                                            </label>

                                            <div class="input-group">

                                                <input type="number" id="employment_duration_years"
                                                    name="employment_duration_years"
                                                    class="form-control rounded-0 @error('employment_duration_years') is-invalid @enderror"
                                                    min="0" max="100" step="1"
                                                    value="{{ old('employment_duration_years') }}">

                                                <span class="input-group-text rounded-0">
                                                    Tahun
                                                </span>

                                            </div>

                                            @error('employment_duration_years')
                                                <small class="text-danger">
                                                    {{ $message }}
                                                </small>
                                            @enderror

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group mb-2">

                                            <label for="other_monthly_income" class="form-label">
                                                Pendapatan di Luar Gaji
                                            </label>

                                            <input type="text" id="other_monthly_income" name="other_monthly_income"
                                                class="form-control rounded-0 @error('other_monthly_income') is-invalid @enderror"
                                                value="{{ old('other_monthly_income') }}" inputmode="numeric">

                                            @error('other_monthly_income')
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

                                            <label for="net_monthly_income" class="form-label">
                                                Pendapatan Bersih yang Diterima
                                            </label>

                                            <input type="text" id="net_monthly_income" name="net_monthly_income"
                                                class="form-control rounded-0 @error('net_monthly_income') is-invalid @enderror"
                                                value="{{ old('net_monthly_income') }}" inputmode="numeric">

                                            @error('net_monthly_income')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group mb-2">

                                            <label for="salary_slip" class="form-label">
                                                File Slip Gaji Terakhir *
                                                <small class="text-muted">
                                                    (pdf, jpg, png. maksimal 2 MB)
                                                </small>
                                            </label>

                                            <input type="file" id="salary_slip" name="salary_slip"
                                                class="form-control rounded-0 @error('salary_slip') is-invalid @enderror"
                                                accept=".pdf,.jpg,.jpeg,.png">

                                            @error('salary_slip')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                    </div>

                                </div>

                                <div class="form-group mb-2">

                                    <label for="other_income_proof" class="form-label">
                                        Bukti Pendapatan di Luar Gaji
                                        <small class="text-muted">
                                            (opsional, pdf/jpg/png. maksimal 2 MB)
                                        </small>
                                    </label>

                                    <input type="file" id="other_income_proof" name="other_income_proof"
                                        class="form-control rounded-0 @error('other_income_proof') is-invalid @enderror"
                                        accept=".pdf,.jpg,.jpeg,.png">

                                    @error('other_income_proof')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- =====================================================
                                TAB 3
                            ====================================================== --}}
                            <div class="tab-pane" id="collateral-tab">

                                <div id="collateral-not-required" class="alert alert-info rounded-0">
                                    <i class="dripicons-information me-2"></i>

                                    Karena nominal pengajuan Anda di bawah atau sama dengan
                                    Rp25.000.000, Anda tidak perlu menyertakan agunan.
                                </div>

                                <div id="collateral-wrapper" class="d-none">

                                    <div class="alert alert-warning rounded-0">
                                        Nominal pengajuan lebih dari Rp25.000.000.
                                        Agunan wajib dilengkapi.
                                    </div>

                                    <div class="row">

                                        <div class="col-md-6">

                                            <div class="form-group mb-2">

                                                <label for="collateral_type" class="form-label">
                                                    Jenis Agunan *
                                                </label>

                                                <select
                                                    class="form-select rounded-0 @error('collateral_type') is-invalid @enderror"
                                                    id="collateral_type" name="collateral_type">
                                                    <option value="">- Pilih -</option>

                                                    <option value="vehicle"
                                                        {{ old('collateral_type') === 'vehicle' ? 'selected' : '' }}>
                                                        Kendaraan
                                                    </option>

                                                    <option value="land_building"
                                                        {{ old('collateral_type') === 'land_building' ? 'selected' : '' }}>
                                                        Tanah & Bangunan
                                                    </option>

                                                    <option value="yard"
                                                        {{ old('collateral_type') === 'yard' ? 'selected' : '' }}>
                                                        Pekarangan
                                                    </option>

                                                    <option value="rice_field"
                                                        {{ old('collateral_type') === 'rice_field' ? 'selected' : '' }}>
                                                        Sawah
                                                    </option>

                                                    <option value="other"
                                                        {{ old('collateral_type') === 'other' ? 'selected' : '' }}>
                                                        Lainnya
                                                    </option>
                                                </select>

                                                @error('collateral_type')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>

                                            <div id="collateral-description-wrapper" class="mt-1">

                                                <input type="text" id="collateral_description"
                                                    name="collateral_description" class="form-control rounded-0"
                                                    value="{{ old('collateral_description') }}"
                                                    placeholder="Keterangan agunan">

                                            </div>

                                        </div>

                                        <div class="col-md-6">

                                            <div class="form-group mb-2">

                                                <label for="ownership_proof" class="form-label">
                                                    Bukti Kepemilikan *
                                                </label>

                                                <select
                                                    class="form-select rounded-0 @error('ownership_proof') is-invalid @enderror"
                                                    id="ownership_proof" name="ownership_proof">
                                                    <option value="">- Pilih -</option>

                                                    <option value="shm"
                                                        {{ old('ownership_proof') === 'shm' ? 'selected' : '' }}>
                                                        SHM
                                                    </option>

                                                    <option value="hgb"
                                                        {{ old('ownership_proof') === 'hgb' ? 'selected' : '' }}>
                                                        HGB
                                                    </option>

                                                    <option value="hgu"
                                                        {{ old('ownership_proof') === 'hgu' ? 'selected' : '' }}>
                                                        HGU
                                                    </option>

                                                    <option value="hak_pakai"
                                                        {{ old('ownership_proof') === 'hak_pakai' ? 'selected' : '' }}>
                                                        Hak Pakai
                                                    </option>

                                                    <option value="bpkb"
                                                        {{ old('ownership_proof') === 'bpkb' ? 'selected' : '' }}>
                                                        BPKB
                                                    </option>
                                                </select>

                                                @error('ownership_proof')
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

                                                <label for="ownership_status" class="form-label">
                                                    Status Kepemilikan *
                                                </label>

                                                <select
                                                    class="form-select rounded-0 @error('ownership_status') is-invalid @enderror"
                                                    id="ownership_status" name="ownership_status">
                                                    <option value="">- Pilih -</option>

                                                    <option value="self"
                                                        {{ old('ownership_status') === 'self' ? 'selected' : '' }}>
                                                        Milik Nasabah
                                                    </option>

                                                    <option value="spouse"
                                                        {{ old('ownership_status') === 'spouse' ? 'selected' : '' }}>
                                                        Milik Pasangan
                                                    </option>

                                                    <option value="parent"
                                                        {{ old('ownership_status') === 'parent' ? 'selected' : '' }}>
                                                        Milik Orang Tua
                                                    </option>

                                                    <option value="other"
                                                        {{ old('ownership_status') === 'other' ? 'selected' : '' }}>
                                                        Milik Pihak Lain
                                                    </option>
                                                </select>

                                                @error('ownership_status')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>

                                        </div>

                                        <div class="col-md-6">

                                            <div class="form-group mb-2">

                                                <label for="collateral_proof_file" class="form-label">
                                                    File Bukti Agunan *
                                                    <small class="text-muted">
                                                        (pdf, jpg, png. maksimal 2 MB)
                                                    </small>
                                                </label>

                                                <input type="file" id="collateral_proof_file"
                                                    name="collateral_proof_file"
                                                    class="form-control rounded-0 @error('collateral_proof_file') is-invalid @enderror"
                                                    accept=".pdf,.jpg,.jpeg,.png">

                                                @error('collateral_proof_file')
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
                    </div>

                </form>
            </div>

            <div class="card-footer d-flex justify-content-between">

                <button type="button" class="btn btn-secondary rounded-0" id="prevBtn">
                    Kembali
                </button>

                <button type="button" class="btn btn-primary rounded-0" id="nextBtn">
                    Lanjut
                </button>

                <button type="submit" class="btn btn-primary rounded-0" id="btn-submit" form="loan-form">
                    <span id="btn-submit-text">
                        <i class="mdi mdi-cash-plus"></i>
                        Ajukan Pinjaman
                    </span>

                    <span id="btn-submit-load" style="display:none;">
                        <i class="mdi mdi-spin mdi-loading"></i>
                        Memproses...
                    </span>
                </button>

            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            const tabs = $('#basicwizard .nav-link');
            let currentTab = 0;

            function formatRupiah(value) {
                const number = String(value).replace(/\D/g, '');

                if (!number) {
                    return '';
                }

                return new Intl.NumberFormat('id-ID').format(number);
            }

            function parseMoney(value) {
                return parseInt(String(value).replace(/\D/g, ''), 10) || 0;
            }

            function updateButtons() {
                $('#prevBtn').prop('disabled', currentTab === 0);

                if (currentTab === tabs.length - 1) {
                    $('#nextBtn').hide();
                    $('#btn-submit').show();
                } else {
                    $('#nextBtn').show();
                    $('#btn-submit').hide();
                }
            }

            function showTab(index) {
                const tab = new bootstrap.Tab(tabs[index]);
                tab.show();
                updateButtons();
            }

            function updateTermOptions() {

                const type = $('#repayment_type').val();
                const selected = @json(old('term_months'));

                const $term = $('#term_months');

                $term.empty();
                $term.append('<option value="">- Pilih -</option>');

                if (type === 'monthly') {

                    [12, 24, 36, 48, 60].forEach(function(months) {

                        const selectedAttr = String(selected) === String(months) ?
                            'selected' :
                            '';

                        $term.append(
                            `<option value="${months}" ${selectedAttr}>
                        ${months} bulan
                    </option>`
                        );

                    });

                } else if (type === 'lump_sum') {

                    [1, 2, 3, 4, 5, 6].forEach(function(months) {

                        const selectedAttr = String(selected) === String(months) ?
                            'selected' :
                            '';

                        $term.append(
                            `<option value="${months}" ${selectedAttr}>
                        ${months} bulan
                    </option>`
                        );

                    });
                }

                calculateMonthlyInstallment();
            }

            function updateBusinessType() {

                const category = $('#purpose_category').val();

                if (category === 'business') {

                    $('#business-type-wrapper').show();

                    if ($('#business_type').val() === 'other') {
                        $('#business-type-other-wrapper').show();
                    } else {
                        $('#business-type-other-wrapper').hide();
                    }

                } else {

                    $('#business-type-wrapper').hide();
                    $('#business-type-other-wrapper').hide();

                    $('#business_type').val('');
                    $('#business_type_other').val('');
                }
            }

            function updateCollateral() {

                const amount = parseMoney($('#requested_amount').val());

                if (amount > 25000000) {
                    $('#collateral-not-required').addClass('d-none');
                    $('#collateral-wrapper').removeClass('d-none');
                } else {
                    $('#collateral-not-required').removeClass('d-none');
                    $('#collateral-wrapper').addClass('d-none');
                }
            }

            function calculateMonthlyInstallment() {

                const type = $('#repayment_type').val();
                const amount = parseMoney($('#requested_amount').val());
                const term = parseInt($('#term_months').val(), 10) || 0;
                const interestRate = {{ (float) $interestRate / 100 }};

                if (type !== 'monthly' || amount <= 0 || term <= 0) {
                    $('#monthly_installment').val('');
                    return;
                }

                const totalInterest = amount * interestRate * (term / 12);
                const total = amount + totalInterest;
                const installment = Math.ceil(total / term);

                $('#monthly_installment').val(formatRupiah(installment));
            }

            $('#requested_amount').on('input', function() {
                $(this).val(formatRupiah($(this).val()));

                updateCollateral();
                calculateMonthlyInstallment();
            });

            $('#repayment_type').on('change', function() {
                updateTermOptions();
            });

            $('#term_months').on('change', function() {
                calculateMonthlyInstallment();
            });

            $('#purpose_category').on('change', function() {
                updateBusinessType();
            });

            $('#business_type').on('change', function() {

                if ($(this).val() === 'other') {
                    $('#business-type-other-wrapper').show();
                } else {
                    $('#business-type-other-wrapper').hide();
                    $('#business_type_other').val('');
                }

            });

            $('#other_monthly_income, #net_monthly_income').on('input', function() {
                $(this).val(formatRupiah($(this).val()));
            });

            $('#nextBtn').on('click', function() {

                if (currentTab < tabs.length - 1) {
                    currentTab++;
                    showTab(currentTab);
                }

            });

            $('#prevBtn').on('click', function() {

                if (currentTab > 0) {
                    currentTab--;
                    showTab(currentTab);
                }

            });

            $('#loan-form').on('submit', function() {

                $('#btn-submit').prop('disabled', true);
                $('#btn-submit-text').hide();
                $('#btn-submit-load').show();

                // Hilangkan formatting rupiah sebelum submit.
                $('#requested_amount').val(parseMoney($('#requested_amount').val()));
                $('#other_monthly_income').val(parseMoney($('#other_monthly_income').val()));

                const netIncome = parseMoney($('#net_monthly_income').val());

                $('#net_monthly_income').val(
                    netIncome > 0 ? netIncome : ''
                );
            });

            $('#btn-submit').hide();

            updateTermOptions();
            updateBusinessType();
            updateCollateral();
            calculateMonthlyInstallment();
        });
    </script>
@endsection
