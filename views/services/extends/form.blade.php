<script>
    var Forms = function(){
        let me = Ext.utils.windowForms(this);

        me.init = function(){
            me.form = Ext.widget('form', {
                bodyPadding     : 10,
                autoHeight      : true,
                border          : false,
                autoScroll      : true,
                defaultType     : 'textfield',
                fieldDefaults   : { labelAlign: 'top',  labelWidth: 90,  msgTarget: 'side', anchor: '100%', allowBlank: false},
                items: [
                    {xtype: 'hidden', name: '_method'},
                    {xtype: 'hidden', name: '_token', value : '{{csrf_token()}}' },
                    {xtype: 'hidden', name: 'id', allowBlank:true},

                    {
                        xtype: 'fieldcontainer', defaultType: 'textfield', layout: {type: 'hbox', align: 'stretch'},
                        items: [
                            {fieldLabel: 'Name', name: 'name', flex: 1},
                            {fieldLabel: 'Alias', name: 'alias', width: 100, margin : '0 10', maxLength: 10},
                            {
                                xtype: 'button', text: 'COLOR', id:'field-button-color', margin : '21 0 0 0',
                                menu: {
                                    items: [
                                        {xtype: 'hidden', name: 'color', id: 'field-color'},
                                        {
                                            xtype: 'colorpicker',
                                            handler: function(obj, val){
                                                Ext.getCmp('field-button-color').getEl().dom.style.background = '#'+val;
                                                Ext.getCmp('field-color').setValue(val);
                                            }
                                        }
                                    ]
                                }
                            }
                        ]
                    },
                    {xtype: 'textarea', name: 'description', fieldLabel: 'Description', allowBlank: true}
                ],
                buttons: [
                    { text: 'Save', iconCls: 'icon-save-bright', handler: me.save },
                    { text: 'Cancel', iconCls:'icon-close', handler: me.close }
                ]
            });

            me.createWindowForm('{{ $title }}', me.form, {width: 500});
        };

        me.create = function(){
            me.show();
            me.reset();
            me.form.url = '{{ route('service.push') }}';
            Ext.getCmp('field-button-color').getEl().dom.style.background = '#DDDDDD';
        }

        me.edit = function(){
            var rec = grids.getRec(true)
            if(rec){
                me.show();
                me.reset();
                me.form.url = '{{ route('service.push') }}/' + rec.id;
                me.setField('name', rec.name);
                me.setField('alias', rec.alias);
                me.setField('description', rec.description);
                Ext.getCmp('field-button-color').getEl().dom.style.background = '#'+rec.color
            }
            else Ext.example.msg('Warning!', 'Please select data!');
        }

        me.save = function(){
            me.submit(me.form.url, {
                autoclose: true,
                success: grids.storeLoad
            });
        }
    }

</script>
