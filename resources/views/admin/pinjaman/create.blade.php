@extends('layout.app')

@section('title', 'Buat Pinjaman')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('admin/pinjaman') }}" class="btn btn-secondary rounded-0">
                <i class="mdi mdi-arrow-left"></i>
            </a>
            <div class="page-title-box">
                <h4 class="page-title">Buat Pinjaman</h4>
            </div>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <form action="{{ url('admin/pinjaman') }}" method="POST" autocomplete="off" id="form-submit">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="user_id" class="form-label">
                                Nama Anggota *
                            </label>
                            <div class="">
                                <button type="button" class="btn btn-outline-secondary rounded-0" data-bs-toggle="modal"
                                    data-bs-target="#modal-anggota">
                                    Pilih Anggota
                                </button>
                            </div>
                            @error('user_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="email" class="form-label">Email *</label>
                        <input type="text" id="email" name="email"
                            class="form-control rounded-0 @error('email') is-invalid @enderror" value="{{ old('email') }}">
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
                    <div class="mb-2">
                        <label for="spesial" class="form-label">
                            Jadikan Sebagai
                            <span class="text-muted">(kosongkan saja jika hanya menjadi anggota)</span>
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
                    <div class="mt-4 mb-2">
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

    <div id="modal-anggota" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-anggota-label"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-anggota-label">Pilih Anggota</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-header d-flex flex-column align-items-stretch">
                    <div class="input-group mb-2">
                        <input type="search" class="form-control rounded-0" id="keyword" name="keyword"
                            placeholder="Cari..." value="{{ request()->get('keyword') }}">
                        <button class="btn btn-info rounded-0" type="submit">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between">
                        <select class="form-select rounded-0" id="page" style="width: 60px;">
                            <option value="10" {{ Request::get('page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ Request::get('page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ Request::get('page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                        <button class="btn btn-primary rounded-0">
                            <i class="mdi mdi-account-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 10px">No</th>
                                <th>Nama Anggota</th>
                                <th style="width: 60px">Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="text-center align-top">{{ $loop->iteration }}</td>
                                    <td class="align-top">{{ $user->nama }}</td>
                                    <td class="text-center align-top">
                                        <button type="button" class="btn btn-outline-primary rounded-0 btn-sm"
                                            onclick="anggota_set('{{ $user->id }}', '{{ $user->name }}')">
                                            <i class="mdi mdi-check"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <span class="text-muted">- Data tidak ditemukan -</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
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
        function anggota_set(id) {
            $.ajax({
                url: "{{ url('admin/anggota-set') }}/" + id,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    // contoh: isi input
                    // $('#user_id').val(response.id);
                    // $('#user_nama').val(response.nama);
                },
                error: function(xhr) {
                    alert('Gagal mengambil data anggota');
                    console.error(xhr.responseText);
                }
            });
        }
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
