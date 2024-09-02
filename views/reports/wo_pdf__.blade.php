<style>
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
  overflow:hidden;padding:10px 5px;word-break:normal;}
.tg .subtitle{font-family: "Times New Roman", Times, serif;font-size: 14;font-weight: bold; text-transform: uppercase;border: none;text-align:center;vertical-align:top;padding:3px 5px;}
.tg .subtitle2{font-weight: bold; border: none;text-align:left;vertical-align:top;padding: 3px}
.tg .tg-0pky{border-color:inherit;text-align:left;vertical-align:top;padding: 2px}
.page-break {
        page-break-after: always;
    }
.title{font-family: "Times New Roman", Times, serif;font-size: 30;text-transform: uppercase;border: none;text-align:center;vertical-align:top;padding:3px 5px;}
    header { position: fixed; top: -10px; left: 80%; right: 0px;height: 30px; }
    footer { position: fixed; bottom: -60px; left: 70%; right: 0px; height: 50px; }
</style>


<?php $pic = "";$pic_phone = ""; ?>
@if($data->site)<?php $pic= $data->site->pic?>
@else <?php $pic= $data->removeSite->pic?>
@endif
@foreach($data->actions as $act)
  @foreach($act->details as $detail)
      @if($detail->detail->name=='Name' && $detail->value<>'')<?php $pic= $detail->value?>
      @elseif($detail->detail->name=='Phone' && $detail->value<>'')<?php $pic_phone= $detail->value?>
      @endif
  @endforeach
@endforeach
<body>
    <header>
        <img src="{{ public_path('images/logo.jpeg') }}" style="height: 50">
    </header>

    <h2 class="title" style="padding-top: 200px">{{ $data->activity->name }} REPORT</h2>
    <div style="height:20px"></div>
    <h2 class="title">Nama Site</h2>
    <h2 class="title">
        @if($data->site){{ $data->site->name }}
        @else {{ $data->removeSite->name }}
        @endif
    </h2>
        <div style="height:20px"></div>
    <h2 class="title">LINK ID SITE</h2>
    <h2 class="title">
        @if($data->site){{ $data->site->link_id }}
        @else {{ $data->removeSite->link_id }}
        @endif
    </h2>
        <div class="page-break"></div>

    <table class="tg" style="table-layout: fixed; width: 100%;padding-top: 80px">
        <tr>
          <td class="tg-0pky" style="padding: 55px; padding-right: 220px" colspan="12">
              <img src="{{ public_path('images/logo.jpeg') }}" style="height: 150">

          </td>
        </tr>
        <tr>
          <td class="tg-0pky" style="height:10px;border: none"colspan="12"></td>
        </tr>
        <tr>
          <td class="tg-0pky" colspan="12">No Kontrak :
          @foreach($data->actions as $act)
            @foreach($act->details as $detail)
                @if($detail->detail->name=='COF ID' && $detail->value<>''){{ $detail->value }}
                @elseif($detail->detail->name=='PO ID' && $detail->value<>'') {{ $detail->value }}
                @endif
            @endforeach
          @endforeach
          </td>
        </tr>
        <tr>
          <td class="tg-0pky" colspan="6">{{ $data->activity->name }} REPORT</td>
          <td class="tg-0pky" colspan="6"></td>
        </tr>
        <tr>
          <td class="tg-0pky" colspan="2"></td>
          <td class="tg-0pky" colspan="2">Pelaksana</td>
          <td class="tg-0pky" colspan="2">Nama</td>
          <td class="tg-0pky" colspan="2">Jabatan</td>
          <td class="tg-0pky" colspan="2">Tanda Tangan</td>
          <td class="tg-0pky" colspan="2">Tanggal</td>
        </tr>
        <tr>
          <td class="tg-0pky" colspan="2">Dipersiapkan Oleh</td>
          <td class="tg-0pky" colspan="2">{{ $data->vendor->alias}}</td>
          <td class="tg-0pky" colspan="2">@if($data->fieldtech){{ $data->fieldtech->name}}@endif</td>
          <td class="tg-0pky" colspan="2">
            @foreach($data->actions as $act)
              @if($act->status_id==1450){{ $act->createdBy->role->name}}
              @endif
            @endforeach  </td>
          <td class="tg-0pky" colspan="2"></td>
          <td class="tg-0pky" colspan="2"></td>
        </tr>
        <tr>
          <td class="tg-0pky" colspan="2">Diperiksa Oleh</td>
          <td class="tg-0pky" colspan="2">NOC</td>
          <td class="tg-0pky" colspan="2">
            @foreach($data->actions as $act)
              @if($act->status_id==1510){{ $act->createdBy->name}}
              @endif
            @endforeach  </td>
          <td class="tg-0pky" colspan="2">
            @foreach($data->actions as $act)
              @if($act->status_id==1510){{ $act->createdBy->role->name}}
              @endif
            @endforeach  </td>
          <td class="tg-0pky" colspan="2"></td>
          <td class="tg-0pky" colspan="2"></td>
        </tr>
        <tr>
          <td class="tg-0pky" colspan="2">Diketahui Oleh</td>
          <td class="tg-0pky" colspan="2">Project</td>
          <td class="tg-0pky" colspan="2">{{ $data->createdBy->name }}</td>
          <td class="tg-0pky" colspan="2">{{ $data->createdBy->role->name }}</td>
          <td class="tg-0pky" colspan="2"></td>
          <td class="tg-0pky" colspan="2"></td>
        </tr>
    </table>

    <div class="page-break"></div>

    <table class="tg" style="table-layout: fixed; width: 100%;padding-top: 80px">
      <tr>
        <td class="subtitle" colspan="12">GENERAL SITE INFORMATION</td>
      </tr>
      <tr>
        <td class="subtitle2" colspan="12">Data Site/ Location</td>
      </tr>
      <tr>
        <td class="tg-0pky" style="font-weight: bold;text-align: center;" colspan="4">Items</td>
        <td class="tg-0pky" style="font-weight: bold;text-align: center;" colspan="8">Value</td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Nama Lokasi</td>
        <td class="tg-0pky" colspan="8">
          @if($data->site){{ $data->site->name }}
          @else {{ $data->removeSite->name }}
          @endif</td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Alamat</td>
        <td class="tg-0pky" colspan="8">
          @if($data->site){{ $data->site->address }}
          @else {{ $data->removeSite->address }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Latitude</td>
        <td class="tg-0pky" colspan="8">
          @if($data->site){{ $data->site->lat }}
          @else {{ $data->removeSite->lat }}
          @endif</td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Longitude</td>
        <td class="tg-0pky" colspan="8">
          @if($data->site){{ $data->site->long }}
          @else {{ $data->removeSite->long }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">PIC di lokasi</td>
        <td class="tg-0pky" colspan="8">{{ $pic }}</td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Nomor Telepon</td>
        <td class="tg-0pky" colspan="8">{{ $pic_phone }}</td>
      </tr>
    </table>

    <table class="tg" style="table-layout: fixed; width: 100%;padding-top: 20px">
      <tr>
        <td class="subtitle2" colspan="12">Data Client/ Customer</td>
      </tr>
      <tr>
        <td class="tg-0pky" style="font-weight: bold;text-align: center;" colspan="4">Title</td>
        <td class="tg-0pky" style="font-weight: bold;text-align: center;" colspan="8">Value</td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Client/ Customer Name</td>
        <td class="tg-0pky" colspan="8">
          @if($data->site){{ $data->client->name }}
          @else {{ $data->removeSite->client->name }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Address</td>
        <td class="tg-0pky" colspan="8">
          @if($data->site){{ $data->client->address }}
          @else {{ $data->removeSite->client->address }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Phone</td>
        <td class="tg-0pky" colspan="8">
          @if($data->site){{ $data->client->phone }}
          @else {{ $data->removeSite->client->phone }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Email</td>
        <td class="tg-0pky" colspan="8">
          @if($data->site){{ $data->client->email }}
          @else {{ $data->removeSite->client->email }}
          @endif
        </td>
      </tr>
    </table>

    <table class="tg" style="table-layout: fixed; width: 100%;padding-top: 20px">
      <tr>
        <td class="subtitle2" colspan="12">Data Vendor</td>
      </tr>
      <tr>
        <td class="tg-0pky" style="font-weight: bold;text-align: center;" colspan="4">Title</td>
        <td class="tg-0pky" style="font-weight: bold;text-align: center;" colspan="8">Value</td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Vendor Name</td>
        <td class="tg-0pky" colspan="8">{{ $data->vendor->name }}</td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Address</td>
        <td class="tg-0pky" colspan="8">{{ $data->vendor->address }}</td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Phone</td>
        <td class="tg-0pky" colspan="8">{{ $data->vendor->phone }}</td>
      </tr>
      <tr>
        <td class="tg-0pky" colspan="4">Email</td>
        <td class="tg-0pky" colspan="8">{{ $data->vendor->email }}</td>
      </tr>
    </table>
    <div class="page-break"></div>

    <h2 class="subtitle" style="text-align: center;padding-top: 80px">HISTORY PROGRESS STATUS</h2>

    @foreach($data->actions as $act)
      <?php $count = 0; $count_image = 0; $last_group =""; $value=""; $act_id = $act->id; $note = str_replace("\n", '<br>', $act->note)?>
      <table class="tg" style="table-layout: fixed; width: 100%;padding-top: 80px">
        <tr style="padding-top: 20px">
          <td class="subtitle2" colspan="8">STATUS: {{ $act->status->name }}</td>
        </tr>
        <tr>
          <td class="subtitle2" style="font-style: italic;font-size: 10px;"colspan="4">Created at: {{ date('d M Y H:i', strtotime($act->created_at)) }}</td>
        </tr>
        <tr>
          <td class="subtitle2" style="font-style: italic;font-size: 10px;"colspan="12">Created by: {{ $act->createdBy->name }} ({{ $act->createdBy->role->name }})</td>
        </tr>
        <tr>
          <td class="subtitle2" style="font-style: italic;font-size: 10px" colspan="12">Note / Description: {{ $note }}</td>
        </tr>
      @foreach($act->status->details as $sts)
        @foreach($sts->actionDetails as $act_dtl)
          @if($act_id == $act_dtl->action_id )
            @if($act_dtl->value!="")
              @if($sts->type=='text')
                <?php $value = $act_dtl->value; ?>
              @elseif($sts->type=='textarea')
                <?php $value = str_replace("\n", '<br>', $act_dtl->value); ?>
              @elseif($sts->type=='datetime')
                <?php $value = date('d M Y H:i', strtotime($act_dtl->value)); ?>
              @elseif($sts->type=='date')
                <?php $value = date('d M Y', strtotime($act_dtl->value)); ?>
              @elseif($sts->type=='time')
                <?php $value = date('H:i', strtotime($act_dtl->value)); ?>
              @elseif($sts->type=='signature')
                <?php $value = date('H:i', strtotime($act_dtl->value)); ?>
              @endif
            @endif

            @if($sts->type!='file' && $sts->type!='hide' && $value!="")
             <?php $count ++; ?>
              @if($count<=1)
                <tr>
                  <td class="tg-0pky" style="font-weight: bold; text-align: center;" colspan="4">Data</td>
                  <td class="tg-0pky" style="font-weight: bold; text-align: center;" colspan="8">Value</td>
                </tr>
              @endif
              @if($value!=="")
                @if($last_group!=$sts->group)
                  <?php $last_group = $sts->group; ?>
                  <tr>
                    <td class="tg-0pky" style="font-weight: bold;font-style: italic;"colspan="12">{{ $sts->group }}</td>
                  </tr>
                @endif
                  <tr>
                      <td class="tg-0pky" colspan="4">{{ $sts->name }}</td>
                        @if($sts->type=='signature')
                          <td class="tg-0pky" colspan="8">
                          @if(file_exists(public_path('storage/uploads/' . $detail->value . '.jpeg')))
                              <img src="{{ public_path('storage/uploads/') }}{{ $act_dtl->value }}.jpeg" style="height: 50px">
                          @else
                              <img src="{{ public_path('storage/uploads/') }}{{ $act_dtl->value }}.png" style="height: 50px">
                          @endif
                          </td>
                        @else
                          <td class="tg-0pky" colspan="8">{{ $value }}</td>
                        @endif
                  </tr>
              @endif
            @elseif($sts->type=='file')
             <?php $count_image ++; ?>
              <tr>
                <td class="subtitle2" colspan="12">
                  @if($count_image<=1)
                  <table class="tg" style="table-layout: fixed; width: 100%;padding-top: 20px">
                  @else <table class="tg" style="table-layout: fixed; width: 100%;padding-top: 80px">
                  @endif
                    @foreach($act_dtl->files as $files)
                            <tr>
                              <td class="tg-0pky"colspan="12"> <img src="{{ public_path('storage/uploads/') }}{{ $files->filename }}" style="height: 500px;display: block;text-align:center;vertical-align:middle;max-width:100%;max-height:100%;">
                              </td>
                            </tr>
                            <tr>
                              <td class="subtitle2" style="text-align: center;" colspan="12"> {{ $act_dtl->detail->name }}</td>
                            </tr>
                    @endforeach
                    </table>
                </td>
              </tr>
            @elseif($sts->type=='hide' && $sts->property=='fieldtech')
                <tr>
                  <td class="subtitle2" style="font-weight: bold; text-align: center;" colspan="12">
                    <table class="tg" style="table-layout: fixed; width: 100%;padding-top: 20px">
                      <tr>
                        <td class="subtitle2" colspan="2" rowspan="4">
                          @if(file_exists(public_path('storage/uploads/' . $act_dtl->fieldtech->photo . '.jpeg')))
                            <img src="{{ public_path('storage/uploads/') }}{{ $act_dtl->fieldtech->photo }}.jpeg" style="height: 100px">
                          @elseif(file_exists(public_path('storage/uploads/' . $act_dtl->fieldtech->photo . '.png')))
                            <img src="{{ public_path('storage/uploads/') }}{{ $act_dtl->fieldtech->photo }}.png" style="height: 100px">
                          @else
                          <img src="{{ public_path('images/nouser.png') }}" style="height: 100px">
                          @endif
                        </td>
                        <td class="subtitle2" style="font-size: 10px;" colspan="10">Vendor : {{ $data->vendor->name }}</td>
                      </tr>
                      <tr>
                        <td class="subtitle2" style="font-size: 10px;" colspan="10">Name : {{ $act_dtl->fieldtech->name }}</td>
                      </tr>
                      <tr>
                        <td class="subtitle2" style="font-size: 10px;" colspan="10">Phone : {{ $act_dtl->fieldtech->phone }}</td>
                      </tr>
                      <tr>
                        <td class="subtitle2" style="font-size: 10px;" colspan="10">Email : {{ $act_dtl->fieldtech->email }}</td>
                      </tr>
                      <tr style="border: none;">
                        <td class="subtitle2" style="font-style: italic;font-size: 10px;"colspan="12">Assigned Fieldtech</td>
                      </tr>
                    </table>
                  </td>
                </tr>
            @endif
          @endif
        @endforeach
      @endforeach
      </table>
      <div class="page-break"></div>
    @endforeach
     @if($data->parts!='[]')
     <table class="tg" style="table-layout: fixed; width: 100%;padding-top: 80px">
      <tr>
          <td class="tg-c3ow" colspan="12">Bill Of Quantity</td>
        </tr>
        <tr>
          <td class="tg-0pky">No</td>
          <td class="tg-0pky" colspan="3">Name</td>
          <td class="tg-0pky" colspan="3">Model</td>
          <td class="tg-0pky" colspan="2">Serial No</td>
          <td class="tg-0pky" colspan="3">Description</td>
        </tr>
        <tr>
          <td class="tg-0pky" colspan="12">A. Perangkat</td>
        </tr>
        <?php $count = 0; ?>
        @foreach($data->parts as $part)
        @if($part->type=="EQUIPMENT")
        <?php $count++ ?>
        <tr>
          <td class="tg-0pky">{{ $count }}.</td>
          <td class="tg-0pky" colspan="3">{{ $part->name }}</td>
          <td class="tg-0pky" colspan="3">{{ $part->model }}</td>
          <td class="tg-0pky" colspan="2">{{ $part->serial }}</td>
          <td class="tg-0pky" colspan="3">{{ $part->description }}</td>
        </tr>
        @endif
      @endforeach
        <tr>
          <td class="tg-0pky" colspan="12">B. Material</td>
        </tr>
        <?php $count = 0; ?>
        @foreach($data->parts as $part)
        @if($part->type=="MATERIAL")
        <?php $count++ ?>
        <tr>
          <td class="tg-0pky">{{ $count }}.</td>
          <td class="tg-0pky" colspan="3">{{ $part->name }}</td>
          <td class="tg-0pky" colspan="3">{{ $part->model }}</td>
          <td class="tg-0pky" colspan="2">{{ $part->serial }}</td>
          <td class="tg-0pky" colspan="3">{{ $part->description }}</td>
        </tr>
        @endif
      @endforeach
    </table>
    @endif
</body>
