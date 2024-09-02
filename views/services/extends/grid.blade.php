<script>
    var Grids = function(){
        let me = Ext.utils.grids(this);
        me.init = function(){
            me.store = me.httpStore('{{ route('service.data') }}', [
                {name: 'id', type: 'int'},
                {name: 'name', type: 'string'},
                {name: 'alias', type: 'string'},
                {name: 'color', type: 'string'},
                {name: 'description', type: 'string'},
            ]);

            me.menus = Ext.create('Ext.menu.Menu', {
                items:[
                    @if($user->hasRoute('service.push'))
                    {text: 'Create', iconCls: 'icon-add', handler: forms.create},
                    {text: 'Edit',   iconCls: 'icon-edit', handler: forms.edit},
                    @endif

                    @if($user->hasRoute('service.delete'))
                    {
                        text: 'Delete', iconCls: 'icon-remove',
                        handler: function(){
                            let data = me.getValues();
                            if(data.length){
                                Ext.ajaxConfirm('Remove Client', {
                                    mask: me.grid,
                                    url: '{{ route('service.delete') }}',
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
                    {text: "#", dataIndex: 'id', width: 80, hidden:true},
                    {text: "ALIAS", dataIndex: 'alias', minWidth: 80,
                          renderer: function(val, meta,rec){
                            return rec.data ? me.renderBox(rec.data.alias,rec.data.color, rec.data.name, meta) : '';
                        }
                    },
                    {text: "NAME", dataIndex: 'name', minWidth: 200},
                    {text: "DESCRIPTION", dataIndex: 'description', minWidth: 150, flex: 1},
                ],
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
