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

        .page-break {
            page-break-after: always;
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
    <div
        style="
    border-top: 1px solid black;
    border-left: 1px solid black;
    border-right: 1px solid black;
    border-bottom: none;
    padding: 10px;
    line-height: 1.4;
">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td width="15%" style="text-align: left;">
                    <img src="{{ public_path('storage/uploads/asset/logo-sm.png') }}" width="70">
                </td>
                <td width="70%" align="center" valign="middle">
                    <div style="line-height: 1.4;">
                        <div style="font-size:14px; font-weight:bold; text-align: center;">
                            KOPERASI KARYAWAN BHAMADA SLAWI
                        </div>
                        <div style="font-size:14px; text-align: center;">
                            Jl. Cut Nyak Dhien No. 16, Kalisapu, Slawi, Kabupaten Tegal, Jawa Tengah
                        </div>
                        <div style="font-size:14px; text-align: center;">
                            <u>kopkarmada123@gmail.com</u>, Telp. (+62) 85801162116
                        </div>
                    </div>
                </td>
                <td width="15%" style="text-align: right;">
                    <img src="{{ public_path('storage/uploads/asset/logo-sm.png') }}" width="70">
                </td>
            </tr>
        </table>
    </div>
    <div style="border: 1px solid black; padding: 10px; line-height: 1.4; margin-bottom: 6px;">
        <div style="font-size: 14px; font-weight:bold; text-align: center;">
            <u>SURAT PERSETUJUAN KREDIT</u>
        </div>
        <div style="font-size: 14px; text-align: center;">
            Nomor:
            {{ $pinjaman->kode }}
        </div>
        <br>
        <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
            <tr>
                <td colspan="4">
                    {{-- Berdasarkan hasil analisa atas permohonan Kredit Saudara nomor :
                    <br>
                    dan dengan mempertimbangkan terhadap segala aspek yang menyangkut kehati-hatian koperasi dalam
                    menyalurkan
                    kredit serta rencana pengembalian fasilitas pinjaman yang diberikan maka dengan ini KOPERASI
                    KARYAWAN
                    BHAMADA (KOPKARMADA) melalui komite kredit memberikan persetujuan kredit dengan data sebagai berikut
                    : --}}
                    Berdasarkan hasil analisis atas permohonan kredit Saudara dengan nomor :
                    <br>
                    serta dengan mempertimbangkan prinsip kehati-hatian koperasi dalam penyaluran kredit dan kemampuan
                    pengembalian pinjaman, maka Koperasi Karyawan Bhamada (KOPKARMADA) melalui Komite Kredit menyetujui
                    permohonan kredit dengan ketentuan sebagai berikut:
                </td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="2">Nama Debitur</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    <strong>{{ $user->nama }}</strong>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="2">Nomor Telepon/HP</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    <strong>{{ $user->telp }}</strong>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="2">Jabatan</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    <strong>{{ $pinjaman->jabatan_terakhir }}</strong>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="2">Alamat</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    <strong>{{ $user_detail->alamat }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4">
                    {{-- Menyetujui Permohonan kredit saudara dengan ketentuan sebagai berikut : --}}
                    Dengan ini menyetujui permohonan kredit Saudara dengan ketentuan sebagai berikut:
                </td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="2">Jenis Kredit</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    Kredit Konsumtif
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="2">Plafon Kredit</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    @rupiah($pinjaman->nominal)
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="2">Jangka Waktu</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    {{ $pinjaman->jangka_waktu * 12 }} Bulan
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="2">Bunga Pinjaman</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    {{ number_format($pengaturan->bunga_pinjaman / 12, 2) }}% per bulan (flat)
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="2">Biaya Administrasi</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    1% (dibebankan satu kali pada saat pencairan kredit)
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="2">Agunan</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top; width: 150px;">Jenis Agunan</td>
                <td style="vertical-align: top; width: 40px; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    Surat Kuasa Pemotongan Gaji
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top;">Dikeluarkan Oleh</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    Universitas Bhamada Slawi
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top;">Pemberi Kuasa</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    {{ $user->nama }}
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top;">Bentuk Surat</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    Surat Kuasa Pemotongan Gaji Pegawai
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top;">Ditandatangani Oleh</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    Nurlaela, M.Si
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top;">Jenis Kuasa</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    Pemotongan Gaji
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top;">Penerima Kuasa</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top;">Institusi/Lembaga</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    YAYASAN PENDIDIKAN TRI SANJA HUSADA
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top;">Nama Bendaharawan</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    Nurlaela, M.Si
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top;">Alamat Instansi</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    Jl. Cut Nyak Dhien No. 16, Kalisapu, Slawi, Kabupaten Tegal, Jawa Tengah.
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 10px;">-</td>
                <td style="vertical-align: top;">Besar Angsuran per bulan</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    @php
                        $bunga_pinjaman = $pengaturan->bunga_pinjaman / 100;
                        $bunga = $pinjaman->nominal_disetujui * ($bunga_pinjaman * $pinjaman->jangka_waktu);
                        $nominal_disetujui = $pinjaman->nominal_disetujui + $bunga;
                    @endphp
                    @rupiah($nominal_disetujui / (12 * $pinjaman->jangka_waktu))
                </td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4">
                    {{-- Demikian Syarat persetujuan kredit ini untuk dapat diterima dan dapat segera direalisasikan dan
                    seluruh kewajiban yang berkaitan dengan pencairan kredit diatas dapat dibayar segera sebelum kredit
                    ini dicairkan atau dapat didebet dari pencairan kredit tersebut diatas. --}}
                    {{-- Demikian surat persetujuan kredit ini dibuat untuk dipergunakan sebagaimana mestinya. Seluruh
                    kewajiban yang berkaitan dengan proses pencairan kredit wajib diselesaikan terlebih dahulu sebelum
                    kredit dicairkan atau dapat diperhitungkan langsung dari hasil pencairan kredit sesuai ketentuan
                    yang berlaku di KOPKARMADA. --}}
                    Demikian Surat Persetujuan Kredit ini dibuat dan disampaikan kepada Saudara. Dengan diterbitkannya
                    surat ini, Saudara dinyatakan telah memperoleh persetujuan kredit sesuai ketentuan yang tercantum di
                    atas. Seluruh proses pencairan kredit akan dilaksanakan setelah seluruh persyaratan administrasi dan
                    ketentuan yang berlaku di KOPKARMADA dipenuhi.
                </td>
            </tr>
        </table>
        <br>
        <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
            <tr>
                <td colspan="2">
                    Slawi, 17 Juni 2026
                    <br>
                    KOPERASI KARYAWAN BHAMADA
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <div style="display: inline-block; text-align: center;">
                        Petugas Administrasi Kredit,
                        <br><br><br><br>
                        <strong>{{ $sekretaris->nama }}</strong>
                    </div>
                </td>
                <td style="text-align: right;">
                    <div style="display: inline-block; text-align: center;">
                        Menerima dan Menyetujui,
                        <br><br><br><br>
                        <strong>{{ $user->nama }}</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>
    <!-- Page Break -->

    <div
        style="
    border-top: 1px solid black;
    border-left: 1px solid black;
    border-right: 1px solid black;
    border-bottom: none;
    padding: 10px;
    line-height: 1.4;
">
        <div style="font-size: 14px; font-weight:bold; text-align: center;">
            REALISASI PINJAMAN
        </div>
    </div>
    <div style="border: 1px solid black; padding: 10px; line-height: 1.4; margin-bottom: 6px;">
        <div style="font-size: 14px; font-weight:bold; text-align: right;">
            NO SPK : {{ $pinjaman->kode }}
        </div>
        <br>
        <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
            <tr>
                <td style="vertical-align: top; width: 110px;">
                    <strong>NAMA</strong>
                </td>
                <td style="vertical-align: top; width: 40px; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    <strong>{{ $user->nama }}</strong>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 110px;">
                    <strong>NO. REKENING</strong>
                </td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">
                    ({{ $user_detail->bank_nama }})
                    <strong>{{ $user_detail->bank_rekening }}</strong>
                </td>
            </tr>
        </table>
        <br>
        <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
            <tr>
                <td style="vertical-align: top;" colspan="6">
                    <strong>REALISASI</strong>
                </td>
                <td style="vertical-align: top; text-align: right;">
                    <strong>Rp</strong>
                </td>
                <td style="vertical-align: top; text-align: right;">
                    <strong>@rupiah_x($pinjaman->nominal_disetujui)</strong>
                </td>
                <td style="vertical-align: top;">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="9">
                    POTONGAN
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Admin</td>
                <td style="vertical-align: top;">1%</td>
                <td style="vertical-align: top;">&nbsp;</td>
                <td style="vertical-align: top; width: 10px;">Rp</td>
                <td style="vertical-align: top; text-align: right;">
                    @rupiah_x($pinjaman->nominal_disetujui / 100)
                </td>
                <td style="vertical-align: top;">&nbsp;</td>
                <td style="vertical-align: top; width: 10px;">&nbsp;</td>
                <td style="vertical-align: top;">&nbsp;</td>
                <td style="vertical-align: top; width: 20px;">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Pinjaman Sebelumnya</td>
                <td style="vertical-align: top;">&nbsp;</td>
                <td style="vertical-align: top;">&nbsp;</td>
                <td style="vertical-align: top;">Rp</td>
                <td style="vertical-align: top; text-align: right;">-</td>
                <td style="vertical-align: top;" colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Lain-lain</td>
                <td style="vertical-align: top;">Admin Bank</td>
                <td style="vertical-align: top;">&nbsp;</td>
                <td style="vertical-align: top;">Rp</td>
                <td style="vertical-align: top; text-align: right;">
                    @rupiah_x(2500)
                </td>
                <td style="vertical-align: top;" colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="6">Jumlah Potongan / Biaya</td>
                <td style="vertical-align: top;">
                    <strong>Rp</strong>
                </td>
                <td style="vertical-align: top; text-align: right;">
                    <strong>@rupiah_x($pinjaman->nominal_disetujui / 100 + 2500)</strong>
                </td>
                <td style="vertical-align: top;">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="6">&nbsp;</td>
                <td colspan="2">
                    <hr>
                </td>
                <td style="padding: 0px 5px;">
                    <hr>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;" colspan="6">
                    <strong>Jumlah yang diterima</strong>
                </td>
                <td style="vertical-align: top;">
                    <strong>Rp</strong>
                </td>
                <td style="vertical-align: top; text-align: right;">
                    @php
                        $potongan = $pinjaman->nominal_disetujui / 100 + 2500;
                    @endphp
                    <strong>@rupiah_x($pinjaman->nominal_disetujui - $potongan)</strong>
                </td>
                <td style="vertical-align: top;">&nbsp;</td>
            </tr>
        </table>
        <br><br><br>
        <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
            <tr>
                <td style="text-align: left;">
                    <div style="display: inline-block; text-align: center;">
                        Peminjam
                        <br><br><br><br>
                        {{ $user->nama }}
                        <br>
                        &nbsp;
                    </div>
                </td>
                <td style="text-align: right;">
                    <div style="display: inline-block; text-align: center;">
                        KOPERASI KARYAWAN BHAMADA
                        <br><br><br><br>
                        <u>{{ $sekretaris->nama }}</u>
                        <br>
                        Bendahara KOPKARMADA
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
