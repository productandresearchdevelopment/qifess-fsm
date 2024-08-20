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
    table{width: 100%}

    .page-break {page-break-after: always;}
    header {position: fixed; top: -10px; left: 80%; right: 0px;height: 30px; }
    footer {position: fixed; bottom: -60px; left: 70%; right: 0px; height: 50px; }
</style>

<body>
    <img src="{{ public_path('images/logo.jpg') }}" style="height: 40">
    <table style="border-bottom: 2px solid #ccc;">
        <tr>
            <td style="padding-bottom: 10px" valign="top">
                <div style="font-size: 28px; color: #333333;">Workorder</div>
                <div style="font-size: 18px; color: #333333; font-weight: 600;">
                    {{ $data->activity->name }}, #{{ $data->id }}
                    <br>
                    <span style="font-size: 11px;">
                        Created At: {!! $data->created_at ? date('d F Y H:i', strtotime($data->created_at)) : '' !!}
                        <br>Open By: {!! $data->createdBy ? $data->createdBy->name : '-' !!}
                    </span>


                </div>
            </td>
            <td width="250" style="padding-bottom: 10px">
                <div style="padding: 5px 20px 20px 20px; border: 1px solid #eeeeee;">
                    <span style="font-size: 11px; font-weight: 600;">CLIENT</span>
                    <div style="font-size: 13px;  font-weight: 600">{{ $data->client->name }}</div>
                    <span style="font-size: 12px;">
                        {{ $data->client->address }}
                        <br>Email: {{ $data->client->email }}
                        <br>Phone: {{ $data->client->phone }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <div style="background: #FAFAFA; margin-top: 20px">
        <table style="border: 1px solid #eee; padding: 20px; vertical-align: top">
            <tr>
                <td width="50%" valign="top">
                    <table style="font-size: 12px">
                        <tr>
                            <td valign="top">AREA</td>
                            <td valign="top">:</td>
                            <td valign="top" style="font-weight: 600">
                                {{ $data->vendor->name }}
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">SERVICE</td>
                            <td valign="top">:</td>
                            <td valign="top" style="font-weight: 600">{{ $data->service ? $data->service->name : '-' }}</td>
                        </tr>
                        <tr>
                            <td width="80px" valign="top">SITE</td>
                            <td width="20px"  valign="top">:</td>
                            <td valign="top">
                                <span style="font-size: 13px; font-weight: 600">{{ $data->site->name }}</span>
                                <br>
                                <span style="font-size: 12px;">
                                    {{ $data->site->address }}
                                    <br>Email: {{ $data->site->pic_email }}
                                    <br>Phone: {{ $data->site->pic_phone }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="50%" valign="top">
                    <table style="font-size: 12px">
                        <tr>
                            <td width="100px" valign="top">BOOKING DATE</td>
                            <td width="20px" valign="top">:</td>
                            <td valign="top" style="font-weight: 600">{!! $data->start_date ? date('d F Y', strtotime($data->start_date)) : '' !!}</td>
                        </tr>
                        <tr>
                            <td valign="top">SLOT</td>
                            <td valign="top">:</td>
                            <td valign="top" style="font-weight: 600">{!! $data->slot ? $data->slot->name : '' !!}</td>
                        </tr>
                        <tr>
                            <td valign="top">TEAM</td>
                            <td valign="top">:</td>
                            <td valign="top">
                                @if($data->fieldtech)
                                    <span style="font-weight: 600">{{ $data->fieldtech->name }}</span>
                                    @foreach($data->fieldtech->users AS $row)
                                        <br> - {{ $row->name }} ({{ $row->phone }})
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div style="padding: 5px 20px 20px 20px; border: 1px solid #eeeeee; margin-top: 20px;">
        <span style="font-size: 10px; font-weight: 600; margin-bottom: 5px;">DESCRIPTION</span>
        <br>
        <span style="font-size: 13px;">{{ $data->description }}</span>
    </div>

    <div style="padding: 5px 20px 20px 20px; border: 1px solid #eeeeee; margin-top: 20px;">
        <div style="font-size: 25px; color: #333; margin-bottom: 5px; border-bottom: 1px solid #666; padding-bottom: 5px;">
            History Status
        </div>
        <br>
        @foreach($data->actions AS $action)
        <div>
            <table border="0" width="100%">
                <tr>
                    <td style="font-size: 30px; width: 30px; line-height: 25px" valign="top">&raquo;</td>
                    <td valign="top">
                        <div style="font-size: 16px; font-weight: 600;">{{ $action->status->name }}</div>
                        <div style="font-size: 10px;">
                            Updated At: {{ date('d/m/Y, H:i', strtotime($action->created_at)) }},&nbsp;
                            [ By: {{ $action->createdBy->name }} ]
                        </div>
                        <div style="font-size: 10px; margin-top: 5px; margin-bottom: 10px;">
                            <div style="font-weight: 600;">Notes:</div> {{ $action->note ?: '-' }}
                        </div>
                    </td>
                </tr>
            </table>
            @if(count($action->status->details))
                <div style="margin-left: 37px">
                    @foreach($action->status->details AS $statusDetail)
                        @php
                        $detail = \App\Models\WorkOrders\ActionDetail::where('action_id',$action->id)
                            ->where('detail_id', $statusDetail->id)
                            ->first();
                        @endphp
                        <div style="margin-bottom: 10px;">
                            <div style="font-weight: 600; font-size: 10px;">{{$statusDetail->name}} : </div>
                            @if($statusDetail->triger == 'wo.fieldtech')
                                <div style="font-size: 12px;">{{$detail ? $detail->fieldtech->name : '-'}}</div>
                            @elseif($statusDetail->triger == 'wo.startdate')
                                <div style="font-size: 12px;">{{ $detail ? date('d/m/Y', strtotime($detail->value)) : '-' }}</div>
                            @elseif($statusDetail->triger == 'wo.slot')
                                <div style="font-size: 12px;">{{ $detail ? $detail->slot->name : '-' }}</div>
                            @elseif($statusDetail->type == 'file')
                                <div style="font-size: 12px;">
                                    @if($detail)
                                    @else
                                        @foreach($detail->files AS $file)
                                            @if($file->type == 'image')
                                                <img style="height: 200px; border: 1px solid #CCC; margin: 10px;" src="{{ storage_path("app/public/uploads/".$file->filename) }}">
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            @elseif($statusDetail->type == 'signature')
                                <div style="font-size: 12px;">
                                    @if($detail)
                                    @else
                                        @if($fsign = \App\SystemModels\Globals\Upload::find($detail->value))
                                            <img style="height: 200px; border: 1px solid #CCC; margin: 10px;" src="{{ storage_path("app/public/uploads/".$fsign->filename) }}">
                                        @endif
                                    @endif
                                </div>
                            @elseif($statusDetail->type == 'date')
                                <div style="font-size: 12px;">{{ $detail ? date('d/m/Y', strtotime($detail->value)) : '-' }}</div>
                            @elseif($statusDetail->type == 'datetime')
                                <div style="font-size: 12px;">{{ $detail ? date('d/m/Y H:i:s', strtotime($detail->value)) : '-' }}</div>
                            @elseif($statusDetail->type == 'combo')
                                <div style="font-size: 12px;">{{ $detail ? ($detail->valueOption ? $detail->valueOption->option : '-') : '-' }}</div>
                            @else
                                <div style="font-size: 12px;">{{ $detail ? ($detail->value ?: '-') : '-'}}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endforeach
    </div>
</body>
