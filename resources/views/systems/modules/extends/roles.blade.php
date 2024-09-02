<style>
    .cellediting tr.x-grid-row.x-grid-row-over  td{background-color: transparent;}
</style>

<script>
    var Roles = function(){
        var me = this;
        
        Ext.define('modelRoles',{
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id', type: 'int'},
                {name: 'name', type: 'string'},
                {name: 'alias', type: 'string'},
                {name: 'home', type: 'int'},
                {name: 'color', type: 'string'},
                {name: 'description', type: 'string'},
                {name: 'checked', type: 'bool'},
            ]
        });      
        
        me.init = function(){
            me.grids = grids; 

            me.store =  Ext.create('Ext.data.Store', {id:'store-groups', model: 'modelRoles'}); 
                        
            me.grid = Ext.create('Ext.grid.Panel', {
                xtype: 'cell-editing', 
                title: 'Groups Roles',
                id: 'grid-roles',  
                cls: 'cellediting', 
                region: 'east', 
                width: 350, 
                split: true, 
                border: true, 
                collapsible: true, 
                collapsed: true, 
                columnLines: false, 
                sortableColumns: false, 
                enableColumnHide: false, 
                enableColumnMove: false, 
                enableLocking: false,
                store: me.store,
                plugins: [new Ext.grid.plugin.CellEditing({clicksToEdit: 1})],
                dockedItems: [{
                    dock: 'top',
                    xtype: 'toolbar',
                    items: [
                        {xtype:'hiddenfield', id: 'roles-id-module'},
                        {
                            xtype:'checkbox', 
                            id: 'roles-check-all', 
                            boxLabel: '<b style="font-size: 13px"> &nbsp;All Roles</b>',
                            margin: '0 0 0 5',
                            listeners: { change: me.checkAll }
                        },
                        '->', 
                        {iconCls: 'icon-save-dark', handler: me.save}
                    ]
                }],

                columns: [
                    { xtype: 'checkcolumn', headerCheckbox: true, dataIndex: 'checked', sortable: false, hideable: false, menuDisabled: true, width: 40},
                    {
                        text: 'Role', dataIndex: 'name', flex: 1,
                        renderer: function(val, meta, rec){
                            return '<span style="color: #'+rec.get('color')+'; font-size: 12px; font-weight: 600">'+val+'</span>';
                        }
                    },
                ],

                selModel: {selType: 'cellmodel'},
                viewConfig: {stripeRows: false}
            });
        }

        me.checkAll = function(obj, val, bval){
            var data = me.store.data.items;
                data = Ext.pluck(me.store.data.items, 'raw');
            for(var i=0; i<data.length; i++){
                var rec = data[i];
                rec.checked = val;
            }
            me.store.loadData(data);
        }
        
        me.save = function(){
            var id   = Ext.getCmp('roles-id-module').getValue();
            var data = Ext.pluck(me.store.data.items, 'data');
                data = $.grep(data, function(r){ return r.checked; });
                data = Ext.pluck(data, 'id');
            http.request({
                method  : 'post',
                url     : '{{ route('auth.module.update.roles','') }}/'+id,
                params  : {
                    _method : 'PUT',
                    _token  : '{{ csrf_token() }}',
                    roles   : Ext.encode(data)
                },
                success : function(obj,r){
                    me.grids.storeLoad();
                    Ext.example.msg('Update Roles', 'Roles Has Update !');
                }
            });
        }
        
        me.storeLoad = function(){
            setTimeout(function(){
                var rec = me.grids.getRec();
                if(rec){
                    for(var i=0; i < dataRoles.length; i++){
                        var role = dataRoles[i];
                        var module = $.grep(rec.roles, function(e){return e.id == role.id});
                        role.checked = (module.length) ? true : false;  
                    }
                    Ext.getCmp('roles-id-module').setValue(rec.id);
                    Ext.getCmp('roles-check-all').setValue(false);
                    me.grid.setTitle('<span style="text-transform: uppercase;">'+rec.text+'</span>');
                    me.grids.selected = rec.id;
                    me.store.loadData(dataRoles);
                }
            }, 100);
        }
        
    }
</script>