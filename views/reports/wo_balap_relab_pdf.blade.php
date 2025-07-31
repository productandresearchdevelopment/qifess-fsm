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
        <img src="{{ public_path('images/logo_relab.jpg') }}" style="height: 40px">
      </td>
      <td width="200" align="right" valign="bottom" style="font-size: 11px">
        <div style="background: #f67e00; color: #ffffff; font-size: 15px; padding: 2px 20px"><b>BERITA ACARA LAPANGAN</b>
        </div>
      </td>
    </tr>
  </table>

  <table style="font-size: 11px; margin-top: 15px">
    <tr>
      <td></td>
      <td width="60">No WO</td>
      <td width="10">:</td>
      <td width="100" align="right">{{ $data->no_wo }}</td>
    </tr>
    <tr>
      <td></td>
      <td>ID Customer</td>
      <td>:</td>
      <td align="right">{{ $ispCustomerId ?: '-' }}</td>
    </tr>
  </table>

  <div style="height: 20px;"></div>

  <p>
    Pada hari ini &nbsp;
    {{ hari($time_finish) }},
    {{ $time_finish ? date('d F Y', strtotime($time_finish)) : '................' }}
    Jam {{ $time_finish ? date('H:i', strtotime($time_finish)) : '................' }}
    telah dilakukan
  <table style="line-height: 20px;">
    <tr>
      <td width="33%">
        [
        @if (in_array($data->activity_id, [1, 9]))
          X
        @else
          &nbsp;
        @endif
        ] &nbsp; Aktivasi Layanan Baru
      </td>
      <td width="33%">
        [
        @if (in_array($data->activity_id, [7]))
          X
        @else
          &nbsp;
        @endif
        ] &nbsp; Troubleshooting
      </td>
      <td width="33%">
        [
        @if (in_array($data->activity_id, [5]))
          X
        @else
          &nbsp;
        @endif
        ] &nbsp; Deaktivasi
      </td>
    </tr>
    <tr>
      <td width="33%">
        [
        @if (in_array($data->activity_id, [4]))
          X
        @else
          &nbsp;
        @endif
        ] &nbsp; Aktivasi Relokasi
      </td>
      <td width="33%">
        [
        @if (in_array($data->activity_id, []))
          X
        @else
          &nbsp;
        @endif
        ] &nbsp; Deaktivasi Relokasi
      </td>
      <td width="33%"></td>
    </tr>
  </table>

  <br><br>

  Untuk pelanggan berikut:
  <table style="line-height: 30px">
    <tr>
      <td valign="top" width="120">Nama</td>
      <td valign="top" width="10">:</td>
      <td valign="top">{{ $data->site->name }}</td>
    </tr>
    <tr>
      <td valign="top">Alamat</td>
      <td valign="top">:</td>
      <td valign="top">{{ $data->site->address }}</td>
    </tr>
    <tr>
      <td valign="top">No Handphone</td>
      <td valign="top">:</td>
      <td valign="top">{{ $data->site->pic_phone }}</td>
    </tr>
    <tr>
      <td valign="top">Jenis layanan</td>
      <td valign="top">:</td>
      <td valign="top">
        <table style="line-height: 30px;">
          <tr>
            <td width="33%">
              [ @if (in_array($data->service_id, [6]) || in_array($data->site->service_id, [6]))
                X
              @else
                &nbsp;
              @endif ] &nbsp; Wirehome 30
            </td>
            <td width="33%">
              [ @if (in_array($data->service_id, [8]))
                X
              @else
                &nbsp;
              @endif ] &nbsp; Wirehome 100
            </td>
            <td width="33%">
              [ &nbsp; ] &nbsp; Paket Lainnya
            </td>
          </tr>
          <tr>
            <td>
              [ @if (in_array($data->service_id, [7]))
                X
              @else
                &nbsp;
              @endif ] &nbsp; Wirehome 50
            </td>
            <td>
              [ @if (in_array($data->service_id, [11]))
                X
              @else
                &nbsp;
              @endif ] &nbsp; Wirehome 200
            </td>
            <td>
              _______________________________
            </td>
          </tr>
          <!-- <tr>
                            <td colspan="3">
                                [ &nbsp; ] &nbsp; Paket Lainnya ________________________________________
                            </td>
                        </tr> -->
        </table>
      </td>
    </tr>
  </table>

  <br>
  Perangkat :<br><br>
  <table class="tableborder" style="line-height: 30px;">
    <tr>
      <td style="font-weight: bold; text-align: center" width="30" bgcolor="#ffd38e">No</td>
      <td style="font-weight: bold; text-align: center" bgcolor="#ffd38e">Nama Peralatan</td>
      <td style="font-weight: bold; text-align: center" width="200" bgcolor="#ffd38e">Mac Address/Serial Number </td>
      <td style="font-weight: bold; text-align: center" width="100" bgcolor="#ffd38e">Jumlah</td>
    </tr>
    <tr>
      <td align="center">1</td>
      <td> {{ $ontType ?: '-' }} </td>
      <td> {{ $ontSN ?: '-' }} </td>
      <td> 1 Unit </td>
    </tr>
    <tr>
      <td align="center"> 2 </td>
      <td>UTP</td>
      <td> - </td>
      <td>{{ $emUtp ?: 0 }} meter</td>
    </tr>
    <tr>
      <td align="center">3</td>
      <td> Drop Wire </td>
      <td> - </td>
      <td>{{ $emWire ?: 0 }} meter</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>

  <br>
  Catatan :
  <br> {{ $lastNote ?: '-' }}

  <br><br>
  * Hasil Tes : &nbsp; &nbsp;
  [X] Youtube &nbsp; &nbsp; &nbsp;
  [X] Speedtest Upload ……… &nbsp; &nbsp; &nbsp;
  [X] Speedtest Download ……….

  </p>

  <table style="font-size: 11px;margin-top: 100px">
    <tr>
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
        <div style="padding: 10px 20px; border-top: 1px solid #333333; text-align: center; margin: 1px 60px;">Petugas
          Teknis</div>
      </td>

      <td width="33%" align="center">
        <div style="height: 120px"></div>
        <div style="padding: 10px 20px; border-top: 1px solid #333333; text-align: center; margin: 1px 60px;">Petugas
          Admin</div>
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
        <div style="padding: 10px 20px; border-top: 1px solid #333333; text-align: center; margin: 1px 60px;">Pelanggan
        </div>
      </td>
    </tr>
  </table>

</body>
