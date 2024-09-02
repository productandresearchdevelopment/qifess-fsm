 Ext.define('Ext.ux.menu.MonthPicker', {
    extend: 'Ext.menu.Menu',
    alias: 'widget.monthmenu',
    requires: ['Ext.picker.Month'],
    hideOnClick : true,
    pickerId : null,
    initComponent : function(){
        var me = this, cfg = Ext.apply({}, me.initialConfig);
        delete cfg.listeners;
        Ext.apply(me, {
            showSeparator: false,
            plain: true,
            bodyPadding: 0, // remove the body padding from the datepicker menu item so it looks like 3.3
            items: Ext.applyIf({
                xtype: 'monthpicker',
                cls: Ext.baseCSSPrefix + 'menu-date-item',
                margin: 0,
                border: false,
                id: me.pickerId,
                focusOnShow: true,
                showToday: me.showToday,
                value : [new Date().getMonth(), new Date().getFullYear()]
            }, cfg)
        });

        me.callParent(arguments);
        me.picker = me.down('monthpicker');
        me.relayEvents(me.picker, ['OkClick']);

        if (me.hideOnClick) {
            me.on('OkClick', me.hidePickerOnSelect, me);
        }
    },

    hidePickerOnSelect: function() {
        Ext.menu.Manager.hideAll();
    }
 });