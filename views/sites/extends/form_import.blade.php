<script>
    var FormImport = function(){
        let me = Ext.utils.windowForms(this);

        me.init = function(){
            me.activities = [{id: '0', name: 'NONE'}, {id: '1', name: 'INSTALLATION'}, {id: '5', name: 'TERMINATE'}];

            me.form = Ext.widget('form', {
                bodyPadding: '10 20',
                border: false,
                autoHeight: true,
                width: 400,
                layout: {type: 'vbox', align: 'stretch'},
                fieldDefaults: {labelAlign: 'top', allowBlank: false},
                items : [
                    {xtype: 'hidden', name: '_token', value: '{{ csrf_token() }}'},
                    {
                        xtype: 'fieldcontainer', id: 'form-import-main',
                        layout: {type: 'vbox', align: 'stretch'},
                        items: [
                            {
                                xtype: 'combo', name: 'activity_id', fieldLabel: 'Create Ticket', flex: 1,
                                forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all',
                                displayField: 'name', valueField: 'id',
                                store: Ext.create('Ext.data.Store', {
                                    data: me.activities,
                                    fields: [
                                        {name: 'id', type: 'int'},
                                        {name: 'name', type: 'string'},
                                    ],
                                })
                            },
                            {
                                xtype: 'filefield', name: 'file', fieldLabel: 'File Excel',
                                emptyText: 'Select an excel format',
                                listeners: {
                                    afterrender: function (cmp) {
                                        setTimeout(function () {
                                            $('#' + cmp.fileInputEl.id).attr("accept", 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel')
                                        }, 200);
                                    }
                                }
                            },
                        ]
                    },
                    {
                        xtype: 'textarea', id: 'form-import-response', height: 200, allowBlank: true,
                    }
                ],
                buttons: [
                    { text: 'Upload', iconCls: 'icon-save-bright', cls: 'btn-green', handler: me.save },
                    { text: 'Cancel', iconCls:'icon-close', cls: 'btn-red', handler: me.close }
                ]
            });

            me.createWindowForm('Import', me.form, {header: true, maximized: false});
        };

        // FORM METHOD -------------------------------------------------------------------------------------------------
        me.open = function(){
            me.show();
            me.reset();
            Ext.getCmp('form-import-response').hide();
            Ext.getCmp('form-import-main').show();
        }

        me.save = function(){
            me.submit('{{ route("site.import") }}', {
                success: function (respon){
                    Ext.getCmp('form-import-response').show();
                    Ext.getCmp('form-import-main').hide();

                    let message = respon.message;
                    let logs = [];
                    logs.push('-------------------------------------------');
                    logs.push('RESULT');
                    logs.push('-------------------------------------------');
                    logs.push('TOTAL ROW: ' + message.totalRow);
                    logs.push('TOTAL SUCCESS: ' + message.totalSuccess);
                    logs.push('TOTAL ERROR: ' + message.totalError);
                    logs.push('-------------------------------------------');
                    logs.push('ERROR LOG :');
                    message.errorLog.forEach(function (e){
                        logs.push('ROW ('+e.row+') : '+ e.message);
                    });

                    Ext.getCmp('form-import-response').setValue(logs.join("\n"));
                }
            }, false);
        }

        me.init();
    }

</script>
