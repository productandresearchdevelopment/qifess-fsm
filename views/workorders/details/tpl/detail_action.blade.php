<div class="action-container">
    <div class="indicator">
        <div class="icon" style="border-color: #{status.color}"></div>
        <div class="line"></div>
    </div>

    <div class="content">
        <div id="title-{id}" class="dropdown-detail" style="display: flex; cursor: pointer">
            <span style="flex: 1; font-size: 12px; color: #{status.color}">{status.name}</span>
            <div id="toggle-detail-{id}" class="toggle-detail" style="{toggleDetailDisplay}"><i class="fa fa-chevron-right"></i></div>
        </div>
        <div style="display: block; width: 200px;font-size: 10px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap">
            {created_date} ({created_by.name})
        </div>
        <div style="font-size: 12px; padding: 2px 0px; color: #999">
            {note} {view_map}
        </div>
        <div id="action-detail-{id}" class="action-detail-container">{actionDetails}</div>
    </div>

</div>

