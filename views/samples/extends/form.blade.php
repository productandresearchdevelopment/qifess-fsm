<script>
    var Forms = function(){
        let me = Ext.utils.windowForms(this);

        me.init = function(){
            me.form = Ext.widget('form', {
                bodyPadding: 10,
                flex: 1,
                autoHeight: true,
                maxHeight: 400,
                border: false,
                autoScroll: true,
                defaultType: 'textfield',
                layout: {type: 'vbox', align: 'stretch'},
                fieldDefaults:{labelAlign: 'left', allowBlank: false},
                items : [
                    {xtype: 'hidden', name: '_token', value: '{{ csrf_token() }}'},
                    {name: 'name', fieldLabel: 'Name'},
                    {name: 'phone', fieldLabel: 'Phone', allowBlank: true},
                    {xtype: 'textareafield', name: 'address', fieldLabel: 'Address', allowBlank:true, flex: 1, minHeight: 80,}
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
            me.form.url = '{{ route('sample.push') }}';
        }

        me.edit = function(){
            var rec = grids.getRec(true)
            if(rec){
                me.show();
                me.reset();
                me.form.url = '{{ route('sample.push') }}/' + rec.id;

                me.setField('name', rec.name);
                me.setField('phone', rec.phone);
                me.setField('address', rec.address);
            }
            else Ext.example.msg('Warning!', 'Please select data!');
        }

        me.save = function(){
            me.submit(me.form.url, {success: grids.storeLoad});
        }
    }

</script>
