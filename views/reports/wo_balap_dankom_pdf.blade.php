<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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

    body, table, td, div {font-family: sans-serif;}
    table{
        width: 100%;
        border-spacing: 0;
        border-collapse: collapse;
    }


    .page-break {page-break-after: always;}
    header {position: fixed; top: -10px; left: 80%; right: 0px;height: 30px; }
    footer {position: fixed; bottom: -60px; left: 70%; right: 0px; height: 50px; }


    .tableheadertitle{
        background: #444444;
        color: #FFFFFF;
        font-size: 10px;
    }
    .tableheadertitle td{
        width: 50%;
        padding: 3px;
        text-align: center;
    }
    .divsquare{border: 1px solid #444444; border-radius: 5px; padding: 3px 5px; margin-right: 20px;}

    .tableborder td{border: 1px solid #666666; height: 15px; padding: 3px 5px; vertical-align: top}

</style>

<body>
    <table>
        <tr>
            <td>

                <b>BERITA ACARA LAPANGAN / <br>USER ACCEPTENCE TEST</b>
            </td>
            <td width="500" align="right" valign="bottom" style="font-size: 11px">
                <img src="{{ public_path('images/dancom-logo.png') }}" style="height: 40px">
            </td>
        </tr>
    </table>

    <div style="height: 20px;"></div>

    <table class="tableborder" style="font-size: 11px">
        {{-- SECTION 1  --}}
        <tr>
            <td colspan="2" align="center" class="tableheadertitle"><b>BALAP Information</b></td>
            <td colspan="2" align="center" class="tableheadertitle"><b>Customer Information</b></td>
        </tr>
        <tr>
            <td width="20%">Nomor / Number</td>
            <td width="20%">: {{ $data->no_wo ?: '-' }}</td>
            <td width="20%">Customer ID</td>
            <td width="40%">: {{ $data->site->link_id ?: '-' }}</td>
        </tr>
        <tr>
            <td>BAL Date / Tgl BAL</td>
            <td>: {{ $time_finish ? date('d/m/Y', strtotime($time_finish)) : '-' }}</td>
            <td>Name</td>
            <td>: {{ $data->site->name ?: '-' }}</td>
        </tr>
        <tr>
            <td>Vendor / Company</td>
            <td>: {{ $data->fieldtech->vendor_name ?: '-' }}</td>
            <td rowspan="2">Address</td>
            <td rowspan="2">
                :
                {{ $data->site ? substr($data->site->address, 0, 130) : '-' }}
                {{ ($data->site && (strlen($data->site->address) > 130)) ? '...' : '' }}
            </td>
        </tr>
        <tr>
            <td>Technician / Teknisi</td>
            <td>: {{ $data->fieldtech ? $data->fieldtech->name : '-' }}</td>
        </tr>
        <tr>
            <td>Start & End Time</td>
            <td>
                :
                {{ $time_start ? date('d/m/Y H:i', strtotime($time_start)) : '-' }}
                &nbsp; <b>To</b> &nbsp;
                {{ $time_finish ? date('d/m/Y H:i', strtotime($time_finish)) : '-' }}
            </td>
            <td>Contact Number</td>
            <td>: {{ $data->site->pic_phone ?: '-' }}</td>
        </tr>

        {{-- SECTION 2  --}}
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" align="center" class="tableheadertitle"><b>Work Order Information</b></td>
            <td colspan="2" align="center" class="tableheadertitle"><b>Service Profile</b></td>
        </tr>
        <tr>
            <td>
            @if(in_array($data->activity_id, [1,9]))
                                <img style="height: 16px;" src="{{ public_path("images/check.jpg") }}">
                            @else
                                <img style="height: 16px;" src="{{ public_path("images/uncheck.jpg") }}">
                            @endif
                <span style="vertical-align: middle;">New Installation / Pasang Baru</span>
            </td>
            <td align="center"><b>Service</b></td>
            <td colspan="2" align="center"><b>Note</b></td>
        </tr>
        <tr>
            <td>
            @if(in_array($data->activity_id, [2,3]))
                                <img style="height: 16px;" src="{{ public_path("images/check.jpg") }}">
                            @else
                                <img style="height: 16px;" src="{{ public_path("images/uncheck.jpg") }}">
                            @endif
                <span style="vertical-align: middle;">Upgrade Service</span>
            </td>
            <td>Internet</td>
            <td colspan="2">: {{ $internet ?: '-'}} </td>
        </tr>
        <tr>
            <td>
            @if(in_array($data->activity_id, []))
                                <img style="height: 16px;" src="{{ public_path("images/check.jpg") }}">
                            @else
                                <img style="height: 16px;" src="{{ public_path("images/uncheck.jpg") }}">
                            @endif
                <span style="vertical-align: middle;">Downgrade Service</span>
            </td>
            <td>Phone</td>
            <td colspan="2">: - </td>
        </tr>
        <tr>
            <td>
            @if(in_array($data->activity_id, [4,6]))
                                <img style="height: 16px;" src="{{ public_path("images/check.jpg") }}">
                            @else
                                <img style="height: 16px;" src="{{ public_path("images/uncheck.jpg") }}">
                            @endif
                <span style="vertical-align: middle;">Relocation / Mutasi</span>
            </td>
            <td>TV</td>
            <td colspan="2">: - </td>
        </tr>
        <tr>
        <td>
            @if(in_array($data->activity_id, [5]))
                                <img style="height: 16px;" src="{{ public_path("images/check.jpg") }}">
                            @else
                                <img style="height: 16px;" src="{{ public_path("images/uncheck.jpg") }}">
                            @endif
            <span style="vertical-align: middle;">Termination / Pemutusan</span>
            </td>
            <td>Other</td>
            <td colspan="2">: - </td>
        </tr>

        {{-- SECTION 3  --}}
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" align="center" class="tableheadertitle"><b>Technical Data</b></td>
            <td colspan="2" align="center" class="tableheadertitle"><b>Check List</b></td>
        </tr>
        <tr>
            <td align="center" style="width: 25%">Equipment Name</td>
            <td align="center" style="width: 25%">Note</td>
            <td style="width: 25%">
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/check.jpg") }}">
                <span style="vertical-align: middle;">PING</span>
            </td>
            <td style="width: 25%">
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/check.jpg") }}">
                <span style="vertical-align: middle;">STREAMING</span>
            </td>
        </tr>
        <tr>
            <td>OLT</td>
            <td>: -</td>
            <td>
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/check.jpg") }}">
                <span style="vertical-align: middle;">TEST CALL</span>
            </td>
            <td>
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/check.jpg") }}">
                <span style="vertical-align: middle;">BROWSING</span>
            </td>
        </tr>
        <tr>
            <td>ODF</td>
            <td>: -</td>
            <td>
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/check.jpg") }}">
                <span style="vertical-align: middle;">SPEED TEST</span>
            </td>
            <td>
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/check.jpg") }}">
                <span style="vertical-align: middle;">VIDEO / TV</span>
            </td>
        </tr>
        <tr>
            <td>ODP</td>
            <td>: -</td>
            <td colspan="2" rowspan="2"></td>
        </tr>
        <tr>
            <td>Spliter</td>
            <td>: -</td>
        </tr>

        {{-- SECTION 4  --}}
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" align="center" class="tableheadertitle"><b>Performance Installation</b></td>
            <td colspan="2" align="center" class="tableheadertitle"><b>Installation Notes</b></td>
        </tr>
        <tr>
            <td>CABLE INSTALLATION</td>
            <td align="center">
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/uncheck.jpg") }}">
                <span style="vertical-align: middle;">OK</span>
                <img style="height: 16px; vertical-align: middle; margin-left: 20px;" src="{{ public_path("images/uncheck.jpg") }}">
                <span style="vertical-align: middle;">NOT</span>
            </td>
            <td colspan="2" rowspan="5"style="height: 100px"> {{ $lastNote ?: '-'}} </td>
        </tr>
        <tr>
            <td>EQUIPMENT INSTALLATION</td>
            <td align="center">
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/uncheck.jpg") }}">
                <span style="vertical-align: middle;">OK</span>
                <img style="height: 16px; vertical-align: middle; margin-left: 20px;" src="{{ public_path("images/uncheck.jpg") }}">
                <span style="vertical-align: middle;">NOT</span>
            </td>
        </tr>
        <tr>
            <td>ENGINEER APPEARENCE</td>
            <td align="center">
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/uncheck.jpg") }}">
                <span style="vertical-align: middle;">OK</span>
                <img style="height: 16px; vertical-align: middle; margin-left: 20px;" src="{{ public_path("images/uncheck.jpg") }}">
                <span style="vertical-align: middle;">NOT</span>
            </td>
        </tr>
        <tr>
            <td>ENGINNER COMUNICATION</td>
            <td align="center">
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/uncheck.jpg") }}">
                <span style="vertical-align: middle;">OK</span>
                <img style="height: 16px; vertical-align: middle; margin-left: 20px;" src="{{ public_path("images/uncheck.jpg") }}">
                <span style="vertical-align: middle;">NOT</span>
            </td>
        </tr>
        <tr>
            <td>OTHER</td>
            <td align="center">
                <img style="height: 16px; vertical-align: middle;" src="{{ public_path("images/uncheck.jpg") }}">
                <span style="vertical-align: middle;">OK</span>
                <img style="height: 16px; vertical-align: middle; margin-left: 20px;" src="{{ public_path("images/uncheck.jpg") }}">
                <span style="vertical-align: middle;">NOT</span>
            </td>
        </tr>
    </table>

    <table class="tableborder" style="font-size: 11px">
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="5" align="center" class="tableheadertitle"><b>Installation Material</b></td>
        </tr>
        <tr>
            <td align="center" style="width: 15px;">No</td>
            <td align="center">Catalog Information</td>
            <td align="center" style="width: 200px;">Type</td>
            <td align="center" style="width: 200px;">Value</td>
            <td align="center" style="width: 200px;">Quantity</td>
        </tr>
        <tr>
            <td align="center">1</td>
            <td>ONT</td>
            <td>{{ $ontType ?: '-' }}</td>
            <td>1 Unit</td>
            <td></td>
        </tr>
        <tr>
            <td align="center">2</td>
            <td>DROP CABLE</td>
            <td></td>
            <td>{{ $emWire ?: 0 }} Meter</td>
            <td></td>
        </tr>
        <tr>
            <td align="center">3</td>
            <td>CABLE DUCT</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td align="center">4</td>
            <td>CONNECTOR</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="tableborder" style="font-size: 11px">
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="5" align="center" class="tableheadertitle"><b>Installer Cable Drop</b></td>
        </tr>
        <tr>
            <td align="center" style="width: 15px;">No</td>
            <td align="center">Catalog Information</td>
            <td align="center" style="width: 200px;">Type</td>
            <td align="center" style="width: 200px;">Value</td>
            <td align="center" style="width: 200px;">Quantity</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="tableborder" style="font-size: 11px">
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="5" align="center" class="tableheadertitle"><b>Installer Cable Drop</b></td>
        </tr>
        <tr>
            <td align="center" style="width: 15px;">No</td>
            <td align="center">Catalog Information</td>
            <td align="center">Serial Number</td>
            <td align="center">Kodefikasi</td>
            <td align="center">Quantity</td>
        </tr>
        <tr>
            <td  style="height: 100px"  ></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table style="font-size: 11px;margin-top: 100px">
        <tr>
            <td width="33%" align="center">
                <div style="height: 120px"></div>
                <div style="padding: 10px 20px; border-top: 1px solid #333333; text-align: center; margin: 1px 60px;">Field Admin</div>
            </td>

            <td width="33%" align="center">
                @if($ttdFieldtech)
                <img style="height: 100px;" src="{{ storage_path("app/public/uploads/".$ttdFieldtech->filename) }}">
                <div style="height: 20px; padding: 0px">{{ $ttdFieldtechName }}</div>
                @else
                    <div style="height: 120px"></div>
                @endif
                <div style="padding: 10px 20px; border-top: 1px solid #333333; text-align: center; margin: 1px 60px;">Technician</div>
            </td>

            <td width="33%" align="center">
                @if($ttdCustomer)
                    <img style="height: 100px;" src="{{ storage_path("app/public/uploads/".$ttdCustomer->filename) }}">
                    <div>{{ $ttdCustomerName }}</div>
                @else
                    <div style="height: 120px"></div>
                @endif
                <div style="padding: 10px 20px; border-top: 1px solid #333333; text-align: center; margin: 1px 60px;">Customer</div>
            </td>
        </tr>
    </table>

</body>
