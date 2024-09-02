<script>
    var Grids = function(){
        let me = Ext.utils.grids(this);
        me.init = function(){
            me.store = me.httpStore('{{ route('sample.data') }}', [
                {name: 'id', type: 'int'},
                {name: 'name', type: 'string'},
                {name: 'phone', type: 'string'},
                {name: 'address', type: 'string'}
            ]);

            me.menus = Ext.create('Ext.menu.Menu', {
                items:[
                    @if($user->hasRoute('sample.push'))
                    {text: 'Create', iconCls: 'icon-add', handler: forms.create},
                    {text: 'Edit',   iconCls: 'icon-edit', handler: forms.edit},
                    @endif

                    @if($user->hasRoute('sample.delete'))
                    {
                        text: 'Delete', iconCls: 'icon-remove',
                        handler: function(){
                            let data = me.getValues();
                            if(data.length){
                                Ext.ajaxConfirm('Remove User', {
                                    mask: me.grid,
                                    url: '{{ route('sample.delete') }}',
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
                columns: [
                    {text: "ID", dataIndex: 'id', width: 120},
                    {text: "NAME", dataIndex: 'name', minWidth: 200},
                    {text: "PHONE", dataIndex: 'phone', width: 150},
                    {text: "ADDRESS", dataIndex: 'address', flex: 1}
                ],
                bbar: me.bbar([
                    {
                        id: 'filter-sample', name: 'Sample Filter',
                        items: [
                            {id: 1, name: 'Filter 1'},
                            {id: 2, name: 'Filter 2'},
                            {id: 3, name: 'Filter 3'},
                        ]
                    }
                ]),

                viewConfig: {
                    stripeRows  : false,
                    listeners: {
                        itemcontextmenu: function(obj, rec, node, index, e) {
                            e.stopEvent();
                            me.menus.showAt(e.getXY());
                        }
                    }
                }
            });
        }
    }
</script>
