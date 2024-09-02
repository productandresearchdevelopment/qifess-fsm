<style>#user-photo{max-height: 240px; max-width: 360px; display: block}</style>

<script>
    Uwa.ready();
    var Forms = function(){
        let me = Ext.utils.windowForms(this);

        me.init = function(){
            me.form = Ext.widget('form', {
                bodyPadding: 10,
                flex: 1,
                autoHeight: true,
                maxHeight: 500,
                width: 700,
                border: false,
                autoScroll: true,
                defaultType: 'textfield',
                layout: {type: 'hbox', align: 'stretch'},
                fieldDefaults:{labelAlign: 'left', allowBlank: false},
                items : [
                    {xtype: 'hidden', name: '_token', value: '{{ csrf_token() }}'},
                    {
                        xtype: 'fieldcontainer',
                        margin: 5,
                        fieldDefaults:{margin: '5 5'},
                        defaultType: 'textfield',
                        layout: {type: 'vbox', align: 'stretch'},
                        items: [
                                {
                                    xtype: 'panel',
                                    height: 240,
                                    width: 240,
                                    border:true,
                                    margin : '0 5 5 0',
                                    layout : 'border',
                                    id : 'layoutimage',
                                    html: '<center><img id="user-photo" src="{{ asset('images/nouser.png') }}"></center>',
                                },
                                {xtype: 'hiddenfield', name:'photo'},
                                {name: 'username', fieldLabel: 'USER NAME',labelAlign: 'top'},
                                {name: 'password', fieldLabel: 'PASSWORD',inputType:'password', allowBlank: true,labelAlign: 'top'},
                        ]
                    },
                    {
                        xtype: 'fieldcontainer',
                        margin: 5,
                        flex: 1,
                        fieldDefaults:{margin: '0 5 5 0'},
                        defaultType: 'textfield',
                        layout: {type: 'vbox', align: 'stretch'},
                        items: [
                            {
                                xtype: 'combo', name: 'vendor_id',id: 'vendor',{{ $user->vendor_id ? "readOnly: true," : ''}} fieldLabel: 'Area',
                                forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all',
                                displayField: 'name', valueField: 'id',
                                {{ $user->vendor_id ? "value: $user->vendor_id," : ''}}
                                store: Ext.create('Ext.data.Store', {
                                    data: vendor,
                                    fields : [
                                        {name: 'id', type: 'int'},
                                        {name: 'name', type: 'string'}
                                    ]
                                }),
                            },
                            {name: 'nik', fieldLabel: 'NIK',},
                            {name: 'name', fieldLabel: 'Name',},
                            {xtype:'hiddenfield',name: 'attach', fieldLabel: 'Name',},

                            {name: 'phone', fieldLabel: 'Phone', allowBlank: true},
                            {name: 'email', fieldLabel: 'Email', allowBlank: true},
                            {xtype: 'textareafield', name: 'address', fieldLabel: 'Address',allowBlank:true, flex: 1, height: 30,},
                            {
                                xtype: 'panel',name: 'file_id',title: 'Attachment File',
                                html: '<div id="attachment"></div>',
                                height: 120,border:true
                            },

                        ]
                    },
                ],
                buttons: [
                    { text: 'Save', iconCls: 'icon-save-bright', handler: me.save },
                    { text: 'Cancel', iconCls:'icon-close', handler: me.close }
                ]
            });

            me.fileUtil = new FileUtil({
                id: 'file-upload',
                fileType: 'image/jpeg',
                multipleFile: false,
            })

            me.createWindowForm('{{ $title }}', me.form);


        };

        me.editPhotoListener = function(){
            $('#user-photo').click(function () {
                me.fileUtil.load(function(files){
                    let file = files[0];
                    me.setField('photo', file)
                    $('#user-photo').attr('src', file);
                });
            });
        }

        me.create = function(){
            me.show();
            me.reset();
            me.form.url = '{{ route('fieldtech.push') }}';
            $('#user-photo').attr('src', '{{ asset('images/nouser.png') }}');

            me.editPhotoListener();
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
                me.form.url = '{{ route('fieldtech.push') }}/' + rec.id;
                me.setField('nik', rec.nik);
                me.setField('name', rec.name);
                me.setField('phone', rec.phone);
                me.setField('address', rec.address);
                me.setField('email', rec.email);
                me.setField('vendor_id', rec.vendor_id);
                if(rec.user)me.setField('username', rec.user.username);
                $image = rec.photo;
                if(rec.photo)Ext.getCmp('layoutimage').update('<center><img id="user-photo" src="{{ route('upload.file') }}/'+rec.photo+'"'+"></center>'");
                else Ext.getCmp('layoutimage').update('<img id="user-photo" src="{{ asset('images/nouser.png') }}">');

                me.editPhotoListener();
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
