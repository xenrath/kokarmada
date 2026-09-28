<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Surat Persetujuan Kredit</title>

    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }

        body {
            margin: 0;
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .justify {
            text-align: justify;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .header-logo {
            width: 80px;
        }

        .title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }

        .row-label {
            width: 32%;
        }

        .separator {
            border-top: 1px solid #000;
        }

        .signature {
            margin-top: 45px;
        }
    </style>
</head>

<body>

    {{-- KOP SURAT --}}
    <div class="header">

        <table>

            <tr>

                <td width="15%">
                    <img src="{{ public_path('storage/uploads/asset/logo-koperasi.png') }}" class="header-logo">
                </td>

                <td width="70%" class="text-center">

                    <div class="bold">
                        KOPERASI KARYAWAN BHAMADA SLAWI
                    </div>

                    <div>
                        (KOPKARMADA SLAWI)
                    </div>

                    <div>
                        Jl. Cut Nyak Dhien No. 16 Kalisapu-Slawi, Kab. Tegal
                    </div>

                    <div>
                        Email:
                        <u>kopkarmada123@gmail.com</u>
                    </div>

                </td>

                <td width="15%" class="text-right">
                    <img src="{{ public_path('storage/uploads/asset/logo-sm.png') }}" class="header-logo">
                </td>

            </tr>

        </table>

    </div>


    {{-- JUDUL --}}
    <div class="title">
        <u>SURAT PERSETUJUAN KREDIT</u>
    </div>

    <div class="text-center">
        Nomor: {{ $loan->code }}
    </div>

    <br>


    {{-- PEMBUKA --}}
    <p class="justify">
        Berdasarkan hasil analisis atas permohonan kredit Saudara
        dengan nomor {{ $loan->code }}, serta dengan mempertimbangkan
        prinsip kehati-hatian koperasi dalam penyaluran kredit dan
        kemampuan pengembalian pinjaman, maka Koperasi Karyawan Bhamada
        (KOPKARMADA) melalui Komite Kredit menyetujui permohonan kredit
        dengan ketentuan sebagai berikut:
    </p>


    {{-- IDENTITAS DEBITUR --}}
    <table>

        <tr>
            <td class="row-label">Nama Debitur</td>
            <td width="5%">:</td>
            <td>
                <strong>{{ $loan->user->name }}</strong>
            </td>
        </tr>

        <tr>
            <td>Nomor Telepon/HP</td>
            <td>:</td>
            <td>
                <strong>{{ $loan->user->phone }}</strong>
            </td>
        </tr>

        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>
                <strong>{{ $loan->position }}</strong>
            </td>
        </tr>

        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>
                <strong>
                    {{ $loan->user->memberProfile?->address ?? '-' }}
                </strong>
            </td>
        </tr>

    </table>

    <br>


    <p>
        Dengan ini menyetujui permohonan kredit Saudara dengan
        ketentuan sebagai berikut:
    </p>


    {{-- DETAIL KREDIT --}}
    <table>

        <tr>
            <td class="row-label">
                Jenis Kredit
            </td>
            <td width="5%">:</td>
            <td>
                @if ($loan->purpose_category === 'business')
                    Kredit Usaha
                @elseif ($loan->purpose_category === 'consumer')
                    Kredit Konsumtif
                @else
                    {{ $loan->purpose_category }}
                @endif
            </td>
        </tr>

        <tr>
            <td>
                Plafon Kredit
            </td>
            <td>:</td>
            <td>
                Rp{{ number_format($loan->approved_amount, 0, ',', '.') }}
            </td>
        </tr>

        <tr>
            <td>
                Jangka Waktu
            </td>
            <td>:</td>
            <td>
                {{ $loan->term_months }} Bulan
            </td>
        </tr>

        <tr>
            <td>
                Bunga Pinjaman
            </td>
            <td>:</td>
            <td>
                {{ number_format($loan->interest_rate, 2, ',', '.') }}%
                per tahun
            </td>
        </tr>

        <tr>
            <td>
                Biaya Administrasi
            </td>
            <td>:</td>
            <td>
                1% dari plafon kredit
            </td>
        </tr>

        <tr>
            <td>
                Agunan
            </td>
            <td>:</td>
            <td>

                @if ($loan->collaterals->isEmpty())

                    Tidak ada
                @else
                    @foreach ($loan->collaterals as $collateral)
                        {{ $collateral->type }}

                        @if ($collateral->description)
                            - {{ $collateral->description }}
                        @endif

                        @if (!$loop->last)
                            <br>
                        @endif
                    @endforeach

                @endif

            </td>
        </tr>

        @if ($loan->repayment_type === 'monthly')
            <tr>
                <td>
                    Besar Angsuran per Bulan
                </td>

                <td>:</td>

                <td>
                    Rp{{ number_format($loan->monthly_installment, 0, ',', '.') }}
                </td>
            </tr>
        @endif

    </table>


    <br>


    <p class="justify">
        Demikian Surat Persetujuan Kredit ini dibuat dan disampaikan
        kepada Saudara. Dengan diterbitkannya surat ini, Saudara
        dinyatakan telah memperoleh persetujuan kredit sesuai ketentuan
        yang tercantum di atas. Seluruh proses pencairan kredit akan
        dilaksanakan setelah seluruh persyaratan administrasi dan
        ketentuan yang berlaku di KOPKARMADA dipenuhi.
    </p>


    {{-- TANDA TANGAN --}}
    <div class="signature">

        <table>

            <tr>

                <td width="50%">
                    Slawi, {{ now()->translatedFormat('d F Y') }}
                </td>

                <td width="50%" class="text-right">
                    KOPERASI KARYAWAN BHAMADA
                </td>

            </tr>

            <tr>

                <td>

                    Petugas Administrasi Kredit,

                    <br><br><br><br>

                    <strong>
                        {{ $secretary->name }}
                    </strong>

                </td>

                <td class="text-right">

                    Menerima dan Menyetujui,

                    <br><br><br><br>

                    <strong>
                        {{ $loan->user->name }}
                    </strong>

                </td>

            </tr>

        </table>

    </div>

</body>

</html>
