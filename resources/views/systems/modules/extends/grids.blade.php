<script>
    var Grids = function(){
        var me = this;
        
        me.selected = null;
        
        me.init = function(){
            me.menus = Ext.create('Ext.menu.Menu',{
                id: 'main-menu',
                items:[
                    @if($user->hasRoute('auth.module.create'))
                    {
                        text: 'Create', iconCls: 'icon-add',
                        menu:{
                            items:[
                                <?php $group = null ?>
                                @foreach ($types as $type)
                                    @if($group && ($group != $type->group))
                                    '-',
                                    @endif

                                    {text: '{{ $type->name }} &nbsp;', value: {{ $type->id }}, iconCls: 'icon-{{ $type->icon }}', handler: forms.create},

                                    <?php $group = $type->group; ?>
                                @endforeach
                            ]
                        }
                    },
                    @endif

                    @if($user->hasRoute('auth.module.update')) {text: 'Edit', iconCls: 'icon-edit', handler: forms.edit}, @endif
                    @if($user->hasRoute('auth.module.delete')) {text: 'Delete', iconCls: 'icon-remove', handler: me.delete}, @endif

                    @if($user->hasRoute('auth.module.update.field'))
                    '-',
                    {
                        text: 'Actived', value: 'active', iconCls: 'icon-yes',
                        menu:{
                            items:[
                                {text: 'Enable', value: '1', iconCls: 'icon-yes', handler: me.updateModule},
                                {text: 'Disable', value: '0', iconCls: 'icon-no', handler: me.updateModule}
                            ]
                        }
                    },
                    {
                        text: 'Lock', value: 'lock', iconCls: 'icon-lock',
                        menu:{
                            items:[
                                {text: 'Locked', value: '1', iconCls: 'icon-lock', handler: me.updateModule},
                                {text: 'Unlocked', value: '0', iconCls: 'icon-unlock', handler: me.updateModule}
                            ]
                        }
                    },
                    {
                        text: 'Device', value: 'device', iconCls: 'icon-devices',
                        menu:{
                            items:[
                                {text: 'All Device', value: '0', iconCls: 'icon-devices', handler: me.updateModule},
                                {text: 'Mobile Only', value: '1', iconCls: 'icon-mobile', handler: me.updateModule},
                                {text: 'Desktop Only', value: '2', iconCls: 'icon-desktop', handler: me.updateModule}
                            ]
                        }
                    }
                    @endif
                ]
            });
 
            me.store =  Ext.create('Ext.data.TreeStore', {
                id : 'store-modules',
                fields: [
                    {name: 'id', type: 'int'},
                    {name: 'type_id', type: 'int'},
                    {name: 'parent', type: 'int'},
                    {name: 'path', type: 'string'},
                    {name: 'param', type: 'string'},
                    {name: 'text', type: 'string'},
                    {name: 'menuIcon', type: 'string'},
                    {name: 'route', type: 'string'},
                    {name: 'url', type: 'string'},
                    {name: 'sort', type: 'int'},
                    {name: 'is_active', type: 'int'},
                    {name: 'is_locked', type: 'int'},
                    {name: 'auth', type: 'string'},
                    {name: 'device', type: 'int'},
                    {name: 'description', type: 'string'},
                    {name: 'roles', type: 'auto'},
                ],
                root: {id: 0, text: 'Root', icon: '{{ asset('images/icons/home.png') }}', expanded: true},
                proxy: { type: 'ajax', url: '{{ route('auth.module.data') }}'},   
                listeners:{
                    load: function(){
                        if(me.selected){
                            var node = me.store.tree.getNodeById(me.selected);
                            if(node){
                                me.grid.expandPath(node.getPath());
                                me.grid.selectPath(node.getPath());
                                roles.storeLoad();
                            }
                        }
                    }    
                }    
            });
        
            me.grid = Ext.create('Ext.tree.Panel', {
                id: 'grid-main',
                title: 'Modules',
                region: 'center',
                rootVisible: false, 
                multiSelect: true,
                singleExpand: true, 
                border: true,
                selType: 'checkboxmodel',
                store: me.store,
                sortableColumns: false, enableColumnHide: false, enableColumnMove : false, enableLocking: false,
                dockedItems : [{
                    dock: 'top', 
                    xtype: 'toolbar', 
                    items: [
                        {text: 'Menu', iconCls: 'icon-menu', menu: me.menus},
                        '->',
                        {text: 'Refresh', iconCls: 'icon-refresh', handler: me.storeLoad}
                    ]
                }],
                columns : [
                    {text: 'Title', dataIndex: 'text', xtype: 'treecolumn', minWidth: 200, width: 300, locked: true},
                    {
                        text: 'Route Auth', dataIndex: 'auth', width: 200, 
                        renderer: function(val, meta, rec){
                            if(val = rec.get('route')) {
                                var routes = $.grep(dataRoutes, function(e){ return e.id == val });
                                if(routes.length){
                                    var tpl = '<b>ROUTE PROPERTIES</b>'+
                                              '<table style=\'font-size: 11px; line-height: 15px\'>'+
                                              '    <tr> <td></td>       <td></td>   <td></td>        </tr>'+
                                              '    <tr> <td>id</td>     <td>:</td>  <td>{id}</td>     </tr>'+
                                              '    <tr> <td>url</td>    <td>:</td>  <td>{{ url('/') }}{url}</td>    </tr>'+
                                              '    <tr> <td>prefix</td> <td>:</td>  <td>{prefix}</td> </tr>'+
                                              '    <tr> <td>method</td> <td>:</td>  <td>{method}</td> </tr>'+
                                              '    <tr> <td>action</td> <td>:</td>  <td>{action}</td> </tr>'+
                                              '<table>';
                                        tpl = String.format(tpl, routes[0]);
                                    meta['tdAttr'] = 'data-qtip="'+tpl+'"';
                                    return '<span style="color: #333333;">'+val+'</span>';
                                }
                                meta['tdAttr'] = 'data-qtip="Route Not Found"';
                                return '<span style="color: #CC0000;">'+val+'</span>';
                            }
                            else if(val = rec.get('auth')) {
                                meta['tdAttr'] = 'data-qtip="Auth"';
                                return '<span style="color: #0072FF;">'+val+'</span>';
                            }
                            return '';
                        }
                    },
                    {
                        text: 'URL', dataIndex: 'url', flex: 1, minWidth: 300,
                        renderer: function(val, obj, rec){
                            if(val = rec.get('url')) return val;
                            return '';
                        }
                    },
                    {
                        text: 'Param', dataIndex: 'param', width: 200,
                    },
                    {
                        text: 'Icon', dataIndex: 'menuIcon', width: 130, 
                        renderer: function(val, obj, rec){
                            if(val) {
                                return '<i class="fa '+val+'"></i> &nbsp; '+val;
                            }
                            else return val;
                        }
                    },
                    {text: 'Description', dataIndex: 'description', minWidth: 150},
                    {
                        text: '<img src="{{ asset('images/icons/devices.png') }}">', dataIndex: 'device', width: 40, align:'center', 
                        renderer: function(val, meta, rec){
                            var result = '';
                            switch(val){
                                case 1  : result = '<img src="{{ asset('images/icons/mobile.png') }}">';  meta['tdAttr'] = 'data-qtip="Mobile Only"'; break;
                                case 2  : result = '<img src="{{ asset('images/icons/desktop.png') }}">'; meta['tdAttr'] = 'data-qtip="Desktop Only"'; break;
                            }
                            return result;
                        }
                    },
                    {
                        text: '<img src="{{ asset('images/icons/lock.png') }}" />', dataIndex: 'is_locked', width: 40, align:'center', 
                        renderer: function(val, meta, rec){
                            if(val == 1) {
                                meta['tdAttr'] = 'data-qtip="Locked"';
                                return '<img src="{{ asset('images/icons/lock.png') }}">';
                            }
                            else{
                                meta['tdAttr'] = 'data-qtip="Unlocked"';
                                return '<img src="{{ asset('images/icons/unlock.png') }}">';
                            }
                        }
                    },    
                    {
                        text: '<img src="{{ asset('images/icons/yes.png') }}">', dataIndex: 'is_active', width: 35, align:'center', 
                        renderer: function(val, meta, rec){
                            if(val == 1) {
                                meta['tdAttr'] = 'data-qtip="Enable"';
                                return '<img src="{{ asset('images/icons/yes.png') }}">';
                            }
                            else{
                                meta['tdAttr'] = 'data-qtip="Disable"';
                                return '<img src="{{ asset('images/icons/no.png') }}">';
                            }
                        }
                    },
                ],
                            
                viewConfig: {
                    enableTextSelection: true,
                    plugins: {ptype: 'treeviewdragdrop', containerScroll: true},
                    listeners: {
                        itemdblclick : roles.storeLoad, 
                        itemclick : roles.storeLoad,
                        drop: me.move,
                        itemcontextmenu: function(obj, rec, node, index, e) {
                            e.stopEvent(); 
                            roles.storeLoad();
                            me.menus.showAt(e.getXY());
                        }
                    }
                }
            })
        }
        
        me.getRec = function(multi){
            var rec  = me.grid.getSelectionModel().getSelection();
            if(rec.length){
                rec = Ext.pluck(rec, 'data');
                if(multi == undefined || !multi) return rec[0];
                else return rec;
            }
            
            return null;
        }
        
        me.delete = function(){
            var rec = me.getRec(true);
            if(rec){
                var data = Ext.pluck(rec, 'id');
                Ext.MessageBox.confirm('Confirm', 'Remove Module?', function(res){
                    if(res=='yes'){
                        Ext.getCmp('main-container').getEl().mask('Proses');
                        http.request({
                            method  : 'POST',
                            url     : '{{ route('auth.module.delete') }}',
                            params  : {
                                _method : 'DELETE',
                                _token  : '{{ csrf_token() }}',
                                data    : Ext.encode(data)
                            },
                            success : function(obj,r){
                                Ext.getCmp('main-container').getEl().unmask();
                                me.storeLoad();
                            },
                            failure: function(obj,r) {Ext.getCmp('main-container').getEl().unmask();}
                        });
                    }
                });
            }
        }

        @if($user->hasRoute('auth.module.update.field'))
        me.updateModule = function(e){
            console.log(e);
            var rec = me.getRec(true);
            if(rec){
                var data = Ext.pluck(rec, 'id');
                Ext.MessageBox.confirm('Confirm', 'Update '+e.parentMenu.parentItem.text+' '+e.text+'?', function(res){
                    if(res=='yes'){
                        Ext.getCmp('main-container').getEl().mask('Proses');
                        http.request({
                            method  : 'POST',
                            url     : '{{ route('auth.module.update.field','') }}/'+e.parentMenu.parentItem.value,
                            params  : {
                                _method : 'PUT',
                                _token  : '{{ csrf_token() }}',
                                data  : Ext.encode(data), 
                                value : e.value,
                            },
                            success : function(obj,r){
                                Ext.getCmp('main-container').getEl().unmask();
                                me.storeLoad();
                            },
                            failure: function(obj,r) {
                                Ext.getCmp('main-container').getEl().unmask();
                            }
                        });
                    }
                });
            }
        }

        @endif
        
        me.move = function(node, data, drop, mode) {
            me.selected = data.records[0].data.id;
            var from    = data.records[0].data;
            var to      = drop.data;            
            http.request({
                method  : 'POST',
                url     : '{{ route('auth.module.move','') }}/' + mode,
                params  : {
                    _method : 'PUT',
                    _token  : '{{ csrf_token() }}',
                    from: Ext.encode(from), 
                    to: Ext.encode(to)
                },
                success : function(){
                    Ext.example.msg('Moved', 'Module Has Moved !');
                    me.storeLoad();
                }
            });
        }
        
        me.storeLoad = function(){
            me.store.load();
        }
           
    }; 
</script>