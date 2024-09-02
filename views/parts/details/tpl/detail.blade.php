
<div class="client-detail">
    <div class="header-title">Information</div>
    <div> 
      <div class="container info-container" style="display: flex; padding: 0px">
        <a href="{url}" target="_blank">
            <div style="display: flex">
                <img class="circle-photo" src="{photo}">
                <div style="flex: 1; text-align: left; display: inline-block; padding-top: 5px">
                </div>
            </div>
        </a>
        </div>  
        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px; border-right: 1px solid #eee; text-align: left">
                <span>NAME</span>
                {name}
            </div>
        </div>                 
        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px; border-right: 1px solid #eee; text-align: left">
                <span>MODEL</span>
                 {model}
            </div>
        </div>  
        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px; border-right: 1px solid #eee; text-align: left">
                <span>PART NO</span>
                {code}
            </div>
        </div> 
        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px; border-right: 1px solid #eee; text-align: left">
                <span>SERIAL NO</span>
                {serial}
            </div>
        </div> 
        <div class="container info-container" style="display: flex; padding: 0px">
            <div style="flex: 1; padding: 15px; border-right: 1px solid #eee; text-align: left">
                <span>DESCRIPTION</span>
                {description}
            </div>
        </div>                 
    </div>
    <div id="list-wo" class="header-title" style="margin-top: 20px">History Workorders</div>
        <div style="margin: 10px 0px;">
        <div class="container" style="padding: 10px 10px;">

                    {woTpl}

        </div>
        </div>

</div>


