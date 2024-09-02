<script>
    var Forms = function(){
        let me = Ext.utils.windowForms(this);

        me.init = function(){
            me.form = Ext.widget('form', {
                bodyPadding: 10,
                flex: 1,
                autoHeight: true,
                maxHeight: 450,
                border: false,
                autoScroll: true,
                defaultType: 'textfield',
                layout: {type: 'vbox', align: 'stretch'},
                fieldDefaults:{labelAlign: 'left', allowBlank: false},
                items : [
                    {xtype: 'hidden', name: '_token', value: '{{ csrf_token() }}'},
                    {name: 'customer_id', fieldLabel: 'Customer ID'},
                    {name: 'name', fieldLabel: 'Name'},
                    {name: 'alias', fieldLabel: 'Alias'},
                    {name: 'phone', fieldLabel: 'Phone', allowBlank: true},
                    {name: 'email', fieldLabel: 'Email', allowBlank: true},
                    {xtype: 'textarea', name: 'address', fieldLabel: 'Address', allowBlank:true, height: 80},
                    {xtype: 'textarea', name: 'description', fieldLabel: 'Description', allowBlank:true, flex: 1, minHeight: 80,}
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
            me.form.url = '{{ route('client.push') }}';
        }

        me.edit = function(){
            var rec = grids.getRec(true)
            if(rec){
                me.show();
                me.reset();
                me.form.url = '{{ route('client.push') }}/' + rec.id;
                me.setField('customer_id', rec.customer_id);
                me.setField('name', rec.name);
                me.setField('alias', rec.alias);
                me.setField('phone', rec.phone);
                me.setField('address', rec.address);
                me.setField('email', rec.email);
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
