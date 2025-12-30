@extends('layout.app')

@section('title', 'Tambah Pengguna')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('admin/user') }}" class="btn btn-secondary rounded-0">
                <i class="mdi mdi-arrow-left"></i>
            </a>
            <div class="page-title-box">
                <h4 class="page-title">Tambah Pengguna</h4>
            </div>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <form action="{{ url('admin/anggota') }}" method="POST" autocomplete="off" id="form-submit">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="nama" class="form-label">Nama Lengkap *</label>
                            <input type="text" id="nama" name="nama"
                                class="form-control rounded-0 @error('nama') is-invalid @enderror"
                                value="{{ old('nama') }}">
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="panggilan" class="form-label">Nama Panggilan *</label>
                            <input type="text" id="panggilan" name="panggilan"
                                class="form-control rounded-0 @error('panggilan') is-invalid @enderror"
                                value="{{ old('panggilan') }}">
                            @error('panggilan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="gender" class="form-label">Jenis Kelamin *</label>
                            <select class="form-select rounded-0 @error('gender') is-invalid @enderror" id="gender"
                                name="gender">
                                <option value="">- Pilih -</option>
                                <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>
                                <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="telp" class="form-label d-flex flex-column flex-md-row align-items-md-center">
                                No. HP / WhatsApp *
                                <small class="text-muted ms-md-2">
                                    (08xxxxxxxxxx)
                                </small>
                            </label>
                            <input type="tel" id="telp" name="telp"
                                class="form-control rounded-0 @error('telp') is-invalid @enderror"
                                value="{{ old('telp') }}">
                            @error('telp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="spesial" class="form-label d-flex flex-column flex-md-row align-items-md-center">
                                Jadikan Sebagai
                                <small class="text-muted ms-md-2">
                                    (kosongkan saja jika hanya menjadi anggota)
                                </small>
                            </label>
                            <select class="form-select rounded-0 @error('spesial') is-invalid @enderror" id="spesial"
                                name="spesial">
                                <option value="">- Pilih -</option>
                                <option value="ketua" {{ old('spesial') == 'ketua' ? 'selected' : '' }}>
                                    Ketua
                                </option>
                                <option value="sekretaris" {{ old('spesial') == 'sekretaris' ? 'selected' : '' }}>
                                    Sekretaris
                                </option>
                                <option value="bendahara" {{ old('spesial') == 'bendahara' ? 'selected' : '' }}>
                                    Bendahara
                                </option>
                                <option value="manajer" {{ old('spesial') == 'manajer' ? 'selected' : '' }}>
                                    Manajer Analis
                                </option>
                                <option value="petugas" {{ old('spesial') == 'petugas' ? 'selected' : '' }}>
                                    Petugas
                                </option>
                            </select>
                            @error('spesial')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2 mb-2">
                        <span>
                            Password default
                            <strong>bhamada</strong>
                        </span>
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
        function form_submit() {
            $('#btn-submit').prop('disabled', true);
            $('#btn-submit-text').hide();
            $('#btn-submit-load').show();
            $('#form-submit').submit();
        }
    </script>
@endsection
