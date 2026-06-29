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

        .hr {
            margin-bottom: 10px;
            margin-top: 10px;
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
    <div style="font-size: 14px; font-weight:bold; text-align: center; margin: 5px;">
        <u>PERJANJIAN KREDIT UANG</u>
    </div>
    <div style="font-size: 14px; text-align: center; margin: 5px;">
        Nomor: {{ $pinjaman->kode }}
    </div>
    <br>
    <div style="margin-bottom: 5px;">
        Pada hari ini, .... tanggal ...., bulan ...., tahun ...., (....-....-.....) bertempat di Slawi, yang
        bertanda tangan dibawah ini:
    </div>
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 5px;">
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
    <div style="margin-bottom: 5px;">
        Dalam hal ini bertindak dan atas nama pengurus Koperasi Karyawan Bhamada Slawi, selanjutnya disebut Pihak
        Pertama.
    </div>
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
    <br>
    <div style="margin-bottom: 10px;">
        Dalam hal ini bertindak sebagai Pemohon Kredit, selanjutnya disebut Pihak Kedua.
    </div>
    <div>
        Pihak Pertama dan Pihak Kedua secara bersama-sama mengadakan perjanjian kredit uang dengan ketentuan
        sebagai berikut:
    </div>
    <br>
    <div style="font-size: 14px; font-weight:bold; text-align: center;">
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
    <br>
    <div style="font-size: 14px; font-weight:bold; text-align: center;">
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
</body>

</html>
