<div class="wo-container">
    <div class="indicator">
        <div class="icon" style="border-color: #{activity.color}"></div>
        <div class="line"></div>
    </div>
    <div class="container info-container">
        <div id="{id}" class="detail-wo" style="display: flex; cursor: pointer">
            <div style="flex: 1; border-right: 1px solid #eee; text-align: left">
                <span>Workorder ({id})</span>
                <div class="box-color" style="margin-right: 5px;text-align: center;background: #{activity.color}">{activity.alias}</div>
                <div class="box-color" style="margin-right: 5px;text-align: center;background: #{service.color}">{service.alias}</div>
            </div>
        </div>
        <div class="info">
            <i>(Open: {start_date}, Close: {close_date})</i>
        </div>
        <div style="flex: 1; border-right: 1px; margin-top: 5px; solid #eee; text-align: left">
            <span>Area</span>
            <div style="font-size: 11px;">({vendor.alias}) {vendor.name}</div>
        </div>
        <div style="font-size: 12px; padding: 2px 0px; color: #999">
            {description}
        </div>
    </div>

</div>
