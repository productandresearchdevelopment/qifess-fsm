ai.mask = {
    show: function(title){
        var dialog = document.getElementById('ai-mask');
        if(dialog){
            dialog.show();
            title = (title == undefined) ? 'Loading' : title;
            $('#ai-mask .title').html(title);
        }
    },

    hide: function () {
        let modal = document.getElementById('ai-mask');
        if(modal) modal.hide();
    },

    init: function(){
        $('body').prepend(`
            <ons-modal id="ai-mask" class="ai-mask" onclick="ai.mask.hide()">
                <div class="container" style="text-align: center; background: #EEEEEE; display: inline-block; padding: 10px 20px; border-radius: 5px">
                    <ons-icon icon="fa-spinner" style="padding-bottom: 5px; font-size: 20px; color: #333333" spin></ons-icon>
                    <div class="title" style="color: #333333" >Loading</div>
                </div>
            </ons-modal>
        `);
    }
}

ai.mask.init();

