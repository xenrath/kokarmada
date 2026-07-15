<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Perjanjian Kredit Uang</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.5;
        }

        p {
            margin: 0 0 8pt 0;
            text-align: justify;
        }

        /* line-height */
        .lh-2 {
            line-height: 2;
        }

        /* margin-bottom */
        .mb-8 {
            margin-bottom: 8pt;
        }

        .mb-12 {
            margin-bottom: 12pt;
        }

        /* text-align */
        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* font-weight */
        .font-bold {
            font-weight: bold;
        }

        /* vertical-align */
        .valign-top {
            vertical-align: top;
        }

        .valign-middle {
            vertical-align: middle;
        }

        /* text-justify */
        .text-justify {
            text-align: justify;
        }

        /* widths */
        .w-1cm {
            width: 1cm;
        }

        .w-3cm {
            width: 3cm;
        }

        .w-4cm {
            width: 4cm;
        }

        .w-20px {
            width: 20px;
        }

        .w-50 {
            width: 50%;
        }

        .w-15 {
            width: 15%;
        }

        .w-70 {
            width: 70%;
        }

        /* display */
        .d-inline-block {
            display: inline-block;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <!-- Kop Surat -->
    <table width="100%" cellspacing="0" cellpadding="0" class="mb-12">
        <tr>
            <td class="w-15 text-left valign-middle">
                <img src="{{ public_path('storage/uploads/asset/logo-koperasi.png') }}" width="90">
            </td>
            <td class="w-70 text-center valign-middle">
                <div class="font-bold text-center">KOPERASI KARYAWAN BHAMADA SLAWI</div>
                <div class="font-bold text-center">(KOPKARMADA SLAWI)</div>
                <div class="text-center">Alamat: Jl. Cut Nyak Dhien No. 16 Kalisapu-Slawi, Kab. Tegal</div>
                <div class="text-center">Email: <u>kopkarmada123@gmail.com</u></div>
            </td>
            <td class="w-15 text-right valign-middle">
                <img src="{{ public_path('storage/uploads/asset/logo-sm.png') }}" width="90">
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <hr>
            </td>
        </tr>
    </table>

    <!-- Judul -->
    <p class="font-bold text-center lh-2">
        <u>PERJANJIAN KREDIT UANG</u>
        <br>
        Nomor: {{ $pinjaman->kode }}
    </p>

    <p>
        Pada hari ini,
        {{ Carbon\Carbon::now()->translatedFormat('l') }}
        tanggal
        {{ Carbon\Carbon::now()->translatedFormat('d') }},
        bulan
        {{ Carbon\Carbon::now()->translatedFormat('F') }},
        tahun
        {{ Carbon\Carbon::now()->translatedFormat('Y') }},
        ({{ Carbon\Carbon::now()->translatedFormat('d-m-Y') }})
        bertempat di Slawi, yang bertanda tangan dibawah ini:</p>

    <!-- Pihak Pertama -->
    <table width="100%" cellspacing="0" cellpadding="0" class="mb-8">
        <tr>
            <td class="valign-top text-justify w-1cm">1.</td>
            <td class="valign-top text-justify w-3cm">Nama</td>
            <td class="valign-top text-justify text-center w-1cm">:</td>
            <td class="valign-top text-justify">{{ $ketua->nama }}</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">&nbsp;</td>
            <td class="valign-top text-justify">Jabatan</td>
            <td class="valign-top text-justify text-center">:</td>
            <td class="valign-top text-justify">Ketua Koperasi Karyawan Bhamada Slawi</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">&nbsp;</td>
            <td class="valign-top text-justify">NIPY</td>
            <td class="valign-top text-justify text-center">:</td>
            <td class="valign-top text-justify">{{ $ketua->nipy }}</td>
        </tr>
    </table>
    <p>Dalam hal ini bertindak dan atas nama pengurus Koperasi Karyawan Bhamada Slawi, selanjutnya disebut Pihak
        Pertama.</p>

    <!-- Pihak Kedua -->
    <table width="100%" cellspacing="0" cellpadding="0" class="mb-8">
        <tr>
            <td class="valign-top text-justify w-1cm">2.</td>
            <td class="valign-top text-justify w-3cm">Nama</td>
            <td class="valign-top text-justify text-center w-1cm">:</td>
            <td class="valign-top text-justify">{{ $user->nama }}</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-20px">&nbsp;</td>
            <td class="valign-top text-justify">Pekerjaan</td>
            <td class="valign-top text-justify text-center w-20px">:</td>
            <td class="valign-top text-justify">{{ $user->user_detail->pekerjaan }}</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-20px">&nbsp;</td>
            <td class="valign-top text-justify">Alamat Rumah</td>
            <td class="valign-top text-justify text-center w-20px">:</td>
            <td class="valign-top text-justify">{{ $user->user_detail->alamat }}</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-20px">&nbsp;</td>
            <td class="valign-top text-justify">Jabatan</td>
            <td class="valign-top text-justify text-center w-20px">:</td>
            <td class="valign-top text-justify">Anggota Koperasi Karyawan Bhamada Slawi</td>
        </tr>
    </table>
    <p>Dalam hal ini bertindak sebagai Pemohon Kredit, selanjutnya disebut Pihak Kedua.</p>

    <p>Pihak Pertama dan Pihak Kedua secara bersama-sama mengadakan perjanjian kredit uang dengan ketentuan
        sebagai berikut:</p>

    <!-- Pasal 1 -->
    <p class="font-bold text-center lh-2">PASAL 1<br>PINJAMAN</p>
    <p>Pihak pertama menyetujui kredit uang kepada pihak kedua dengan rincian:</p>
    <table width="100%" cellspacing="0" cellpadding="0" class="mb-8">
        <tr>
            <td class="valign-top text-justify w-1cm">a.</td>
            <td class="valign-top text-justify w-4cm">Jumlah Kredit</td>
            <td class="valign-top text-justify text-center w-1cm">:</td>
            <td class="valign-top text-justify">@rupiah($pinjaman->nominal_disetujui)</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">b.</td>
            <td class="valign-top text-justify">Terbilang</td>
            <td class="valign-top text-justify text-center">:</td>
            <td class="valign-top text-justify">{{ ucwords($nominal_terbilang) }} Rupiah</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">c.</td>
            <td class="valign-top text-justify">Jumlah Angsuran</td>
            <td class="valign-top text-justify text-center">:</td>
            <td class="valign-top text-justify">1987.09.09.22.170</td>
        </tr>
    </table>

    <!-- Pasal 2 -->
    <p class="font-bold text-center lh-2">PASAL 2<br>ANGSURAN POKOK & JASA</p>
    <table width="100%" cellspacing="0" cellpadding="0" class="mb-8">
        <tr>
            <td class="valign-top text-justify w-1cm">(1)</td>
            <td class="valign-top text-justify">Atas pinjaman sebagaimana disebut dalam Pasal 1, Pihak kedua setuju
                untuk
                membayar jasa sebesar Rp. ……………… per bulan atau 0,67% dari pokok pinjaman per bulan, dengan sistem
                pembayaran jasa tetap;</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(2)</td>
            <td class="valign-top text-justify">Selain pembayaran jasa sebagaimana disebut dalam Pasal 2 ayat (1), Pihak
                kedua
                setuju mengembalikan jumlah pokok pinjaman berikut kewajiban-kewajiban lain yang ditetapkan oleh Pihak
                Pertama (antara lain: simpanan pokok dan/atau simpanan wajib);</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(3)</td>
            <td class="valign-top text-justify">Pihak Kedua setuju untuk melunasi pokok pinjaman berikut jasa dalam
                waktu
                ..... bulan (Rp. ........................./bulan) dan/atau dalam ..... kali Angsuran Pokok dan Jasa
                sebagaimana termuat dalam Daftar Angsuran terlampir;</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(4)</td>
            <td class="valign-top text-justify">Pembayaran Angsuran Pokok dan Jasa sebagaimana disebut dalam Pasal 2
                ayat
                (3) diatas dilakukan mulai tanggal ................... sampai dengan tanggal ......................</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(5)</td>
            <td class="valign-top text-justify">Pembayaran Angsuran Pokok dan Jasa sebagaimana disebut dalam Pasal 2
                ayat
                (3) diatas harus dibayarkan dengan cara POTONG GAJI</td>
        </tr>
    </table>

    <!-- Pasal 3 -->
    <p class="font-bold text-center lh-2">PASAL 3<br>HAK & KEWAJIBAN</p>
    <table width="100%" cellspacing="0" cellpadding="0" class="mb-8">
        <tr>
            <td class="valign-top text-justify">(1)</td>
            <td class="valign-top text-justify" colspan="2">Hak Pihak Pertama</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">a.</td>
            <td class="valign-top text-justify">Menyalurkan dan mengelola dana pinjaman kepada Pihak Kedua sesuai
                dengan ketentuan dan jangka waktu yang telah disepakati dalam perjanjian ini.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">b.</td>
            <td class="valign-top text-justify">Menerima pengembalian pinjaman pokok beserta jasa pinjaman dari Pihak
                Kedua sesuai dengan jadwal dan ketentuan sebagaimana diatur dalam Pasal 2.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">c.</td>
            <td class="valign-top text-justify">Memperoleh informasi yang benar, lengkap, dan berkelanjutan dari Pihak
                Kedua mengenai kondisi keuangan, pemanfaatan dana pinjaman, serta perkembangan usaha yang dibiayai.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(2)</td>
            <td class="valign-top text-justify" colspan="2">Kewajiban Pihak Pertama</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">a.</td>
            <td class="valign-top text-justify">Menyerahkan dana pinjaman kepada Pihak Kedua sebesar jumlah yang
                diperjanjikan.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">b.</td>
            <td class="valign-top text-justify">Memberikan informasi yang diperlukan kepada Pihak Kedua mengenai hak,
                kewajiban, serta ketentuan pengembalian pinjaman.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">c.</td>
            <td class="valign-top text-justify">Memberikan saran dan pendampingan sewaktu-waktu kepada Pihak Kedua
                agar penggunaan dana pinjaman sesuai dengan tujuan pengajuan kredit.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(3)</td>
            <td class="valign-top text-justify" colspan="2">Hak Pihak Kedua</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">a.</td>
            <td class="valign-top text-justify">Menerima dana pinjaman dari Pihak Pertama sesuai dengan jumlah dan
                ketentuan yang telah disepakati.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">b.</td>
            <td class="valign-top text-justify">Memperoleh informasi dan saran dari Pihak Pertama terkait pengelolaan
                serta
                pemanfaatan dana pinjaman.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">c.</td>
            <td class="valign-top text-justify">Menarik simpanan pokok dan/atau simpanan wajib sesuai dengan ketentuan
                yang berlaku di koperasi setelah seluruh kewajiban pinjaman kepada Pihak Pertama dipenuhi, sebagaimana
                diatur dalam Pasal 2.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(4)</td>
            <td class="valign-top text-justify" colspan="2">Kewajiban Pihak Kedua</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">a.</td>
            <td class="valign-top text-justify">Menggunakan dana pinjaman sesuai dengan tujuan pengajuan pinjaman
                sebagaimana tercantum dalam perjanjian ini.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">b.</td>
            <td class="valign-top text-justify">Mengembalikan seluruh jumlah pinjaman pokok beserta jasa pinjaman tepat
                waktu sesuai dengan ketentuan Pasal 2</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">c.</td>
            <td class="valign-top text-justify">Memberikan informasi keuangan dan informasi lain yang diperlukan kepada
                Pihak Pertama terkait pemanfaatan dana pinjaman dan perkembangan usaha.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify w-1cm">&nbsp;</td>
            <td class="valign-top text-justify w-1cm">d.</td>
            <td class="valign-top text-justify">Memberitahukan secara tertulis kepada Pihak Pertama apabila terjadi
                perubahan alamat, usaha, kepemilikan, atau hal lain yang dapat memengaruhi pelaksanaan perjanjian ini.
            </td>
        </tr>
    </table>

    <!-- Pasal 4 -->
    <p class="font-bold text-center lh-2">PASAL 4<br>KETENTUAN PENYELESAIAN KREDIT BERMASALAH</p>
    <table width="100%" cellspacing="0" cellpadding="0" class="mb-8">
        <tr>
            <td class="valign-top text-justify w-1cm">(1)</td>
            <td class="valign-top text-justify">Apabila terjadi keterlambatan pembayaran angsuran selama lebih dari 3
                (tiga)
                bulan berturut-turut, maka pinjaman dinyatakan macet dan akan diproses sesuai dengan ketentuan internal
                koperasi.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(2)</td>
            <td class="valign-top text-justify">Pihak Pertama berhak melakukan restrukturisasi pinjaman atau
                penyelesaian
                kredit bermasalah sesuai ketentuan dalam Peraturan Pihak Pertama dan peraturan perundang-undangan yang
                berlaku.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(3)</td>
            <td class="valign-top text-justify">Dalam hal Pihak Kedua meninggal dunia, maka kewajiban pinjaman dapat
                diselesaikan oleh ahli waris.</td>
        </tr>
    </table>

    <!-- Pasal 5 -->
    <p class="font-bold text-center lh-2">PASAL 5<br>KEPATUHAN PEMINJAM</p>
    <table width="100%" cellspacing="0" cellpadding="0" class="mb-8">
        <tr>
            <td class="valign-top text-justify w-1cm">(1)</td>
            <td class="valign-top text-justify">Pihak Kedua bersedia tunduk kepada semua peraturan dan kebiasaan
                mengenai
                kredit uang yang ada pada Pihak Pertama termasuk ketentuan yang tercantum pada Anggaran Dasar Pihak
                Pertama serta peraturan yang akan diadakan kelak meskipun tidak disebutkan disini secara terperinci.
            </td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(2)</td>
            <td class="valign-top text-justify">Segala perubahan terhadap isi perjanjian ini hanya dapat dilakukan atas
                persetujuan tertulis dari kedua belah pihak.</td>
        </tr>
        <tr>
            <td class="valign-top text-justify">(3)</td>
            <td class="valign-top text-justify">Hal-hal yang belum diatur dalam perjanjian ini akan mengikuti ketentuan
                Anggaran Dasar dan Anggaran Rumah Tangga Koperasi serta peraturan perundang-undangan yang berlaku.</td>
        </tr>
    </table>

    <!-- Pasal 6 -->
    <p class="font-bold text-center lh-2">PASAL 6<br>PERSELISIHAN</p>
    <p>Atas perjanjian ini beserta pelaksanaannya dan seluruh akibat hukumnya, Pihak Pertama dan Pihak Kedua sepakat
        untuk memilih domisili hukum yang umum dan tetap yaitu Kantor Universitas Bhamada Slawi.
        <br>
        Demikian Perjanjian ini telah dibaca dan dipahami oleh Pihak Pertama dan Pihak Kedua dan selanjutnya
        ditandatangani oleh kedua belah pihak, dibuat dalam 2 (dua) rangkap yang masing-masing memiliki kekuatan hukum
        yang sama.
    </p>

    <br><br>
    <p class="text-right">Slawi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>

    <!-- Tanda Tangan -->
    <table width="100%" cellspacing="0" cellpadding="0" class="mb-8">
        <tr>
            <td class="valign-top w-50 text-left">
                <div class="text-center d-inline-block">
                    Pihak Pertama
                    <br>
                    Ketua Koperasi Karyawan Bhamada Slawi
                    <br>
                    <br>
                    <br>
                    <br>
                    (........................................)
                </div>
            </td>
            <td class="valign-top w-50 text-right">
                <div class="text-center d-inline-block">
                    Pihak Kedua
                    <br>
                    Pemohon Kredit
                    <br>
                    <br>
                    <br>
                    <br>
                    (........................................)
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
