<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Perjanjian Kredit Uang</title>
    <style>
        * {
            font-family: 'Times New Roman', Times, serif;
            text-align: justify;
            text-justify: inter-word;
        }

        body {
            font-size: 14px;
            line-height: 1.5;
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
            font-size: 14px;
            font-weight: bold;
            display: block;
            text-align: center;
            text-justify: none;
            margin-bottom: 2px;
        }

        .header .h2 {
            font-size: 14px;
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
            font-size: 14px;
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
            font-size: 14px;
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
    <div style="padding: 10px; line-height: 1.4;">
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
                        <div style="font-size:14px; font-weight:bold; text-align: center;">
                            (KOPKARMADA SLAWI)
                        </div>
                        <div style="font-size:14px; text-align: center;">
                            Alamat: Jl. Cut Nyak Dhien No. 16 Kalisapu-Slawi, Kab. Tegal
                        </div>
                        <div style="font-size:14px; text-align: center;">
                            Email: <u>kopkarmada123@gmail.com</u>
                        </div>
                    </div>
                </td>
                <td width="15%" style="text-align: right;">
                    <img src="{{ public_path('storage/uploads/asset/logo-sm.png') }}" width="70">
                </td>
            </tr>
        </table>
    </div>
    <hr class="hr">
    <br>
    <div style="font-size: 14px; font-weight:bold; text-align: center; line-height: 1.5;">
        <u>PERJANJIAN KREDIT UANG</u>
    </div>
    <div style="font-size: 14px; text-align: center; line-height: 1.5;">
        Nomor: {{ $pinjaman->kode }}
    </div>
    <br>
    <p>
        Pada hari ini, .... tanggal ...., bulan ...., tahun ...., (....-....-.....) bertempat di Slawi, yang
        bertanda tangan dibawah ini:
    </p>
    <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
        <tr>
            <td style="vertical-align: top; width: 20px;">1.</td>
            <td style="vertical-align: top; width: 100px;">Nama</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">{{ $user->nama }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; width: 20px;">&nbsp;</td>
            <td style="vertical-align: top;">Jabatan</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">{{ $user->jabatan_terakhir }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; width: 20px;">&nbsp;</td>
            <td style="vertical-align: top;">NIPY</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">1987.09.09.22.170</td>
        </tr>
    </table>
    <p>
        Dalam hal ini bertindak dan atas nama pengurus Koperasi Karyawan Bhamada Slawi, selanjutnya disebut Pihak
        Pertama.
    </p>
    <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
        <tr>
            <td style="vertical-align: top; width: 20px;">2.</td>
            <td style="vertical-align: top; width: 100px;">Nama</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">{{ $user->nama }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; width: 20px;">&nbsp;</td>
            <td style="vertical-align: top;">Pekerjaan</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">{{ $user->jabatan_terakhir }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; width: 20px;">&nbsp;</td>
            <td style="vertical-align: top;">Alamat Rumah</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">1987.09.09.22.170</td>
        </tr>
        <tr>
            <td style="vertical-align: top; width: 20px;">&nbsp;</td>
            <td style="vertical-align: top;">Jabatan</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">Anggota Koperasi Karyawan Bhamada Slawi</td>
        </tr>
    </table>
    <p>
        Dalam hal ini bertindak sebagai Pemohon Kredit, selanjutnya disebut Pihak Kedua.
    </p>
    <p>
        Pihak Pertama dan Pihak Kedua secara bersama-sama mengadakan perjanjian kredit uang dengan ketentuan
        sebagai berikut:
    </p>
    <div style="font-size: 14px; font-weight:bold; text-align: center; line-height: 1.5;">
        PASAL 1
        <br>
        PINJAMAN
    </div>
    <p>
        Pihak pertama menyetujui kredit uang kepada pihak kedua dengan rincian:
    </p>
    <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
        <tr>
            <td style="vertical-align: top; width: 20px;">a.</td>
            <td style="vertical-align: top; width: 100px;">Jumlah Kredit</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">{{ $user->nama }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; width: 20px;">b.</td>
            <td style="vertical-align: top;">Terbilang</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">{{ $user->jabatan_terakhir }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; width: 20px;">c.</td>
            <td style="vertical-align: top;">Jumlah Angsuran</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">1987.09.09.22.170</td>
        </tr>
    </table>
    <div style="font-size: 14px; font-weight:bold; text-align: center; line-height: 1.5;">
        PASAL 2
        <br>
        ANGSURAN POKOK & JASA
    </div>
    <p>
        Pihak pertama menyetujui kredit uang kepada pihak kedua dengan rincian:
    </p>
    <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
        <tr>
            <td style="vertical-align: top; width: 20px;">a.</td>
            <td style="vertical-align: top; width: 100px;">Jumlah Kredit</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">{{ $user->nama }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; width: 20px;">b.</td>
            <td style="vertical-align: top;">Terbilang</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">{{ $user->jabatan_terakhir }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; width: 20px;">c.</td>
            <td style="vertical-align: top;">Jumlah Angsuran</td>
            <td style="vertical-align: top; text-align: center; width: 20px;">:</td>
            <td style="vertical-align: top;">1987.09.09.22.170</td>
        </tr>
    </table>

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
