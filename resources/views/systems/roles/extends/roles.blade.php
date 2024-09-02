<style type="text/css">

</style>

<script>
    var GridRoles = function(){
        var me = Ext.utils.grids(this);

        me.init = function(){
            me.store = Ext.create('Ext.data.Store', {
                fields : [
                    {name: 'id', type: 'int'},
                    {name: 'name', type: 'string'},
                    {name: 'alias', type: 'string'},
                    {name: 'color', type: 'string'},
                    {name: 'decription', type: 'string'},
                ],
                proxy : {
                    type  : 'ajax',
                    url   : '{{ route('auth.role.data') }}'
                }
            });

            me.menus = Ext.create('Ext.menu.Menu',{
                items:[
                    @if($user->hasRoute('auth.role.create')) {text: 'Create', iconCls: 'icon-add', handler: forms.create}, @endif
                    @if($user->hasRoute('auth.role.update')) {text: 'Edit', iconCls: 'icon-edit', handler: forms.edit}, @endif
                    @if($user->hasRoute('auth.role.delete'))
                    {
                        text: 'Delete', iconCls: 'icon-remove',
                        handler: function(){
                            var rec = me.getRec(true);
                            if(rec){
                                Ext.ajaxConfirm('Remove User', {
                                    mask: me.grid,
                                    url: '{{ route('auth.role.delete','') }}/' + rec.id,
                                    params: {
                                        '_method': 'DELETE',
                                        '_token': '{{ csrf_token() }}'
                                    },
                                    success: me.storeLoad
                                });
                            }
                            else {Ext.msg.warning('Please select data!');}
                        }
                    },
                    @endif
                ]
            });

            me.grid = Ext.create('Ext.grid.Panel', {
                title   : 'List Groups',
                region  : 'west',
                border  : true,
                width   : 300,
                split   : true,
                store   : me.store,

                dockedItems: [{
                    dock: 'top', xtype: 'toolbar',
                    items: [ {text: 'Menu', iconCls: 'icon-menu', menu: me.menus}]
                }],
                columns : [
                    {
                        text: '#', dataIndex: 'color', width: 30,
                        renderer: function(val, rec){
                            return '<div style="width: 12px; height: 6px; background: #'+val+'"></div>';
                        }
                    },
                    {
                        text: 'Group Name', dataIndex: 'name', flex: 1,
                        renderer: function(val, rec){
                            return '<div style="padding: 5px 0px">'+val+'</div>';
                        }
                    },
                ],
                viewConfig : {
                    stripeRows: false,
                    listeners: {
                        itemclick: function(){
                            gridModules.storeLoad();
                            $('#check-project-all').prop('checked', false);
                        },
                        itemcontextmenu: function(obj, rec, node, index, e) {
                            e.stopEvent();
                            me.menus.showAt(e.getXY());
                        },
                        @if($user->hasRoute('auth.role.update')) itemdblclick: forms.edit @endif
                    }
                }
            })
        }

        me.storeLoad = function(){ me.store.load(); }
    }
</script>
