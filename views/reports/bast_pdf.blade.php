<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tp  {border-collapse:collapse;border-spacing:0;}
.tp td{border-color:black;border-style:solid;border-width: thin;font-family:Arial, sans-serif;font-size:14px;
  overflow:hidden;padding:10px 10px;word-break:normal;}
.tp .tp-subtitle{font-size: 14;font-weight: bold;border-color:inherit;text-align:center;vertical-align:top}
.tg td{border-color:black;border-style:solid;border: none;font-family:Times New Roman, sans-serif;font-size:14px;
  overflow:hidden;padding:5px 5px;word-break:normal;}
.tg th{border-color:black;border-style:solid;border: none;font-family:Times New Roman, sans-serif;font-size:14px;
  font-weight:normal;overflow:hidden;padding:5px 5px;word-break:normal;}
.tg .tg-title{font-size: 16;font-weight: bold;text-transform: uppercase;border: none;vertical-align:middle,text-align:left}
.tg .tg-subtitle{font-size: 12;font-weight: bold;border-color:inherit;text-align:center;vertical-align:top}
.tg .tg-ttd{font-size: 10;border-color:inherit;text-align:center;vertical-align:top;font-weight:bold;text-decoration: underline;}
.tg .tg-note{font-size: 8;border-color:inherit;text-align:justify;vertical-align:top}
.tg .tg-9wq8{border-color:inherit;text-align:center;vertical-align:middle}
.tg .tg-c3ow{border-color:inherit;text-align:center;vertical-align:top}
.tg .tg-0pky{padding:3px 3px;border-color:inherit;text-align:justify;vertical-align:top}
.tg .tg-parts{border:1px;border-color:inherit;text-align:left;vertical-align:top}
.tg .tg-0lax{text-align:left;vertical-align:top}
.page-break {
        page-break-after: always;
    }
</style>
<?php $pic = ""; ?>
@if($data->site)<?php $pic= $data->site->pic?>
@else <?php $pic= $data->removeSite->pic?>
@endif
@foreach($data->actions as $act)
  @foreach($act->details as $detail)
      @if($detail->detail->name=='Name' && $detail->value<>'')<?php $pic= $detail->value?>=;
      @endif
  @endforeach
@endforeach
<table class="tg" style="undefined;table-layout: fixed; width: 100%">
<colgroup>
<col style="width: 10px">
<col style="width: 24px">
<col style="width: 195px">
<col style="width: 22px">
<col style="width: 50px">
<col style="width: 145px">
<col style="width: 247px">
</colgroup>
  <tr>
    <th class="tg-0pky" colspan="1">
        <img src="{{ public_path('images/logo.jpeg') }}" style="height: 50px">
    </th>
    <th class="tg-title" colspan="6">Berita Acara Serah Terima (BAST)</th>
  </tr>
<tbody>
  <tr>
    <td class="tg-0pky" colspan="7"></td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="7">Pada hari ini
      @foreach($data->actions as $act)
        @foreach($act->details as $detail)
            @if($detail->detail->name=='Active Date')
              <?php
              $day = date('D', strtotime($detail->value));
              $date = date('d', strtotime($detail->value));
              $month = date('m', strtotime($detail->value));
              $year = date('Y', strtotime($detail->value));
              $dayList = array(
                'Sun' => 'Minggu',
                'Mon' => 'Senin',
                'Tue' => 'Selasa',
                'Wed' => 'Rabu',
                'Thu' => 'Kamis',
                'Fri' => 'Jumat',
                'Sat' => 'Sabtu'
              );
              echo $dayList[$day]; ?>, tanggal {{ $date }}, bulan
              <?php
              $monthList = array(
                '01' => 'Januari',
                '02' => 'Pebruari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
              );
              echo $monthList[$month]; ?>, tahun {{ $year }}. Kami yang bertandatangan dibawah ini:</td>
            @endif
        @endforeach
      @endforeach
  </tr>
  <tr>
    <td class="tg-0pky">1.</td>
    <td class="tg-0pky" colspan="2">Nama</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">{{ $pic }}</td>
  </tr>
  <tr>
    <td class="tg-0pky"></td>
    <td class="tg-0pky" colspan="2">Jabatan - Perusahaan</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">(
        @if($data->site){{ $data->client->name }}
        @else {{ $data->removeSite->client->name }}
        @endif
    )</td>
  </tr>
  <tr>
    <td class="tg-0pky"></td>
    <td class="tg-0pky" colspan="2">Selanjutnya disebut</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">Pihak Pertama</td>
  </tr>
  <tr>
    <td class="tg-0pky">2.</td>
    <td class="tg-0pky" colspan="2">Nama</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">{{ $data->createdBy->name }}</td>
  </tr>
  <tr>
    <td class="tg-0pky"></td>
    <td class="tg-0pky" colspan="2">Jabatan - Perusahaan</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">{{ $data->createdBy->role->name }}</td>
  </tr>
  <tr>
    <td class="tg-0pky"></td>
    <td class="tg-0pky" colspan="2">Selanjutnya disebut</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">Pihak Kedua</td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="7"></td>
  </tr>
  <tr>
    <td class="tg-0pky"></td>
    <td class="tg-0pky" colspan="6">Selanjutnya PIHAK PERTAMA dan PIHAK KEDUA secara bersama-sama disebut PARA PIHAK.</td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="7"></td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="7">Dengan ini PARA PIHAK menyatakan bahwa PIHAK KEDUA telah selesai melakukan serah terima pekerjaan kepada PIHAK PERTAMA sesuai dengan:</td>
  </tr>
  <tr>
    <td class="tg-0lax" colspan="7"></td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="3">No Perjanjian/COF/PO/SPK</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">
      @foreach($data->actions as $act)
        @foreach($act->details as $detail)
            @if($detail->detail->name=='COF ID'){{ $detail->value }}
            @endif
        @endforeach
      @endforeach
    </td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="3">Lokasi</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">
        @if($data->site){{ $data->site->name }}
        @else {{ $data->removeSite->name }}
        @endif
    </td>
  </tr>

  <tr>
    <td class="tg-0pky" colspan="3">Jangka waktu berlangganan</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">
      @foreach($data->actions as $act)
        @foreach($act->details as $detail)
            @if($detail->detail->name=='Contract Periode'){{ $detail->value }}
            @endif
        @endforeach
      @endforeach
    </td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="3">Kapasitas</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">
      @foreach($data->actions as $act)
        @foreach($act->details as $detail)
            @if($detail->detail->name=='Capacity'){{ $detail->value }}
            @endif
        @endforeach
      @endforeach
    </td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="3">PID</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">
      @foreach($data->actions as $act)
        @foreach($act->details as $detail)
            @if($detail->detail->name=='PID'){{ $detail->value }}
            @endif
        @endforeach
      @endforeach
    </td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="3">Link ID</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">
        @if($data->site){{ $data->site->link_id }}
        @else {{ $data->removeSite->link_id }}
        @endif
    </td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="3">Latitude</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">
        @if($data->site){{ $data->site->lat }}
        @else {{ $data->removeSite->lat }}
        @endif
    </td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="3">Longitude</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">
        @if($data->site){{ $data->site->long }}
        @else {{ $data->removeSite->long }}
        @endif
    </td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="3">Tanggal Aktif Link/Integrasi</td>
    <td class="tg-0pky">:</td>
    <td class="tg-0pky" colspan="3">
      @foreach($data->actions as $act)
        @foreach($act->details as $detail)
            @if($detail->detail->name=='Active Date')<?php
            $date=date_create($detail->value);
            echo date_format($date,"d/m/Y"); ?>
            @endif
        @endforeach
      @endforeach
    </td>
  </tr>
  <tr>
    <td class="tg-0lax" colspan="7"></td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="7">PARA PIHAK menyetujui bahwa link telah selesai dilakukan pengetesan dan berjalan normal sesuai dengan spesifikasi teknis dan objektif yang disepakati serta layanan dinyatakan telah digunakan/ dioperasikan oleh PIHAK PERTAMA terhitung sejak Tanggal Berita Acara Serah Terima ini dibuat.</td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="7"></td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="7">PIHAK PERTAMA bertanggung jawab sepenuhnya atas isi dari semua informasi yang ada pada Berita Acara Serah Terima ini. Pelanggan menyetujui untuk tunduk dan patuh pada syarat dan ketentuan yang berlaku di .</td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="7"></td>
  </tr>
  <tr>
    <td class="tg-subtitle" colspan="3">PIHAK KEDUA</td>
    <td class="tg-0pky" colspan="2" rowspan="7"></td>
    <td class="tg-subtitle" colspan="2">PIHAK PERTAMA</td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="3">Yang menyerahkan,</td>
    <td class="tg-c3ow" colspan="2">Yang menerima,</td>
  </tr>
  <tr >
    <td class="tg-9wq8" colspan="3" rowspan="2">
      <img src="{{ public_path('images/ttd.jpg') }}" style="height: 50px">
    </td>
    <td class="tg-9wq8" colspan="2" rowspan="2">
      @foreach($data->actions as $act)
        @foreach($act->details as $detail)
            @if($detail->detail->type=='signature')
              @if(file_exists(public_path('storage/uploads/' . $detail->value . '.jpeg')))
                  <img src="{{ public_path('storage/uploads/') }}{{ $detail->value }}.jpeg" style="height: 50px">
              @else
                  <img src="{{ public_path('storage/uploads/') }}{{ $detail->value }}.png" style="height: 50px">
              @endif
            @endif
        @endforeach
      @endforeach
    </td>
  </tr>
  <tr>
  </tr>
  <tr>
    <td class="tg-ttd" colspan="3">{{ $data->createdBy->name }}</td>
    <td class="tg-ttd" colspan="2">{{ $pic }}</td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="3">{{ $data->createdBy->role->name }}</td>
    <td class="tg-c3ow" colspan="2"></td>
  </tr>
  <tr>
    <td class="tg-0lax" colspan="7"></td>
  </tr>
  <tr>
    <td class="tg-note" colspan="7">*Note : Semua equipment yang terpasang merupakan equipment yang dipinjamkan selama masa berlangganan dan akan ditarik kembali setelah masa kontrak berakhir</td>
  </tr>
</tbody>
</table>
<div class="page-break"></div>
@if($data->parts<>'[]')
<?php echo '<table class="tp" style="undefined;table-layout: fixed; width: 100%">
<thead>
  <tr>
    <th colspan="4" class="tp-subtitle">Perangkat yang terpasang</th>
  </tr>
  <tr>
  <td style="border: none;"></td>
  </tr>
</thead>
<tbody>
  <tr class="tp-subtitle">
    <td>Unit</td>
    <td>Model</td>
    <td>Serial No</td>
    <td>MAC</td>
  </tr>'?>
@foreach($data->parts as $part)
  @if($part->type=="EQUIPMENT")
  <tr>
    <td>{{ $part->name }}</td>
    <td>{{ $part->model }}</td>
    <td>{{ $part->serial }}</td>
    <td>{{ $part->code }}</td>
  </tr>
  @endif
@endforeach
<?php echo '</tbody>
</table>'?>
@endif
