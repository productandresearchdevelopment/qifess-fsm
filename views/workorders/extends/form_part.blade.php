<script>
    var FormPart = function(){
        let me = Ext.utils.windowForms(this);

        me.fileEditor = null;

        me.init = function(){
            me.form = Ext.widget('form', {
                border: false,
                bodyPadding: 10,
                flex: 1,
                autoScroll: true,
                layout: {type: 'vbox', align: 'stretch'},
                defaultType: 'textfield',
                fieldDefaults:{labelAlign: 'left', allowBlank: false},
                items : [
                    {xtype: 'hidden', name: '_token', value: '{{ csrf_token() }}'},
                    {xtype: 'hidden', name: 'wo_id', value: ''},
                    {
                        xtype: 'combo', name: 'type', fieldLabel: 'Type', flex: 1,
                        forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all',
                        store: ['EQUIPMENT', 'MATERIAL'],
                    },
                    {name: 'code', fieldLabel: 'Number'},
                    {name: 'name', fieldLabel: 'Name'},
                    {name: 'model', fieldLabel: 'Model'},
                    {name: 'serial', fieldLabel: 'Serial Number'},
                    {xtype: 'textarea', name: 'description', fieldLabel: 'Description', allowBlank: true},
                    {
                        xtype: 'displayfield', fieldLabel: 'Photo', value: '<div id="form-part-files"></div>',
                        height: 80,
                    },
                ],
                buttons:[
                    {
                        text: 'Save', iconCls: 'icon-save-bright',
                        handler: function () {
                            me.save();
                        }
                    },
                    {text: 'Cancel', iconCls: 'icon-close', cls: 'btn-red', handler: me.close}
                ]
            });

            me.createWindowForm('Add Sparepart', me.form, {width: 500, layout: 'border'});
        };

        me.create = function(rec){
            if(rec){
                me.show();
                me.reset();
                me.form.url = '{{ route('wo.push.part') }}';
                me.setField('wo_id', rec.id);
                me.renderPhotos();
            }
            else Ext.example.msg('Warning!', 'Please select data!');
        }

        me.edit = function(data){
            if(data){
                me.show();
                me.reset();
                me.form.url = '{{ route('wo.push.part') }}/'+data.id;
                me.setField('wo_id', data.wo_id);
                me.setField('code', data.code);
                me.setField('name', data.name);
                me.setField('type', data.type);
                me.setField('model', data.model);
                me.setField('serial', data.serial);
                me.setField('description', data.description);
                me.renderPhotos(data.files)
            }
            else Ext.example.msg('Warning!', 'Please select data!');
        }

        me.renderPhotos = function(files){
            files = files || [];
            me.fileEditor = new Uwa.FileEditor({
                prefixFile: '{{ route('upload.file') }}',
                maxFile: 4,
                renderTo: '#form-part-files',
                fileType: '.jpg,.jpeg',
                files: files,
            });
        }

        me.save = function(){
            me.submit(me.form.url, {
                params: {files: JSON.stringify(me.fileEditor.resultFiles)},
                success: function(){
                    grids.storeLoad();
                    grids.detailWo.load();
                }
            });
        }
    }

</script>
