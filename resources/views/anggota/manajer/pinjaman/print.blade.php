<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Formulir Pengajuan Pinjaman Koperasi</title>
    <style>
        * {
            font-family: 'Times New Roman', Times, serif;
            text-align: justify;
            text-justify: inter-word;
        }

        body {
            padding: -10px;
            font-size: 14px;
            /* line-height: 1.2; */
        }

        /* .logo {
            width: 80px;
            position: absolute;
        }

        .header {
            margin-left: 100px;
            height: 100px;
        }

        .header .h1 {
            font-size: 16px;
            font-weight: bold;
            display: block;
            text-align: center;
            text-justify: none;
            margin-bottom: 2px;
        }

        .header .h2 {
            font-size: 16px;
            font-weight: bold;
            display: block;
            text-align: center;
            text-justify: none;
            margin-bottom: 2px;
        }

        .header .p {
            font-size: 14px;
            display: block;
            text-align: center;
            text-justify: none;
            margin-bottom: 2px;
        }

        .h1-title {
            font-size: 16px;
            font-weight: bold;
            display: block;
            text-align: center;
            text-justify: none;
        }

        .h1-num {
            font-size: 14px;
            display: block;
            text-align: center;
            text-justify: none;
        }

        .h1-sm {
            font-size: 14px;
            font-weight: bold;
            display: block;
            text-align: center;
            text-justify: none;
        }

        .hr {
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .tanggal {
            display: flex;
            float: right;
        }

        .table {
            width: 100%;

        }

        .table .th {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
            vertical-align: top;
        }

        .table .td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
            vertical-align: top;
        }

        .table .td-sm {
            border: 1px solid black;
            text-align: center;
            vertical-align: middle;
        }

        .text-center {
            text-align: center
        }

        .text-header {
            font-size: 18px;
            font-weight: 500;
        }

        .layout-ttd {
            display: inline-flex;
            text-align: center;
        }

        .ttd-p {
            text-align: center;
            text-justify: none;
        }

        .text-muted {
            font-size: 14px;
            opacity: 80%;
        }

        .page-break {
            page-break-after: always;
        }

        .border {
            border: 1px solid black;
        } */
    </style>
</head>

<body>
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 20px;">
        <tr>
            <td width="15%" align="center" valign="middle">
                <img src="{{ public_path('storage/uploads/asset/logo-sm.png') }}" width="70">
            </td>
            <td width="70%" align="center" valign="middle">
                <div style="line-height: 1.4;">
                    <div style="font-size:14px; font-weight:bold; text-align: center;">
                        KOPERASI KARYAWAN BHAMADA SLAWI
                    </div>
                    <div style="font-size:14px; text-align: center;">
                        Alamat : Jl. Cut Nyak Dhien No. 16 Kalisapu-Slawi, Kab. Tegal
                    </div>
                    <div style="font-size:14px; text-align: center;">
                        Email : <u>kopkarmada123@gmail.com</u>, Telp. (+62) 85801162116
                    </div>
                </div>
            </td>
            <td width="15%" align="center" valign="middle">
                <img src="{{ public_path('storage/uploads/asset/logo-sm.png') }}" width="70">
            </td>
        </tr>
    </table>
    <div style="border: 1px solid black; padding: 10px; line-height: 1.4; margin-bottom: 6px;">
        <div style="font-size: 14px; font-weight:bold; text-align: center;">
            <u>PERMOHONAN KREDIT</u>
        </div>
        <div style="font-size: 14px; text-align: center;">
            No;
            {{ $pinjaman->kode }}
        </div>
        <br>
        <div style="font-size: 14px;">
            Kepada Yth;
            <br>
            Ketua Koperasi Karyawan Bhamada (KOPKARMADA)
            <br>
            di Slawi
        </div>
        <br>
        <div style="font-size: 14px;">
            Dengan hormat,
            <br>
            Yang bertanda tangan dibawah ini :
        </div>
        <br>
        <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
            <tr>
                <!-- KIRI -->
                <td width="60%" valign="top">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td style="vertical-align: top; width: 120px;">Nama</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user->nama }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">Panggilan</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user->panggilan }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">TTL</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->tempat_lahir }},
                                {{ Carbon\Carbon::parse($user_detail->tanggal_lahir)->translatedFormat('d F Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">Pekerjaan</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->pekerjaan }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">
                                Nama
                                @if ($user->gender == 'L')
                                    Istri
                                @else
                                    Suami
                                @endif
                            </td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->nama_pasangan ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">Alamat</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->alamat }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">Nama Gadis Ibu Kandung
                            </td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->nama_ibu }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">Tinggal Bersama</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->tinggal_bersama }}
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- SPASI TENGAH -->
                <td width="2%">&nbsp;</td>

                <!-- KANAN -->
                <td width="38%" valign="top">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td style="vertical-align: top; width: 120px;">No. KTP</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->no_ktp }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">Masa Berlaku KTP</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->masa_berlaku_ktp }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">No. Telp / HP</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user->telp }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;" colspan="3">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">
                                Pekerjaan
                                @if ($user->gender == 'L')
                                    Istri
                                @else
                                    Suami
                                @endif
                            </td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->pekerjaan_pasangan ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">Kode POS</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->kode_pos }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 120px;">No. NPWP</td>
                            <td style="vertical-align: top; padding: 0px 6px;">:</td>
                            <td style="vertical-align: top;">
                                {{ $user_detail->no_npwp }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div style="border: 1px solid black; border-bottom: none; padding: 10px; line-height: 1.4;">
        <strong>DATA PERMOHONAN</strong>
    </div>
    <div style="border: 1px solid black; padding: 10px; line-height: 1.4; margin-bottom: 6px;">
        <span>
            Dengan ini mengajukan permohonan kredit sebesar
            @rupiah($pinjaman->nominal)
        </span>
        <br>
        <span>
            Terbilang :
            {{ strtoupper($dana_terbilang) }}
        </span>
        <br>
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td style="vertical-align: top; width: 180px;">Tujuan pengajuan kredit</td>
                <td style="vertical-align: top; width: 10px;">
                    :
                </td>
                <td style="vertical-align: top; padding-left: 0px;">
                    {{ $pinjaman->tujuan }}
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 180px;">Usaha yang akan dibiayai</td>
                <td style="vertical-align: top; width: 10px;">:</td>
                <td style="padding-top: 4px;">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            {{-- Perdagangan --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->usaha == 'perdagangan' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">Perdagangan</td>

                            {{-- Pertanian --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->usaha == 'pertanian' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">Pertanian</td>

                            {{-- Jasa --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->usaha == 'jasa' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">Jasa</td>
                        </tr>
                        <tr>
                            {{-- Lainnya --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->usaha == 'lainnya' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="vertical-align: top;">
                                Lainnya:
                                @if ($pinjaman->usaha == 'lainnya')
                                    {{ $pinjaman->usaha_lainnya }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 180px;">Jangka waktu pinjaman</td>
                <td style="vertical-align: top; width: 10px;">
                    :
                </td>
                <td style="vertical-align: top; padding-left: 0px;">
                    {{ $pinjaman->jangka_waktu }} Tahun / {{ $pinjaman->jangka_waktu * 12 }} Bulan
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 180px;">Tipe angsuran</td>
                <td style="vertical-align: top; width: 10px;">:</td>
                <td style="padding-top: 4px;">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            {{-- Tiap Bulan --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->tipe_angsuran == 'bulanan' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px;">Tiap Bulan</td>

                            {{-- Sekaligus --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->tipe_angsuran == 'sekaligus' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px;">Sekaligus</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 180px;">Jenis agunan tambahan</td>
                <td style="vertical-align: top; width: 10px;">:</td>
                <td style="padding-top: 4px;">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            {{-- Kendaraan --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->jenis_agunan == 'kendaraan' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">Kendaraan</td>

                            {{-- Tanah & Bangunan --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->jenis_agunan == 'tanah_bangunan' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">Tanah & Bangunan</td>

                            {{-- Pekarangan --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->jenis_agunan == 'pekarangan' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">Pekarangan</td>
                        </tr>
                        <tr>
                            {{-- Sawah --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->jenis_agunan == 'sawah' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">Sawah</td>

                            {{-- Lainnya --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->jenis_agunan == 'lainnya' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="vertical-align: top;">
                                Lainnya:
                                @if ($pinjaman->pinjaman_agunan->jenis_agunan == 'lainnya')
                                    {{ $pinjaman->pinjaman_agunan->jenis_agunan_lainnya }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 180px;">Bukti kepemilikan agunan</td>
                <td style="vertical-align: top; width: 10px;">:</td>
                <td style="padding-top: 4px;">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            {{-- SHM --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->bukti_agunan == 'shm' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">SHM</td>

                            {{-- HGB --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->bukti_agunan == 'hgb' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">HGB</td>

                            {{-- HGU --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->bukti_agunan == 'hgu' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">HGU</td>
                        </tr>
                        <tr>
                            {{-- Hak Pakai --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->bukti_agunan == 'hak_pakai' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">Hak Pakai</td>

                            {{-- BPKB --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->bukti_agunan == 'bpkb' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">BPKB</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 180px;">Penguasaan bukti kepemilikan</td>
                <td style="vertical-align: top; width: 10px;">:</td>
                <td style="padding-top: 4px;">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            {{-- Milik Nasabah --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->bukti_kepemilikan == 'milik_nasabah' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">Milik Nasabah</td>

                            {{-- Bukan Milik Nasabah --}}
                            <td style="width:16px; vertical-align: top;">
                                <img src="{{ public_path('storage/uploads/asset/' . ($pinjaman->pinjaman_agunan->bukti_kepemilikan == 'bukan_milik_nasabah' ? 'check-square-regular.svg' : 'square-regular.svg')) }}"
                                    style="height:16px;">
                            </td>
                            <td style="width:140px; vertical-align: top;">Bukan Milik Nasabah</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div style="border: 1px solid black; border-bottom: none; padding: 10px; line-height: 1.4;">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 40px; vertical-align: top;">
                    <strong>A.</strong>
                </td>

                {{-- Kredit konsumtif lainya --}}
                <td style="width:16px; vertical-align: top;">
                    <img src="{{ public_path('storage/uploads/asset/square-regular.svg') }}" style="height:16px;">
                </td>
                <td style="width: 204px; vertical-align: top;">Kredit konsumtif lainya</td>

                {{-- Kredit potong gaji --}}
                <td style="width:16px; vertical-align: top;">
                    <img src="{{ public_path('storage/uploads/asset/check-square-regular.svg') }}"
                        style="height:16px;">
                </td>
                <td style="width: 204px; vertical-align: top;">Kredit potong gaji</td>
            </tr>
        </table>
    </div>
    <div style="border: 1px solid black; padding: 10px; line-height: 1.4; margin-bottom: 6px;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td style="vertical-align: top; width: 40px;">&nbsp;</td>
                <td style="vertical-align: top; width: 20px;">a.</td>
                <td style="vertical-align: top; width: 200px;">
                    Dinas/instansi tempat bekerja
                </td>
                <td style="vertical-align: top; width: 10px;">:</td>
                <td style="vertical-align: top;">
                    {{ $pinjaman->tempat_kerja }}
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 40px;">&nbsp;</td>
                <td style="vertical-align: top; width: 20px;">b.</td>
                <td style="vertical-align: top; width: 200px;">
                    Jabatan terakhir
                </td>
                <td style="vertical-align: top; width: 10px;">:</td>
                <td style="vertical-align: top;">
                    {{ $pinjaman->jabatan_terakhir }}
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 40px;">&nbsp;</td>
                <td style="vertical-align: top; width: 20px;">c.</td>
                <td style="vertical-align: top; width: 200px;">
                    Lama bekerja pada dinas/instansi
                </td>
                <td style="vertical-align: top; width: 10px;">:</td>
                <td style="vertical-align: top;">
                    {{ $pinjaman->lama_kerja }} Tahun
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 40px;">&nbsp;</td>
                <td style="vertical-align: top; width: 20px;">d.</td>
                <td style="vertical-align: top; width: 200px;">
                    Pedapatan kotor dalam 1 bulan
                </td>
                <td style="vertical-align: top; width: 10px;">:</td>
                <td style="vertical-align: top;">
                    @rupiah($pinjaman->pendapatan_kotor)
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 40px;">&nbsp;</td>
                <td style="vertical-align: top; width: 20px;">d.</td>
                <td style="vertical-align: top; width: 200px;">
                    Pedapatan bersih yang diterima
                </td>
                <td style="vertical-align: top; width: 10px;">:</td>
                <td style="vertical-align: top;">
                    @rupiah($pinjaman->pendapatan_bersih)
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
