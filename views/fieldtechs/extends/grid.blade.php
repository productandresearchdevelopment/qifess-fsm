<script>
    var Grids = function(){
        let me = Ext.utils.grids(this);
        me.init = function(){
            me.store = me.httpStore('{{ route('fieldtech.data') }}', [
                {name: 'id', type: 'int'},
                {name: 'nik', type: 'string'},
                {name: 'fieldtech1', type: 'string'},
                {name: 'fieldtech2', type: 'string'},
                {name: 'name', type: 'string'},
                {name: 'phone', type: 'string'},
                {name: 'address', type: 'string'},
                {name: 'email', type: 'string'},
                {name: 'photo', type: 'string'},
                {name: 'vendor_id', type: 'int'},
                {name: 'files', type: 'auto'},
                {name: 'user', type: 'auto'},
                {name: 'workorders', type: 'auto'},
                {name: 'workorders_count', type: 'int'},
            ]);

            me.menus = Ext.create('Ext.menu.Menu', {
                items:[
                    @if($user->hasRoute('fieldtech.push'))
                    {text: 'Create', iconCls: 'icon-add', handler: forms.create},
                    {text: 'Edit',   iconCls: 'icon-edit', handler: forms.edit},
                    @endif

                    @if($user->hasRoute('fieldtech.delete'))
                    {
                        text: 'Delete', iconCls: 'icon-remove',
                        handler: function(){
                            let data = me.getValues();
                            if(data.length){
                                Ext.ajaxConfirm('Remove Fieldtech', {
                                    mask: me.grid,
                                    url: '{{ route('fieldtech.delete') }}',
                                    params: {
                                        '_method': 'DELETE',
                                        '_token': '{{ csrf_token() }}',
                                        data: Ext.encode(data)
                                    },
                                    success: me.storeLoad
                                });
                            }
                            else Ext.msg.warning('Please select data!');
                        }
                    },
                    @endif
                ]
            });

            me.grid  = Ext.create('Ext.grid.Panel', {
                region: 'center',
                store: me.store,
                selType: 'checkboxmodel',
                border: false,
                tbar: me.tbar(me.menus),
                cls: 'large-grid',
                columns: [
                    {text: "ID", dataIndex: 'id', width: 80, hidden:true},
                    {
                        text: "AREA", dataIndex: 'vendor_id', minWidth: 80,
                        renderer: function(val, meta, rec){
                            let data = find(vendor, val);
                            return data ? me.renderText(data.name, data.name, meta) : '';
                        }
                    },
                    //{text: "NIK", dataIndex: 'nik', width: 100},
                    {text: "TEAM", dataIndex: 'name', minWidth: 200},
                    {text: "USER NAME", dataIndex: 'user', width: 150,hidden:true,
                        renderer: function (val) {
                            return val ? val.username : '';
                        }
                    },
                    {text: "PHONE", dataIndex: 'phone', width: 150},
                    {text: "EMAIL", dataIndex: 'email', minWidth: 200},
                    {text: "ADDRESS", dataIndex: 'address', minWidth: 250, flex: 1},
                ],
                @if(!$user->vendor_id)
                bbar: me.bbar([
                    {id: 'vendor', name: 'Vendor', items: vendor}
                ]),
                @endif

                viewConfig: {
                    stripeRows  : false,
                    listeners: {
                        itemcontextmenu: function(obj, rec, node, index, e) {
                            e.stopEvent();
                            me.menus.showAt(e.getXY());
                        },
                        itemclick: function(obj, rec){
                            data = rec.data;
                            if(data){
                               detailFieldtech(data, '#view-detail');
                                Ext.getCmp('panel-detail').setTitle('View ('+data.name+')');
                                if(data.workorders_count<=0)
                                    setTimeout(function () {
                                    $('#list-wo').hide();
                                    },10);
                                else
                                    setTimeout(function () {
                                    $('#list-wo').show();
                                    },10);
                            }else {

                                Ext.getCmp('panel-detail').update(`<div id="view-detail" style="position: absolute; top: 0; left:0; right:0; background: #FAFAFA">
                                        <div style="font-size: 11px; padding: 30px 0px; color: #ccc; text-align: center">
                                            NO DISPLAY DATA
                                        </div>
                                   </div>`);
                                Ext.getCmp('panel-detail').setTitle('View (No Data)');
                            }

                        },
                        itemdblclick : function(obj, rec){
                            Ext.getCmp('panel-detail').expand();

                        }
                    }
                }
            });
        }
    }
</script>
