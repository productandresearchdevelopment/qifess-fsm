<div class="part-container">
    <div class="container">
        <div class="button-menu-part" id="{id}">
            <img src="{{ asset('images/icons/menu_dotted.png') }}">
        </div>
        <div id="menu-part-{id}" class="menu-part-container" style="display: none">
            <div id="part-edit-{id}" class="menu edit" style="border-bottom: 1px solid #eee">Edit</div>
            @if($user->hasRoute('wo.delete'))
            <div id="part-delete-{id}" class="menu delete">Delete</div>
            @endif
        </div>
        <span>
            <div style="font-size: 11px">{type}</div>
            <div style="font-size: 15px; padding-right: 15px">{name} ({code})</div>
            <div style="font-size: 11px">{model}</div>
        </span>
        <div style="font-size: 11px">Date: {date}</div>

        <div style="padding: 5px 0px">SN: {serial}</div>
        <div style="color: #666; font-size: 11px">{description}</div>
        <span style="margin-top: 10px">PHOTO</span>
        <div class="files">{photos}</div>
    </div>
</div>
