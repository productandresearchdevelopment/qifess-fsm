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
    height: 15px;
    padding: 3px 5px;
    vertical-align: top
  }
</style>

<body>
  <table>
    <tr>
      <td>
        {{-- <img src="{{ public_path('images/logo_taranet.jpg') }}" style="height: 40px"> --}}
      </td>
      <td width="400" align="right" valign="bottom" style="font-size: 11px">
        BERITA ACARA LAPANGAN / <br>USER ACCEPTENCE TEST
      </td>
    </tr>
  </table>

  <div style="height: 20px;"></div>

  <table class="tableborder" style="font-size: 11px">
    {{-- SECTION 1  --}}
    <tr>
      <td colspan="2" align="center"><b>BALAP Information</b></td>
      <td colspan="2" align="center"><b>Customer Information</b></td>
    </tr>
    <tr>
      <td width="20%">Number</td>
      <td width="20%">: {{ $data->no_wo ?: '-' }}</td>
      <td width="20%">Customer ID</td>
      <td width="40%">: {{ $ispCustomerId ?: '-' }}</td>
    </tr>
    <tr>
      <td>Date</td>
      <td>: {{ $time_finish ? date('d/m/Y', strtotime($time_finish)) : '-' }}</td>
      <td>Name</td>
      <td>: {{ $data->site->name ?: '-' }}</td>
    </tr>
    <tr>
      <td>Vendor</td>
      <td>: -</td>
      <td rowspan="2">Address</td>
      <td rowspan="2">
        :
        {{ $data->site ? substr($data->site->address, 0, 130) : '-' }}
        {{ $data->site && strlen($data->site->address) > 130 ? '...' : '' }}
      </td>
    </tr>
    <tr>
      <td>Technician</td>
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
      <td colspan="2" align="center"><b>Work Order Information</b></td>
      <td colspan="2" align="center"><b>Service Profile</b></td>
    </tr>
    <tr>
      <td>New Installation</td>
      <td>: @if (in_array($data->activity_id, [1, 9]))
          Yes
        @endif
      </td>
      <td align="center"><b>Service</b></td>
      <td align="center"><b>Note</b></td>
    </tr>
    <tr>
      <td>Upgrade Service</td>
      <td>: @if (in_array($data->activity_id, [0]))
          Yes
        @endif
      </td>
      <td>Internet</td>
      <td>: {{ $internet ?: '-' }} </td>
    </tr>
    <tr>
      <td>Downgrade Service</td>
      <td>: @if (in_array($data->activity_id, [0]))
          Yes
        @endif
      </td>
      <td>TV</td>
      <td>: - </td>
    </tr>
    <tr>
      <td>Relocation</td>
      <td>: @if (in_array($data->activity_id, [0]))
          Yes
        @endif
      </td>
      <td>Others</td>
      <td>: - </td>
    </tr>
    <tr>
      <td>Termination</td>
      <td>: @if (in_array($data->activity_id, [0]))
          Yes
        @endif
      </td>
      <td></td>
      <td></td>
    </tr>

    {{-- SECTION 3  --}}
    <tr>
      <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="center"><b>Technical Data</b></td>
      <td colspan="2" align="center"><b>Check List</b></td>
    </tr>
    <tr>
      <td>Equipment Name</td>
      <td>: -</td>
      <td align="center">PING</td>
      <td>
        <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
      </td>
    </tr>
    <tr>
      <td>ODP</td>
      <td>: -</td>
      <td align="center">STREAMING</td>
      <td>
        <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
      </td>
    </tr>
    <tr>
      <td>Spliter</td>
      <td>: -</td>
      <td align="center">BROWSING</td>
      <td>
        <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
      </td>
    </tr>
    <tr>
      <td>MAC Address</td>
      <td>: {{ $ontMac ?: '-' }}</td>
      <td align="center">SPEEDTEST</td>
      <td>
        <img style="height: 16px;" src="{{ public_path('images/check.jpg') }}">
      </td>
    </tr>
    <tr>
      <td colspan="2" rowspan="2"></td>
      <td align="center">TEST CALL</td>
      <td>
        <img style="height: 16px;" src="{{ public_path('images/uncheck.jpg') }}">
      </td>
    </tr>
    <tr>
      <td align="center">VIDEO/TV</td>
      <td>
        <img style="height: 16px;" src="{{ public_path('images/uncheck.jpg') }}">
      </td>
    </tr>

    {{-- SECTION 4  --}}
    <tr>
      <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" align="center"><b>Installation Notes</b></td>
    </tr>
    <tr>
      <td colspan="4" style="height: 100px"></td>
    </tr>
  </table>

  <table class="tableborder" style="font-size: 11px">
    <tr>
      <td colspan="5" align="center"><b>Installation Material & Equipment</b></td>
    </tr>
    <tr>
      <td align="center">Catalog Information</td>
      <td align="center">Type</td>
      <td align="center">Value</td>
      <td align="center">Quantity</td>
      <td align="center">OTHERS/ SERIAL NUMBER</td>
    </tr>
    <tr>
      <td>ONT</td>
      <td>{{ $ontType ?: '-' }}</td>
      <td></td>
      <td></td>
      <td> {{ $OntSN ?? '-' }} </td>
    </tr>
    <tr>
      <td>Drop Cable</td>
      <td>{{ $emWire ?: 0 }}m</td>
      <td></td>
      <td>{{ $emWire ?: 0 }}m</td>
      <td></td>
    </tr>
    <tr>
      <td>Cable Duct</td>
      <td>-</td>
      <td>-</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>Connector</td>
      <td>-</td>
      <td>-</td>
      <td></td>
      <td></td>
    </tr>
  </table>

  <table class="tableborder" style="font-size: 11px">
    <tr>
      <td align="center"><b>Scheme Of Network</b></td>
    </tr>
    <tr>
      <td style="height: 150px"></td>
    </tr>
  </table>

  <table style="font-size: 11px;margin-top: 100px">
    <tr>
      <td width="33%" align="center">
        <div style="height: 120px"></div>
        <div style="padding: 10px 20px; border-top: 1px solid #333333; text-align: center; margin: 1px 60px;">Field
          Admin</div>
      </td>

      <td width="33%" align="center">
        @if ($ttdFieldtech)
          @php
            $fileUrl = route('upload.file', ['id' => $ttdFieldtech]);
          @endphp
          <img style="height: 100px;" src="{{ $fileUrl }}">
          <div style="height: 20px; padding: 0px">{{ $ttdFieldtechName ?: '-' }}</div>
        @else
          <div style="height: 120px"></div>
        @endif
        <div style="padding: 10px 20px; border-top: 1px solid #333333; text-align: center; margin: 1px 60px;">Technician
        </div>
      </td>

      <td width="33%" align="center">
        @if ($ttdCustomer)
          @php
            $fileUrl = route('upload.file', ['id' => $ttdCustomer]);
          @endphp
          <img style="height: 100px;" src="{{ $fileUrl }}">
          <div style="height: 20px; padding: 0px">{{ $ttdCustomerName ?: '-' }}</div>
        @else
          <div style="height: 120px"></div>
        @endif
        <div style="padding: 10px 20px; border-top: 1px solid #333333; text-align: center; margin: 1px 60px;">Customer
        </div>
      </td>
    </tr>
  </table>

</body>
