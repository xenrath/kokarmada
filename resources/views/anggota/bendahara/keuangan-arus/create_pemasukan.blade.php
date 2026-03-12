@extends('layout.app')

@section('title', 'Tambah Rekening')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="page-title-box">
            <h4 class="page-title">Tambah Rekening</h4>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <form action="{{ url('anggota/bendahara/keuangan-rekening') }}" method="POST" autocomplete="off" id="form-submit">
                @csrf
                <div class="card-body">
                    <div class="form-group mb-2">
                        <label for="jenis" class="form-label">Rekening *</label>
                        <select class="form-select rounded-0 @error('jenis') is-invalid @enderror" id="jenis"
                            name="jenis" onchange="jenis_check()">
                            <option value="">- Pilih -</option>
                            <option value="bank" {{ old('jenis') == 'bank' ? 'selected' : '' }}>
                                Bank
                            </option>
                            <option value="tunai" {{ old('jenis') == 'tunai' ? 'selected' : '' }}>
                                Tunai
                            </option>
                        </select>
                        @error('jenis')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                    <div id="layout-bank" style="display: none;">
                        <div class="form-group mb-2">
                            <label for="bank" class="form-label">Nama Bank *</label>
                            <input type="text" id="bank" name="bank"
                                class="form-control rounded-0 @error('bank') is-invalid @enderror"
                                value="{{ old('bank') }}">
                            @error('bank')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group mb-2">
                            <label for="nomor" class="form-label">Nomor Rekening *</label>
                            <input type="text" id="nomor" name="nomor"
                                class="form-control rounded-0 @error('nomor') is-invalid @enderror"
                                value="{{ old('nomor') }}">
                            @error('nomor')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group mb-2">
                            <label for="nama_atas" class="form-label">Atas Nama *</label>
                            <input type="text" id="nama_atas" name="nama_atas"
                                class="form-control rounded-0 @error('nama_atas') is-invalid @enderror"
                                value="{{ old('nama_atas') }}">
                            @error('nama_atas')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div id="layout-tunai" style="display: none;">
                        <div class="form-group mb-2">
                            <label for="nama_rekening" class="form-label">Nama Rekening *</label>
                            <input type="text" id="nama_rekening" name="nama_rekening"
                                class="form-control rounded-0 @error('nama_rekening') is-invalid @enderror"
                                value="{{ old('nama_rekening') }}">
                            @error('nama_rekening')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary rounded-0" id="btn-submit" onclick="form_submit()">
                        <span id="btn-submit-text">
                            <i class="mdi mdi-content-save"></i>
                            Simpan
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
        function jenis_check() {
            let jenis = $('#jenis').val();

            if (jenis === 'bank') {
                $('#layout-bank').show();
                $('#layout-tunai').hide();
            } else if (jenis === 'tunai') {
                $('#layout-bank').hide();
                $('#layout-tunai').show();
            } else {
                // kondisi default (belum pilih apa-apa)
                $('#layout-bank').hide();
                $('#layout-tunai').hide();
            }
        }

        $(document).ready(function() {
            jenis_check();

            $('#jenis').on('change', function() {
                jenis_check();
            });
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
