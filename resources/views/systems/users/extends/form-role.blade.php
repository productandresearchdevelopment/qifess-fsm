<script>
var FormsRole = function(){
    let me = Ext.utils.windowForms(this);

    me.init = function(){
        me.form = Ext.widget('form', {
            id: 'forms_role',
            bodyPadding: 10,
            autoHeight: true,
            border: false,
            items : [
                {xtype: 'hidden', name: '_token', value: '{{ csrf_token() }}'},
                {xtype: 'hidden', name: '_method', value: 'PUT'},
                {xtype: 'hidden', name: 'data', value: ''},
                {
                    xtype: 'combo', name: 'role_id', anchor: '100%', allowBlank: false,
                    forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all',
                    displayField: 'name', valueField: 'id',
                    store: Ext.create('Ext.data.Store', {fields : ['name', 'id'], data : roles})
                }
            ],

            buttons: [
                { text: 'Save', iconCls: 'icon-save-bright', handler: me.save },
                { text: 'Cancel', iconCls:'icon-close', handler: me.close }
            ]
        });

        me.createWindowForm('Role', me.form, {width: 300, maximized: false});
    };

    me.show =  function(){
        let recs = grids.getValues();
        if(recs.length){
            me.win.show();
            me.reset();
            me.setField('data', Ext.encode(recs));
        }
        else Ext.example.msg('Warning!', 'Please select data!');
    };

    me.close = function(){
        me.win.hide();
    };

    me.save = function(){
        me.submit('{{ route('auth.user.set.role') }}', {success: grids.storeLoad});
    }

}

</script>
