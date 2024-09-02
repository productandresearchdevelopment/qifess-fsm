<h3>WO {{ $wo->id ?? '-' }}</h3>
<ul>
    <li>Date: {{ date('d M Y', strtotime($wo->created_at)) }}</li>
    <li>Owner: {{ $wo->owner ? $wo->owner->name : '-' }}</li>
    <li>Activity: {{ $wo->activity ? $wo->activity->name : '-' }}</li>
    <li>Service: {{ $wo->service ? $wo->service->name : '-' }}</li>
    <li>Client: {{ $wo->client ? $wo->client->name : '-' }}</li>

    @if($wo->site_id)
        <li>Site: {{ $wo->site ? $wo->site->name : '-' }}</li>
    @endif

    @if($wo->remove_site_id)
        <li>Dismantle Site: {{ $wo->removeSite ? $wo->removeSite->name : '' }}</li>
    @endif

    @if($wo->vendor_id)
        <li>Vendor: {{ $wo->vendor ? $wo->vendor->name : '-' }}</li>
    @endif

    @if($wo->fieldtech_id)
        <li>Fieldtech: {{ $wo->fieldtech ? $wo->fieldtech->name : '' }}</li>
    @endif

    <li>Description: {{ $wo->description ?? '-' }}</li>

    @if($wo->createdBy)
        <li>Created By: {{ $wo->createdBy ? $wo->createdBy->name : '' }}</li>
    @endif
</ul>

<div style="border-bottom: 1px dotted #666"></div>

<h3 style="color: #{{$action->status->color}}">{{ $action->status->name ?? '-' }}</h3>
<ul>
    <li>Date: {{ date('d M Y H:i', strtotime($action->created_at)) }}</li>
    <li>Note / Description: {{ $action->note ?? '-' }}</li>
    @if($action->createdBy)
        <li>Created By: {{ $action->createdBy ? $action->createdBy->name : '' }}</li>
    @endif
</ul>
