<script>
    var Forms = function(){
        let me = Ext.utils.windowForms(this);

        me.init = function(){
            me.form = Ext.widget('form', {
                bodyPadding: 10,
                flex: 1,
                autoHeight: true,
                maxHeight: 500,
                border: false,
                autoScroll: true,
                defaultType: 'textfield',
                layout: {type: 'vbox', align: 'stretch'},
                fieldDefaults:{labelAlign: 'left', allowBlank: false},
                items : [
                    {xtype: 'hidden', name: '_token', value: '{{ csrf_token() }}'},
                    {name: 'name', fieldLabel: 'Name'},
                    {name: 'alias', fieldLabel: 'Alias'},
                    {name: 'phone', fieldLabel: 'Phone', allowBlank: true},
                    {name: 'email', fieldLabel: 'Email', allowBlank: true, hidden: true},
                    {xtype: 'textarea', name: 'address', fieldLabel: 'Address', allowBlank:true, height: 80},
                    {xtype: 'textarea', name: 'description', fieldLabel: 'Description', allowBlank:true, flex: 1, minHeight: 80,},
                    {
                        xtype: 'panel',name: 'file_id',title: 'Attachment File', hidden: true,
                        html: '<div id="attachment"></div>',
                        height: 120,border:true
                    },
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
            me.form.url = '{{ route('vendor.push') }}';
            me.fileUpload = new Uwa.FileEditor({
            prefixFile: '{{ route('upload.file') }}',
            maxFile: 4,
            renderTo: '#attachment',
            fileType: '*',
            });
        }

        me.edit = function(){
            var rec = grids.getRec(true)
            if(rec){
                me.show();
                me.reset();
                me.form.url = '{{ route('vendor.push') }}/' + rec.id;
                me.setField('name', rec.name);
                me.setField('alias', rec.alias);
                me.setField('phone', rec.phone);
                me.setField('address', rec.address);
                me.setField('email', rec.email);
                me.setField('alias', rec.alias);
                if(rec.files.length){
                    me.fileUpload = new Uwa.FileEditor({
                        prefixFile: '{{ route('upload.file') }}',
                        maxFile: 4,
                        renderTo: '#attachment',
                        files: rec.files
                    });
                }
                else
                me.fileUpload = new Uwa.FileEditor({
                    prefixFile: '{{ route('upload.file') }}',
                    maxFile: 4,
                    renderTo: '#attachment',
                    fileType: '*',
                });
            }
            else Ext.example.msg('Warning!', 'Please select data!');
        }

        me.save = function(){
            me.submit(me.form.url, {
                params: {attachment: Ext.encode(me.fileUpload.resultFiles)},
                autoclose: true,
                success: grids.storeLoad
            });
        }
    }

</script>
