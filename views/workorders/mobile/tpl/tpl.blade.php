<ons-card  modifier="material" class="card-list">
    <div style="display: flex; margin-bottom: 5px">
        <div class="small-text" style="flex: auto">OPEN {open}</div>
        {expire}
    </div>
    <div class="title">{site_name}</div>
    <div style="font-size: 12px; line-height: 25px">WO {id}</div>
    {activity}
    <div class="small-box bright-text" style="background: #666">{client_name}</div>
    <div class="content">{description}</div>
    <div class="footer">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="20">{status}</td>
                <td class="assign">{vendor_name} <br> {fieldtech_name}</td>
                <td width="50" class="popup-action" align="center">
                    <i class="fa fa-ellipsis-h" style="padding: 0px 7px; color: #444"></i>
                </td>
            </tr>
        </table>
    </div>
</ons-card>
