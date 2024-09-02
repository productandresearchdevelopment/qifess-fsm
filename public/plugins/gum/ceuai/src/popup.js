/*

*/

ai.Popup = function(option){
    let me = this;

    me.id = ai.autoId('popup-menu');
    me.buttonId = null;
    me.type = 'popover';
    me.items = [];

    me.init = function(option){
        if(!isNull(option.id)) me.id = option.id;
        if(!isNull(option.buttonId)) me.buttonId = option.buttonId;
        if(!isNull(option.type)) me.type = option.type;

        let poptpl = '';
        switch (me.type) {
            case 'sheet': poptpl = me.actionSheetTpl(); break;
            case 'modal': poptpl = me.actionModalTpl(); break;
            default:  poptpl = me.popoverTpl(); break;
        }
        $('body').append(poptpl);
        $(function () {
            $('#'+me.buttonId).click(me.show);
            if(option.items) {
                option.items.forEach(function (item){
                    me.items.push(new me.MenuItem(me, item));
                });
            }
        });
        $('#'+me.id).click(me.hide);
    }

    me.popoverTpl = function(){
        let tpl = `<ons-popover modifier="material" id="{0}" class="ai-popup" cancelable direction="down" cover-target>
                        <ons-list modifier="material"  id="{0}-list" class="ai-popup-list"> </ons-list>
                   </ons-popover>`;
        return String.format(tpl, me.id);
    }

    me.actionSheetTpl = function(){
        let tpl = `<ons-action-sheet modifier="material" id="{0}" cancelable class="ai-popup">
                        <ons-list  id="{0}-list" class="ai-popup-list"> </ons-list>
                   </ons-action-sheet>`;
        return String.format(tpl, me.id);
    }

    me.actionModalTpl = function(){
        let tpl = `<ons-modal modifier="material" id="{0}" cancelable class="ai-popup">
                        <ons-list  id="{0}-list" style="margin: 20px" class="ai-popup-list"> </ons-list>
                   </ons-modal>`;
        return String.format(tpl, me.id);
    }

    me.show = function(){
        if(me.type == 'popover') {
            let target = document.getElementById(me.buttonId);
            document.getElementById(me.id).show(target);
        }
        else document.getElementById(me.id).show();
    }

    me.hide = function(){
        document.getElementById(me.id).hide();
    }

    me.MenuItem = function(parent, option){
        let me = this;

        me.id = ai.autoId('popup-menu-item');
        me.text = '';
        me.value = null;
        me.hidden = false;
        me.iconLeft = null;
        me.iconRight = null;
        me.handler = null;
        me.menu = null;
        me.parent = parent;

        me.init = function(option){
            if(!isNull(option.id)) me.id = (!isNull(option.items) && option.items.length) ? option.id+'-btn' : option.id;

            if(option == '-') $('#' + parent.id + '-list').append(String.format('<div id="{0}" class="popup-line"></div>', me.id));
            else {
                if(!isNull(option.text)) me.text = option.text;
                if(!isNull(option.value)) me.value = option.value;
                if(!isNull(option.handler)) me.handler = option.handler;
                if(!isUndef(option.hidden)) me.hidden = option.hidden;

                if(!isNull(option.icon)) {
                    if (typeof option.icon == 'string') me.iconLeft = option.icon;
                    else {
                        if(!isNull(option.icon[0])) me.iconLeft = option.icon[0];
                        if(!isNull(option.icon[1])) me.iconRight = option.icon[1];
                        if(!isNull(option.icon.left)) me.iconLeft = option.icon.left;
                        if(!isNull(option.icon.right)) me.iconRight = option.icon.right;
                    }
                }
                if(!isNull(option.iconLeft)) me.iconLeft = option.iconLeft;
                if(!isNull(option.iconRight)) me.iconRight = option.iconRight;

                let tpl = `<ons-list-item  modifier="material" id="{id}" class="ai-popup-item" tappable>
                                <div class="left icon-left">{iconLeft}</div>
                                <div class="center popup-text">{text}</div>
                                <div class="right icon-right">{iconRight}</div>
                           </ons-list-item>`;
                tpl = String.format(tpl, {
                    id: me.id,
                    text: me.text,
                    iconLeft: me.iconLeft ? '<ons-icon class="menu-icon" icon="' + me.iconLeft + '"></ons-icon>' : '',
                    iconRight: me.iconRight ? '<ons-icon class="menu-icon" icon="' + me.iconRight + '"></ons-icon>' : '',
                })
                $('#' + parent.id + '-list').append(tpl);

                if(!isNull(option.items) && option.items.length){
                    option.buttonId = me.id;
                    me.menu = new ai.popupSheet(option);
                }

                $(function () {
                    $('#' + me.id).click(function () {
                        if(me.handler) me.handler(me.value, me);
                        parent.hide();
                    });
                    if (me.hidden) me.hide();
                })
            }
        }

        me.show = function(){
            $('#'+me.id).show();
        }

        me.hide = function(){
            $('#'+me.id).hide();
        }

        me.init(option);
    }

    me.init(option);
}

ai.popupSheet = function(option) {
    option.type = 'sheet';
    return new ai.Popup(option);
}

ai.popupModal = function(option) {
    option.type = 'modal';
    return new ai.Popup(option);
}
