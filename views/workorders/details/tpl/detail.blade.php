<div class="wo-detail">
    <div class="header-title">Information</div>
    <div>
        <div class="container info-container">
            <span>Workorder Id</span> {id}
        </div>

        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px; border-right: 1px solid #eee; text-align: left">
                <span>Activity</span>
                {activity.name} <br>
                <div class="box-color" style="background: #{activity.color}">{activity.alias}</div>
            </div>
        </div>

        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px;">
                <span>Booking Date</span>
                {start_date} <br>
                {slot_name}
            </div>
        </div>

        <div class="container info-container">
            <span>Last Action (Status) & Duration</span>
            <table width="100%" style="margin-top: 0px" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding: 0px 10px 0px 0px">
                        <div style="font-size: 15px; color: #{last_action_status.color}">{last_action_status.name}</div>
                        <div style="font-size: 11px">
                            <i>{last_action.by} ({last_action.date})
                        </div>
                    </td>
                    <td width="10" style="padding-left: 10px; color: #{last_action.duration_color};">
                        <div style="font-size: 23px; text-align: center">{last_action.duration}</div>
                        <div style="font-size: 11px; text-align: center">Day's</div>
                    </td>
                </tr>
            </table>
        </div>

        {clientTpl}

        {vendorTpl}

        {siteTpl}

        {removeSiteTpl}

        {fieldtechTpl}

        <div class="container info-container">
            <span>Description</span>
            {description}
        </div>

        <div class="container info-container">
        <span>Created By</span>
        {created_by.name} <br>
        <i style="font-size: 10px">{created_at}</i>
    </div>
    </div>

    <div class="header-title" style="margin-top: 20px">History Status</div>
    <div style="margin: 10px 0px;">
        <div class="container" style="padding: 10px 10px;">
            <div class="action-container">
                <div class="indicator" style="margin: 0px">
                    <div class="startend">Start</div>
                    <div class="line" style="height: 15px; margin: 0px 0px 0px 15px"></div>
                </div>
            </div>

            {actionsTpl}

            <div class="action-container">
                <div class="indicator" style="margin: 0px">
                    <div class="line" style="height: 0px; margin: 0px 0px 0px 15px"></div>
                    <div class="startend" style="margin-left: 1px">End</div>
                </div>
            </div>
        </div>
    </div>

    <a href="{link_pdf}" target="_blank" style="text-decoration: none">
        <div class="btn" id="download-pdf" style="background: #CC0000">DOWNLOAD PDF</div>
    </a>

{{--
    <div class="part" style="position: relative">
        <div class="header-title" style="margin-top: 20px">Spare Part</div>
        {partTpl}
        <div class="btn" id="add-part-btn">ADD SPAREPART</div>
    </div>


    <a href="{link_bast}" target="_blank" style="text-decoration: none">
    <div class="btn" id="download-bast-btn" style="background: #CC0000">DOWNLOAD BAST</div>
    </a>

    <a href="{link_wo}" target="_blank" style="text-decoration: none">
    <div class="btn" id="download-doc" style="background: #CC0000">DOCUMENTATION</div>
    </a>
--}}

</div>
