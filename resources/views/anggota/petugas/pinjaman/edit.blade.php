@extends('layout.app')

@section('title', 'Edit Anggota')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('admin/anggota') }}" class="btn btn-secondary rounded-0">
                <i class="mdi mdi-arrow-left"></i>
            </a>
            <div class="page-title-box">
                <h4 class="page-title">Edit Anggota</h4>
            </div>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <form action="{{ url('admin/anggota/' . $user->id) }}" method="POST" autocomplete="off" id="form-submit">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-2">
                        <label for="nama" class="form-label">Nama Lengkap *</label>
                        <input type="text" id="nama" name="nama"
                            class="form-control rounded-0 @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $user->nama) }}">
                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label for="email" class="form-label">Email *</label>
                        <input type="text" id="email" name="email"
                            class="form-control rounded-0 @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label for="gender" class="form-label">Jenis Kelamin *</label>
                        <select class="form-select rounded-0 @error('gender') is-invalid @enderror" id="gender"
                            name="gender">
                            <option value="">- Pilih -</option>
                            <option value="L" {{ old('gender', $user->gender) == 'L' ? 'selected' : '' }}>
                                Laki-laki
                            </option>
                            <option value="P" {{ old('gender', $user->gender) == 'P' ? 'selected' : '' }}>
                                Perempuan
                            </option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label for="spesial" class="form-label">
                            Jadikan Sebagai
                            <span class="text-muted">(kosongkan saja jika hanya menjadi anggota)</span>
                        </label>
                        <select class="form-select rounded-0 @error('spesial') is-invalid @enderror" id="spesial"
                            name="spesial">
                            <option value="">- Pilih -</option>
                            <option value="ketua" {{ old('spesial', $user->spesial) == 'ketua' ? 'selected' : '' }}>
                                Ketua
                            </option>
                            <option value="sekretaris"
                                {{ old('spesial', $user->spesial) == 'sekretaris' ? 'selected' : '' }}>
                                Sekretaris
                            </option>
                            <option value="bendahara"
                                {{ old('spesial', $user->spesial) == 'bendahara' ? 'selected' : '' }}>
                                Bendahara
                            </option>
                            <option value="manajer" {{ old('spesial', $user->spesial) == 'manajer' ? 'selected' : '' }}>
                                Manajer Analis
                            </option>
                            <option value="petugas" {{ old('spesial', $user->spesial) == 'petugas' ? 'selected' : '' }}>
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
                <div class="card-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-warning rounded-0" data-bs-toggle="modal"
                        data-bs-target="#modal-reset">
                        <i class="mdi mdi-lock-reset"></i>
                        Reset Password
                    </button>
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

    <div id="modal-reset" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-reset-label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-reset-label">Reset Password</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin reset password dari <strong>{{ $user->nama }}</strong>?</p>
                    <span class="text-muted">
                        Password akan diubah menjadi
                        <strong>bhamada</strong>
                    </span>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning rounded-0">Reset</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
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
