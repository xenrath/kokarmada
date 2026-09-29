<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-justify {
            text-align: justify;
        }

        .font-bold {
            font-weight: bold;
        }

        .valign-top {
            vertical-align: top;
        }

        .valign-middle {
            vertical-align: middle;
        }

        .w-1cm {
            width: 1cm;
            white-space: nowrap;
        }

        .w-3cm {
            width: 3cm;
        }

        .w-4cm {
            width: 4cm;
        }

        .w-15 {
            width: 15%;
        }

        .w-70 {
            width: 70%;
        }

        .w-50 {
            width: 50%;
        }

        .mb-8 {
            margin-bottom: 8pt;
        }

        .mb-12 {
            margin-bottom: 12pt;
        }

        .lh-2 {
            line-height: 2;
        }

        .signature-space {
            height: 75px;
        }
    </style>
</head>

<body>

    {{-- KOP SURAT --}}
    <table class="mb-12">
        <tr>
            <td class="w-15 text-left valign-middle">
                <img src="{{ public_path('storage/uploads/asset/logo-koperasi.png') }}" width="80">
            </td>

            <td class="w-70 text-center valign-middle">
                <div class="font-bold">
                    KOPERASI KARYAWAN BHAMADA SLAWI
                </div>

                <div class="font-bold">
                    (KOPKARMADA SLAWI)
                </div>

                <div>
                    Jl. Cut Nyak Dhien No. 16 Kalisapu-Slawi,
                    Kabupaten Tegal
                </div>

                <div>
                    Email:
                    <u>kopkarmada123@gmail.com</u>
                </div>
            </td>

            <td class="w-15 text-right valign-middle">
                <img src="{{ public_path('storage/uploads/asset/logo-sm.png') }}" width="80">
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <hr>
            </td>
        </tr>
    </table>

    {{-- JUDUL --}}
    <p class="font-bold text-center lh-2">
        <u>PERJANJIAN KREDIT UANG</u>
        <br>
        Nomor: {{ $loan->code }}
    </p>

    <p>
        Pada hari ini,
        {{ now()->translatedFormat('l') }},
        tanggal
        {{ now()->translatedFormat('d') }},
        bulan
        {{ now()->translatedFormat('F') }},
        tahun
        {{ now()->translatedFormat('Y') }},
        bertempat di Slawi, yang bertanda tangan di bawah ini:
    </p>

    {{-- PIHAK PERTAMA --}}
    <table class="mb-8">
        <tr>
            <td class="valign-top w-1cm">1.</td>
            <td class="valign-top w-3cm">Nama</td>
            <td class="valign-top text-center w-1cm">:</td>
            <td class="valign-top">
                {{ $chairman->name }}
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">Jabatan</td>
            <td class="valign-top text-center">:</td>
            <td class="valign-top">
                Ketua Koperasi Karyawan Bhamada Slawi
            </td>
        </tr>

        @if ($chairman->nipy)
            <tr>
                <td></td>
                <td class="valign-top">NIPY</td>
                <td class="valign-top text-center">:</td>
                <td class="valign-top">
                    {{ $chairman->nipy }}
                </td>
            </tr>
        @endif
    </table>

    <p>
        Dalam hal ini bertindak dan atas nama pengurus
        Koperasi Karyawan Bhamada Slawi, selanjutnya disebut
        <strong>Pihak Pertama</strong>.
    </p>

    {{-- PIHAK KEDUA --}}
    <table class="mb-8">
        <tr>
            <td class="valign-top w-1cm">2.</td>
            <td class="valign-top w-3cm">Nama</td>
            <td class="valign-top text-center w-1cm">:</td>
            <td class="valign-top">
                {{ $loan->user->name }}
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">Pekerjaan</td>
            <td class="valign-top text-center">:</td>
            <td class="valign-top">
                {{ $loan->position ?: '-' }}
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">Alamat Rumah</td>
            <td class="valign-top text-center">:</td>
            <td class="valign-top">
                {{ $loan->user->memberProfile->address ?? '-' }}
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">Jabatan</td>
            <td class="valign-top text-center">:</td>
            <td class="valign-top">
                Anggota Koperasi Karyawan Bhamada Slawi
            </td>
        </tr>
    </table>

    <p>
        Dalam hal ini bertindak sebagai Pemohon Kredit,
        selanjutnya disebut <strong>Pihak Kedua</strong>.
    </p>

    <p>
        Pihak Pertama dan Pihak Kedua secara bersama-sama
        mengadakan perjanjian kredit uang dengan ketentuan
        sebagai berikut:
    </p>

    {{-- PASAL 1 --}}
    <p class="font-bold text-center lh-2">
        PASAL 1<br>
        PINJAMAN
    </p>

    <p>
        Pihak Pertama menyetujui pemberian kredit kepada
        Pihak Kedua dengan rincian:
    </p>

    <table class="mb-8">
        <tr>
            <td class="valign-top w-1cm">a.</td>
            <td class="valign-top w-4cm">Jumlah Kredit</td>
            <td class="valign-top text-center w-1cm">:</td>
            <td class="valign-top">
                Rp{{ number_format($loan->approved_amount, 0, ',', '.') }}
            </td>
        </tr>

        <tr>
            <td class="valign-top">b.</td>
            <td class="valign-top">Terbilang</td>
            <td class="valign-top text-center">:</td>
            <td class="valign-top">
                {{ ucwords(\App\Helpers\NumberHelper::terbilang((int) $loan->approved_amount)) }}
                rupiah
            </td>
        </tr>

        <tr>
            <td class="valign-top">c.</td>
            <td class="valign-top">Jangka Waktu</td>
            <td class="valign-top text-center">:</td>
            <td class="valign-top">
                @if ($loan->repayment_type === 'monthly')
                    {{ $loan->term_months }} bulan
                @else
                    Lump sum {{ $loan->term_months }} bulan
                @endif
            </td>
        </tr>

        @if ($loan->repayment_type === 'monthly')
            <tr>
                <td class="valign-top">d.</td>
                <td class="valign-top">Angsuran per bulan</td>
                <td class="valign-top text-center">:</td>
                <td class="valign-top">
                    Rp{{ number_format($loan->monthly_installment, 0, ',', '.') }}
                </td>
            </tr>
        @endif
    </table>

    {{-- PASAL 2 --}}
    <p class="font-bold text-center lh-2">
        PASAL 2<br>
        ANGSURAN POKOK DAN JASA
    </p>

    <table class="mb-8">
        <tr>
            <td class="valign-top w-1cm">(1)</td>
            <td class="valign-top text-justify">
                Atas pinjaman sebagaimana disebut dalam Pasal 1,
                Pihak Kedua setuju untuk membayar jasa pinjaman
                sebesar {{ $loan->interest_rate }}% sesuai dengan
                ketentuan perhitungan jasa yang berlaku di
                KOPKARMADA.
            </td>
        </tr>

        <tr>
            <td class="valign-top">(2)</td>
            <td class="valign-top text-justify">
                Selain pembayaran jasa sebagaimana dimaksud pada
                ayat (1), Pihak Kedua wajib mengembalikan pokok
                pinjaman dan kewajiban lainnya sesuai ketentuan
                koperasi.
            </td>
        </tr>

        <tr>
            <td class="valign-top">(3)</td>
            <td class="valign-top text-justify">
                Untuk pembayaran secara angsuran bulanan,
                Pihak Kedua wajib melakukan pembayaran selama
                {{ $loan->term_months }} bulan sesuai jadwal
                angsuran yang ditetapkan koperasi.
            </td>
        </tr>

        <tr>
            <td class="valign-top">(4)</td>
            <td class="valign-top text-justify">
                Pembayaran angsuran dilakukan melalui mekanisme
                yang telah ditetapkan oleh koperasi.
            </td>
        </tr>
    </table>

    {{-- PASAL 3 --}}
    <p class="font-bold text-center lh-2">
        PASAL 3<br>
        HAK DAN KEWAJIBAN
    </p>

    <table class="mb-8">

        <tr>
            <td class="valign-top w-1cm">(1)</td>
            <td class="valign-top font-bold">
                Hak Pihak Pertama
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                a. Menyalurkan dan mengelola dana pinjaman
                kepada Pihak Kedua sesuai ketentuan perjanjian.
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                b. Menerima pengembalian pokok pinjaman beserta
                jasa sesuai jadwal dan ketentuan yang berlaku.
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                c. Memperoleh informasi yang benar dan lengkap
                mengenai penggunaan dana pinjaman.
            </td>
        </tr>

        <tr>
            <td class="valign-top">(2)</td>
            <td class="valign-top font-bold">
                Kewajiban Pihak Pertama
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                a. Menyerahkan dana pinjaman kepada Pihak Kedua
                sesuai jumlah yang diperjanjikan.
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                b. Memberikan informasi mengenai hak dan
                kewajiban Pihak Kedua.
            </td>
        </tr>

        <tr>
            <td class="valign-top">(3)</td>
            <td class="valign-top font-bold">
                Hak Pihak Kedua
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                a. Menerima dana pinjaman sesuai dengan jumlah
                dan ketentuan yang telah disepakati.
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                b. Memperoleh informasi mengenai pengelolaan
                dan pembayaran pinjaman.
            </td>
        </tr>

        <tr>
            <td class="valign-top">(4)</td>
            <td class="valign-top font-bold">
                Kewajiban Pihak Kedua
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                a. Menggunakan dana pinjaman sesuai tujuan
                pengajuan.
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                b. Mengembalikan seluruh pokok pinjaman beserta
                jasa tepat waktu.
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                c. Memberikan informasi yang diperlukan kepada
                Pihak Pertama.
            </td>
        </tr>

        <tr>
            <td></td>
            <td class="valign-top">
                d. Memberitahukan secara tertulis apabila terjadi
                perubahan alamat, pekerjaan, usaha, atau hal lain
                yang dapat memengaruhi pelaksanaan perjanjian.
            </td>
        </tr>

    </table>

    {{-- PASAL 4 --}}
    <p class="font-bold text-center lh-2">
        PASAL 4<br>
        KETENTUAN PENYELESAIAN KREDIT BERMASALAH
    </p>

    <table class="mb-8">
        <tr>
            <td class="valign-top w-1cm">(1)</td>
            <td class="valign-top text-justify">
                Apabila terjadi keterlambatan pembayaran selama
                lebih dari 3 (tiga) bulan berturut-turut, pinjaman
                dapat dinyatakan bermasalah dan diproses sesuai
                ketentuan internal koperasi.
            </td>
        </tr>

        <tr>
            <td class="valign-top">(2)</td>
            <td class="valign-top text-justify">
                Pihak Pertama berhak melakukan langkah
                penyelesaian pinjaman bermasalah sesuai ketentuan
                yang berlaku.
            </td>
        </tr>

        <tr>
            <td class="valign-top">(3)</td>
            <td class="valign-top text-justify">
                Dalam hal Pihak Kedua meninggal dunia, kewajiban
                pinjaman diselesaikan sesuai ketentuan koperasi
                dan peraturan perundang-undangan yang berlaku.
            </td>
        </tr>
    </table>

    {{-- PASAL 5 --}}
    <p class="font-bold text-center lh-2">
        PASAL 5<br>
        KEPATUHAN PEMINJAM
    </p>

    <table class="mb-8">
        <tr>
            <td class="valign-top w-1cm">(1)</td>
            <td class="valign-top text-justify">
                Pihak Kedua bersedia tunduk kepada ketentuan
                koperasi yang berkaitan dengan pinjaman dan
                pelaksanaannya.
            </td>
        </tr>

        <tr>
            <td class="valign-top">(2)</td>
            <td class="valign-top text-justify">
                Segala perubahan terhadap isi perjanjian ini hanya
                dapat dilakukan atas persetujuan tertulis kedua
                belah pihak.
            </td>
        </tr>

        <tr>
            <td class="valign-top">(3)</td>
            <td class="valign-top text-justify">
                Hal-hal yang belum diatur dalam perjanjian ini
                mengikuti ketentuan Anggaran Dasar, Anggaran
                Rumah Tangga koperasi, dan ketentuan lain yang
                berlaku.
            </td>
        </tr>
    </table>

    {{-- PASAL 6 --}}
    <p class="font-bold text-center lh-2">
        PASAL 6<br>
        PERSELISIHAN
    </p>

    <p>
        Apabila terjadi perselisihan dalam pelaksanaan
        perjanjian ini, Pihak Pertama dan Pihak Kedua terlebih
        dahulu menyelesaikannya secara musyawarah.
    </p>

    <p>
        Demikian Perjanjian Kredit ini telah dibaca dan dipahami
        oleh Pihak Pertama dan Pihak Kedua untuk kemudian
        ditandatangani dalam 2 (dua) rangkap yang masing-masing
        mempunyai kekuatan hukum yang sama.
    </p>

    <p class="text-right">
        Slawi, {{ now()->translatedFormat('d F Y') }}
    </p>

    {{-- TANDA TANGAN --}}
    <table>
        <tr>
            <td class="w-50 text-center valign-top">
                <div>
                    <strong>Pihak Pertama</strong>
                    <br>
                    Ketua Koperasi Karyawan Bhamada Slawi

                    <div class="signature-space"></div>

                    <strong>{{ $chairman->name }}</strong>
                </div>
            </td>

            <td class="w-50 text-center valign-top">
                <div>
                    <strong>Pihak Kedua</strong>
                    <br>
                    Pemohon Kredit

                    <div class="signature-space"></div>

                    <strong>{{ $loan->user->name }}</strong>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>
