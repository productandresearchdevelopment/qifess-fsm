<script>

    var Forms = function(){
        var me = this;
        
        me.init = function(){
            me.roles  = roles; 
            me.grids = grids;
            
            me.form = Ext.widget('form', {
                bodyPadding: '10 15 10 15',
                autoHeight: true,
                border: false,
                fieldDefaults: {labelAlign: 'left', labelWidth: 80, msgTarget: 'side'},
                defaults: {anchor: '100%'},
                items: [
                    {xtype: 'hidden', name: 'type_id'},
                    {xtype: 'hidden', name: '_method'},
                    {xtype: 'hidden', name: '_token', value: '{{ csrf_token() }}'},

                    {xtype: 'textfield', fieldLabel : 'Title', name: 'text'},

                    Ext.create('Ext.ux.form.field.TreeCombo',{
                        name: 'parent', fieldLabel: 'Parent', 
                        rootVisible: true, canSelectFolders: true, editable: false, 
                        store: Ext.create('Ext.data.TreeStore',{
                            folderSort: false,
                            root: {id: '0', text: 'Root', icon: '{{ asset('images/icons/home.png') }}', expanded: true},
                            proxy: {type: 'ajax', url: '{{ route('auth.module.data') }}'},
                        })  
                    }),

                    {xtype: 'textfield', fieldLabel : 'Url', name: 'url'},

                    {
                        xtype: 'combo', name: 'route', fieldLabel : 'Route', 
                        forceSelection: true, editable: true, queryMode: 'local', triggerAction: 'all', 
                        displayField: 'id', valueField: 'id',
                        store: Ext.create('Ext.data.Store', {
                            fields : ['id', 'url', 'prefix', 'method', 'action'], 
                            data : dataRoutes
                        })
                    },

                    {xtype: 'textfield', fieldLabel : 'Param', name: 'param'},

                    {xtype: 'textfield', name: 'auth', fieldLabel : 'Auth'},

                    {
                        xtype: 'fieldcontainer', id: 'form-icon-container', layout: {type: 'hbox', align: 'stretch'},
                        items:[
                            {xtype: 'textfield', id: 'form-icon-text', name: 'icon', fieldLabel : 'Icon', flex: 1},
                            {
                                xtype: 'button', iconCls: 'icon-search-bright', margin: '0 0 0 5', 
                                handler: function(){
                                    formIcons.show();
                                }
                            },
                        ]
                    },

                    {
                        xtype: 'combo', name: 'device', fieldLabel : 'Device', 
                        forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all', 
                        displayField: 'name', valueField: 'id',
                        store: Ext.create('Ext.data.Store', {
                            fields : [{name: 'id', type: 'int'}, {name: 'name', type: 'string'}], 
                            data : [
                                {id: 0, name: 'All Device'},
                                {id: 2, name: 'Desktop Only'},
                                {id: 1, name: 'Mobile Only'},
                            ]
                        })
                    },

                    {xtype: 'textfield', fieldLabel : 'Description', name: 'description'},
                    {
                        xtype: 'checkboxgroup', columns: 2, margin : '0 0 0 80',
                        items: [
                            {xtype: 'checkbox', name: 'is_active', boxLabel: 'Active', inputValue: 1, checked: true},
                            {xtype: 'checkbox', name: 'is_locked', boxLabel: 'Locked', inputValue: 1, checked: true},
                        ]
                    }
                ],
                buttons: [
                    {text: 'Save', iconCls:'icon-save-bright', handler: me.save},
                    {text: 'Cancel', iconCls:'icon-cancel', handler: me.close} 
                ]
            });
            
            me.win = Ext.widget('window', {
                id : 'win-module',
                title: 'Create Modules', 
                closeAction: 'hide', 
                width: 400,
                resizable: false, 
                modal: true, 
                autoHeight: true,
                items: me.form
            });
        }
        
        me.show = function(type, title){
            me.win.show();
            title = '&nbsp; '+title;
            var form = me.form.getForm();
            switch(parseInt(type)){
                @foreach ($types as $type)
                    case {{ $type->id }} : 
                        me.win.setTitle(title+' {{ $type->name }}'); 
                        me.win.setIconCls('icon-{{ $type->icon }}'); 
                        form.findField('url').{{ $type->xurl ? 'show()' : 'hide()' }}; 
                        form.findField('route').{{ $type->xroute ? 'show()' : 'hide()' }}; 
                        form.findField('param').{{ $type->xroute ? 'show()' : 'hide()' }}; 
                        form.findField('auth').{{ $type->xauth ? 'show()' : 'hide()' }}; 
                        Ext.getCmp('form-icon-container').{{ $type->xicon ? 'show()' : 'hide()' }}; 
                        form.findField('device').{{ $type->xdevice ? 'show()' : 'hide()' }}; 
                        break;
                @endforeach
            }
            form.reset();
        }
        
        me.close = function(){
            me.win.hide();
        }
        
        me.create = function(menu){
            var form  = me.form.getForm();
            var rec = me.grids.getRec();
            
            me.show(menu.value, 'Create');
            form.url = '{{ route('auth.module.create') }}';
            form.findField('type_id').setValue(menu.value);
            form.findField('parent').store.load();
            if(rec) form.findField('parent').setValue(rec.id);
            else form.findField('parent').setValue('0');
        }
        
        me.edit = function(){
            var form = me.form.getForm();
            var rec = me.grids.getRec();
            if(rec){
                me.show(rec.type_id, 'Edit');

                form.url = '{{ route('auth.module.update', '') }}/' + rec.id;

                form.findField('_method').setValue('PUT');
                form.findField('type_id').setValue(rec.type_id);
                form.findField('parent').setValue(rec.parent ? rec.parent : '0');
                form.findField('text').setValue(rec.text);
                form.findField('icon').setValue(rec.menuIcon);
                form.findField('route').setValue(rec.route);
                form.findField('param').setValue(rec.param);
                form.findField('url').setValue(rec.url);
                form.findField('is_active').setValue(rec.is_active);
                form.findField('is_locked').setValue(rec.is_locked);
                form.findField('auth').setValue(rec.auth);
                form.findField('device').setValue(rec.device);
                form.findField('description').setValue(rec.description);
            }
            else Ext.example.msg('Warning', 'Please Select Data!');
        }
        
        me.save = function(){
            var form    = me.form.getForm();
            Ext.MessageBox.confirm('Confirm', 'Save data module?', function(res){
                if(res=='yes'){
                    form.submit({
                        waitMsg : 'Proses',
                        success : function(obj, respon){
                            me.grids.selected = parseInt(respon.result.id);
                            me.grids.storeLoad();
                            me.close();
                        }            
                    });
                }
            });
        }
    }

</script>