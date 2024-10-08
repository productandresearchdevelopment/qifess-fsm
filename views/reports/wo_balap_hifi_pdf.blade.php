<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
    @font-face {
        font-family: 'sans-serif';
    }

    @font-face {
        font-family: 'sans-serif';
        font-weight: 600;
    }

    @font-face {
        font-family: 'sans-serif';
        font-weight: bold;
    }

    body,
    table,
    td,
    div {
        font-family: sans-serif;
    }

    table {
        width: 100%;
        border-spacing: 0;
        border-collapse: collapse;
    }


    .page-break {
        page-break-after: always;
    }

    header {
        position: fixed;
        top: -10px;
        left: 80%;
        right: 0px;
        height: 30px;
    }

    footer {
        position: fixed;
        bottom: -60px;
        left: 70%;
        right: 0px;
        height: 50px;
    }


    .tableheadertitle {
        background: #444444;
        color: #FFFFFF;
        font-size: 10px;
    }

    .tableheadertitle td {
        width: 50%;
        padding: 3px;
        text-align: center;
    }

    .divsquare {
        border: 1px solid #444444;
        border-radius: 5px;
        padding: 3px 5px;
        margin-right: 20px;
    }

    .tableborder td {
        border: 1px solid #666666;
        height: 20px;
        padding: 3px 5px;
    }
</style>

<body>
    <table>
        <tr>
            <td>
                <img src="{{ public_path('images/logo_hifi.jpg') }}" style="height: 80px">
            </td>
            <td width="400" align="left">
                <div
                    style="background: #444444; text-align: center; padding: 5px 30px; color: #FFFFFF; border-bottom-left-radius: 10px;border-top-left-radius: 10px;">
                    <b>BERITA ACARA LAPANGAN</b> / FIELD REPORT
                </div>
                <div
                    style="margin: 20px 0px 0px 150px; text-align: left; border: 1px solid #333333; padding: 5px 10px; font-size: 10px;">
                    <b>NOMOR SERI FORM</b> / FORM SERIAL NUMBER : {{ $data->id }}
                </div>
            </td>
        </tr>
    </table>

    <div style="height: 30px;"></div>

    {{-- SECTION 1  --}}

    <table class="tableheadertitle" style="margin-bottom: 5px;">
        <tr>
            <td><b>Informasi Formulir BALAP</b> / Field Report Information</td>
            <td><b>Informasi Pelanggan</b> / Customer Information</td>
        </tr>
    </table>

    <table style="font-size: 10px;">
        <tr>
            <td width="50%">
                <table>
                    <tr>
                        <td width="150"> <b>Nomor</b> <br> <i>Number</i> </td>
                        <td width="10">:</td>
                        <td>
                            <div class="divsquare">{{ $data->no_wo ?: '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td> <b>Tanggal</b> <br> <i>Date</i> </td>
                        <td>:</td>
                        <td>
                            <div class="divsquare">

                                {{ $time_finish ? date('d/m/Y', strtotime($time_finish)) : '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td> <b>Vendor</b> <br> <i>Company</i> </td>
                        <td>:</td>
                        <td>
                            <div class="divsquare">{{ $data->fieldtech->vendor_name ?: '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td> <b>Teknisi</b> <br> <i>Technician</i> </td>
                        <td>:</td>
                        <td>
                            <div class="divsquare">{{ $data->fieldtech->name ?: '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td> <b>Waktu Mulai dan Selesai</b> <br> <i>Start and End Time</i> </td>
                        <td>:</td>
                        <td>
                            <div class="divsquare">
                                {{ $time_start ? date('d/m/Y H:i', strtotime($time_start)) : '-' }}
                                &nbsp; <b>To</b> &nbsp;
                                {{ $time_finish ? date('d/m/Y H:i', strtotime($time_finish)) : '-' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td width="150"> <b>ID Pelanggan</b> <br> <i>Customer ID</i> </td>
                        <td width="10">:</td>
                        <td>
                            <div class="divsquare">{{ $ispCustomerId ? : '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td> <b>Nama</b> <br> <i>Name</i> </td>
                        <td>:</td>
                        <td>
                            <div class="divsquare">{{ $data->site ? $data->site->name : '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td> <b>Alamat</b> <br> <i>Address</i> </td>
                        <td>:</td>
                        <td rowspan="2">
                            <div class="divsquare" style="height: 40px">
                                {{ $data->site ? substr($data->site->address, 0, 130) : '-' }}
                                {{ $data->site && strlen($data->site->address) > 130 ? '...' : '' }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td height="20">&nbsp;</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td> <b>Nomor Kontak</b> <br> <i>Contact Number</i> </td>
                        <td>:</td>
                        <td>
                            <div class="divsquare">{{ $data->site ? $data->site->pic_phone : '-' }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- SECTION 2  --}}

    <div style="height: 10px;"></div>

    <table class="tableheadertitle" style="margin-bottom: 5px;">
        <tr>
            <td><b>Tipe Formulir</b> / Formulir Type</td>
            <td><b>Profil Layanan</b> / Service Profile</td>
        </tr>
    </table>

    <table style="font-size: 10px;">
        <tr>
            <td width="25%" valign="top">
                <table>
                    <tr>
                        <td width="20">
                            @if (in_array($data->activity_id, [1, 9]))
                                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
                            @else
                                <img style="height: 16px;" src="{{ public_path('images/uncheck.jpg') }}">
                            @endif
                        </td>
                        <td><b>Pasang Baru</b> <br> <i>New Installation</i></td>
                    </tr>
                    <tr>
                        <td width="20">
                            @if (in_array($data->activity_id, [2]))
                                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
                            @else
                                <img style="height: 16px;" src="{{ public_path('images/uncheck.jpg') }}">
                            @endif
                        </td>
                        <td><b>Ganti Layanan</b> <br> <i>Service Exchange</i></td>
                    </tr>
                    <tr>
                        <td width="20">
                            @if (in_array($data->activity_id, []))
                                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
                            @else
                                <img style="height: 16px;" src="{{ public_path('images/uncheck.jpg') }}">
                            @endif
                        </td>
                        <td><b>Tambahan Layanan</b> <br> <i>Service Addition</i></td>
                    </tr>
                </table>
            </td>
            <td width="25%" valign="top">
                <table>
                    <tr>
                        <td width="20">
                            @if (in_array($data->activity_id, [4]))
                                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
                            @else
                                <img style="height: 16px;" src="{{ public_path('images/uncheck.jpg') }}">
                            @endif
                        </td>
                        <td><b>Pindah Logasi (Relokasi</b> <br> <i>Relocation)</i></td>
                    </tr>
                    <tr>
                        <td>
                            @if (in_array($data->activity_id, [5]))
                                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
                            @else
                                <img style="height: 16px;" src="{{ public_path('images/uncheck.jpg') }}">
                            @endif
                        </td>
                        <td><b>Pemutusan Layanan</b> <br> <i>Service Cancelation</i></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td width="150" align="center"> <b>Layanan</b> <i>Service</i> </td>
                        <td width="10">&nbsp;</td>
                        <td align="center"> <b>Catatan</b> <i>Note</i> </td>
                    </tr>
                    <tr>
                        <td> <b>Internet</b> <br> <i>Internet</i> </td>
                        <td>:</td>
                        <td>
                            <div class="divsquare"> {{ $internet ?: '-' }} </div>
                        </td>
                    </tr>
                    <tr>
                        <td> <b>Telepon</b> <br> <i>Phone</i> </td>
                        <td>:</td>
                        <td>
                            <div class="divsquare">&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                        <td> <b>Televisi</b> <br> <i>Television</i> </td>
                        <td>:</td>
                        <td>
                            <div class="divsquare"> {{ $totalStb ?: '-' }} </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

    {{-- SECTION 3  --}}

    <div style="height: 10px;"></div>

    <table class="tableheadertitle" style="margin-bottom: 5px;">
        <tr>
            <td><b>Hasil Instalasi</b> / Installation Results</td>
            <td><b>Profil Layanan</b> / Service Profile</td>
        </tr>
    </table>

    <table style="font-size: 10px;">
        <tr>
            <td><b>Instalasi Kabel FO</b> <br> <i>FO Cable Installation</i> </td>
            <td width="100" align="right">Baik / Good</td>
            <td width="20" align="right">
                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
            </td>

            <td width="100" align="right">Buruk / Bad</td>
            <td width="20" align="right">
                <img style="height: 16px;" src="{{ public_path('images/uncheck.jpg') }}">
            </td>

            <td width="100" align="right">PING</td>
            <td width="20" align="right">
                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
            </td>

            <td width="100" align="right">STREAMING</td>
            <td width="20" align="right">
                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
            </td>

            <td width="100" align="right">TEST CALL</td>
            <td width="20" align="right">
                <img style="height: 16px;" src="{{ public_path('images/uncheck.jpg') }}">
            </td>
            <td width="50" align="right">&nbsp;</td>
        </tr>
        <tr>
            <td><b>Instalasi Perangkat</b> <br> <i>Device Installation</i> </td>
            <td align="right">Baik / Good</td>
            <td align="right">
                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
            </td>

            <td align="right">Buruk / Bad</td>
            <td align="right">
                <img style="height: 16px;" src="{{ public_path('images/uncheck.jpg') }}">
            </td>

            <td align="right">BROWSING</td>
            <td align="right">
                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
            </td>

            <td align="right">SPEED TEST</td>
            <td align="right">
                <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
            </td>

            <td align="right">VIDEO / TV</td>
            <td align="right">
                @if ($totalStb)
                <img style="height: 16px;"
                    src="{{ public_path($totalStb ? 'images/check.jpg' : 'images/check.jpg') }}">
                @else
                <img style="height: 16px;"
                    src="{{ public_path($totalStb ? 'images/check.jpg' : 'images/uncheck.jpg') }}">
                @endif
            </td>
            <td align="right">&nbsp;</td>
        </tr>
    </table>

    {{-- SECTION 4  --}}

    <div style="height: 10px;"></div>

    <table class="tableheadertitle" style="margin-bottom: 5px;">
        <tr>
            <td><b>Catatan Instalasi</b> / Additional Note</td>
        </tr>
    </table>

    <table style="font-size: 10px;">
        <tr>
            <td width="50%">
                <b>Mohon isi apabila terdapat pergantian perangkat pada perubahan layanan pelanggan.</b> <br>
                <i>Please fill in the installation notes if there is a device replacement during the service type
                    exchange.</i>
            </td>
            <td width="10">&nbsp;</td>
            <td>
                <div class="divsquare" style="padding: 6px;">{{ $data->description ?: '-' }}</div>
            </td>
        </tr>
    </table>

    {{-- SECTION 5  --}}

    <div style="height: 10px;"></div>

    <table class="tableheadertitle" style="margin-bottom: 5px;">
        <tr>
            <td><b>Material dan Kabel Drop Terpasang</b> / Installed Material and Cable Drop</td>
        </tr>
    </table>

    <table class="tableborder" style="font-size: 10px;">
        <tr>
            <td width="30">No</td>
            <td><b>Informasi Katalog</b> <br> <i>Catalog Information</i></td>
            <td width="100"><b>Tipe</b> <br> <i>Type</i></td>
            <td width="100"><b>Unit</b> <br> <i>Unit</i></td>
            <td width="100"><b>Harga/Unit (Rp)</b> <br> <i>Price/Unit (IDR)</i></td>
            <td width="80"><b>Jumlah Barang</b> <br> <i>Item Quantity</i></td>
            <td width="100"><b>Subtotal Harga</b> <br> <i>Subtotal Price</i></td>
        </tr>
        <tr>
            <td>1</td>
            <td>Excess Material - UTP</td>
            <td>{{ $emUtp ?: 0 }}</td>
            <td>Meter</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Excess Material - Drop Wire</td>
            <td> {{ $emWire ?: 0 }} </td>
            <td>Meter</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>

    {{-- SECTION 6  --}}

    <div style="height: 10px;"></div>

    <table class="tableheadertitle" style="margin-bottom: 5px;">
        <tr>
            <td><b>Perangkat Terpasang</b> / Installed Device</td>
        </tr>
    </table>

    <table class="tableborder" style="font-size: 10px;">
        <tr>
            <td>No</td>
            <td width="32%"><b>Informasi Katalog</b> / <i>Catalog Information</i></td>
            <td width="32%"><b>Nomor Seri</b> / <i>Serial Number</i></td>
            <td width="32%"><b>Kodifikasi</b> / <i>Codification</i></td>
        </tr>
        <tr>
            <td>1</td>
            <td>{{ $ontType ?: '-' }}</td>
            <td>{{ $ontSN ?: '-' }}</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>2</td>
            <td>{{ $stbType1 ?: '-' }}</td>
            <td>{{ $stbSN1 ?: '-' }}</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>3</td>
            <td>{{ $stbType2 ?: '-' }}</td>
            <td>{{ $stbSN2 ?: '-' }}</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>4</td>
            <td>{{ $stbType3 ?: '-' }}</td>
            <td>{{ $stbSN3 ?: '-' }}</td>
            <td>&nbsp;</td>
        </tr>
    </table>

    {{-- SECTION 6  --}}

    <div
        style="border: 2px solid #c90668; border-radius: 5px; padding: 5px; text-align: center; margin-top: 10px; font-size: 10px;">
        <b>
            MOHON UNTUK TIDAK MEMBERIKAN UANG TUNAI KEPADA TIM DI LAPANGAN DENGAN ALASAN APA PUN. <br>
            INDOSAT OOREDOO HUTCHISON TIDAK BERTANGGUNG JAWAB ATAS KERUGIAN YANG TERJADI DARI PENIPUAN YANG DIALAMI.
        </b>
        <div style="height: 5px;"></div>
        <i>
            PLEASE DO NOT GIVE CASH TO THE FIELD TEAM FOR ANY REASON.<br>
            INDOSAT OOREDOO HUTCHISON IS NOT RESPONSIBLE FOR ANY DAMAGES DUE TO THE FRAUDULENT ACT.
        </i>
    </div>

    <table style="font-size: 10px; margin-top: 24px;">
        <tr>
            <td width="20%">
                <table class="tableborder">
                    <tr>
                        <td style="height: 110px">

                        </td>
                    </tr>
                    <tr>
                        <td style="height: 16px; text-align: center;">
                            <b>Admin Lapangan</b> / Field Admin
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 110px">
                            @if ($ttdFieldtech)
                                <img style="height: 100px; margin-top: 15px;"
                                    src="{{ storage_path('app/public/uploads/' . $ttdFieldtech->filename) }}">
                                <div style="height: 20px; padding: 0px; text-align: center">{{ $ttdFieldtechName }}
                                </div>
                            @else
                                <div style="height: 110px"></div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 16px; text-align: center;">
                            <b>Teknisi</b> / Technician
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 110px">
                                <img style="height: 100px; margin-top: 15px;"
                                    src="{{ storage_path('app/public/uploads/' . $ttdCustomer->filename) }}">
                                <div style="height: 20px; padding: 0px; text-align: center">{{ $ttdCustomerName ?: $data->site->name }}
                                </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 16px; text-align: center;">
                            <b>Pelanggan</b> / Customer
                        </td>
                    </tr>
                </table>
            </td>
            <td width="24">&nbsp;</td>
            <td style="height: 330px; vertical-align: top;">
                <div style="font-size: 20px; margin-bottom: 20px;">
                    <b>Syarat dan Ketentuan</b> <i>/ Terms and Conditions</i>
                </div>
                <ol
                    style="font-size: 14px; padding-left: 20px; text-align: justify;">
                    <li style="margin-bottom: 20px;">
                        <b>Formulir Berita Acara Lapangan ini tunduk pada Syarat dan Ketentuan Layanan Indosat HiFi yang
                        merupakan satu kesatuan yang tidak terpisahkan.</b>
                        <br>
                        <i>This Field Report is subject to and can not be separated from the Indosat HiFi Service Terms
                            and Conditions.</i>
                    </li>
                    <li style="margin-bottom: 20px;">
                        <b>Indosat Ooredoo Hutchison hanya akan menanggung biaya penarikan kabel fiber dengan panjang kabel
                        maksimal 200 meter dan kabel UTP LAN sepanjang 1,5 meter.</b>
                        <br>
                        <i>Indosat Ooredoo Hutchison will only bear the cost of pulling fiber cables with a maximum
                            cable length of 200 meters and UTP LAN cable by up to 1.5 meter.</i>
                    </li>
                    <li style="margin-bottom: 20px;">
                        <b>Apabila panjang kabel melebihi yang ditentukan, Pelanggan akan menanggung biaya kelebihan
                        penarikan kabel dengan harga Rp8.000,- per meter untuk kabel fiber dan Rp10.000,- per meter
                        untuk kabel UTP LAN.</b>
                        <br>
                        <i>If the cable exceeds the specified length, the Customer will bear the cost of excess cable
                            pulling at a price of IDR 8,000 per meter for fiber cable and IDR 10,000 per meter for UTP
                            LAN cable.</i>
                    </li>
                    <li style="margin-bottom: 20px;">
                        <b>Pelanggan setuju untuk membayar biaya tambahan material yang akan ditagihkan pada bulan
                        berikutnya.</b>
                        <br>
                        <i>Customer agrees to pay additional material costs billed the following month.</i>
                    </li>
                    <li>
                        <b>Dengan menandatangani Formulir Berita Acara Lapangan ini, Pelanggan menerima hasil pekerjaan
                        yang dilakukan oleh personel Indosat Ooredoo Hutchison dengan hasil sebagaimana
                        tercantum dalam informasi yang disebutkan di atas.</b>
                        <br>
                        <i>By signing this Field Report, the Customer accepts the work of Indosat Ooredoo Hutchison’s
                            team with the results as stated in the information table above.</i>
                    </li>
                </ol>
            </td>

        </tr>
    </table>
</body>
