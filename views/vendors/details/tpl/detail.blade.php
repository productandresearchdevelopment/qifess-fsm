
<div class="vendor-detail">
    <div class="header-title">Information</div>
    <div>       
        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px; border-right: 1px solid #eee; text-align: left">
                <span>NAME</span>
                ({alias}) {name}
            </div>
        </div>                 
        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px; border-right: 1px solid #eee; text-align: left">
                <span>CONTACT</span>
                <div class="info">
                    <i>(Phone: {phone}, Email: {email})</i>
                </div>
            </div>
        </div>  
        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px; border-right: 1px solid #eee; text-align: left">
                <span>ADDRESS</span>
                {address}
            </div>
        </div> 
        <div class="container info-container">
        <span>FIELDTECH REGISTERED</span>
        {fieldteches_count} fieldtechs<br>
        </div>
        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px; border-right: 1px solid #eee; text-align: left">
                <span>ATTACHMENT</span>
                {attachment}
            </div>
        </div>         
        <div class="container info-container">
        <span>WORK ORDER</span>
        {workorders_count} ticket<br>
        </div>
    </div>
    <div id="list-wo" class="header-title" style="margin-top: 20px">History Workorders</div>
        <div style="margin: 10px 0px;">
        <div class="container" style="padding: 10px 10px;">

                    {woTpl}

        </div>
        </div>

</div>


