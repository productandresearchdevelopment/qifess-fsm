<script>
    var GridModules = function(){
        var me = Ext.utils.grids(this);

        me.selected = null;
        me.group = null;

        me.init = function(){
            me.menus = Ext.create('Ext.menu.Menu',{
                items:[
                    {text: 'Set Home', iconCls: 'icon-yes', handler: me.setHome},
                ]
            });

            me.store =  Ext.create('Ext.data.TreeStore', {
                id : 'store-modules',
                fields: [
                    {name: 'id', type: 'int'},
                    {name: 'type_id', type: 'int'},
                    {name: 'parent', type: 'int'},
                    {name: 'path', type: 'string'},
                    {name: 'text', type: 'string'},
                    {name: 'icon' , type: 'string'},
                    {name: 'route', type: 'string' },
                    {name: 'url' , type: 'string'},
                    {name: 'sort', type: 'int'},
                    {name: 'is_active' , type: 'int'},
                    {name: 'is_locked' , type: 'int'},
                    {name: 'auth'  , type: 'string'},
                    {name: 'device'  , type: 'int'},
                    {name: 'home'  , type: 'bool'},
                    {name: 'description', type: 'string'},
                    {name: 'created_at' , type: 'date'},
                    {name: 'updated_at'  , type: 'date'},
                    {name: 'children', type: 'auto'},
                ],
                root: {id: '0', text: 'Root', icon: '{{ asset('images/icons/home.png') }}'},
                proxy: { type: 'ajax', url: '{{ route('auth.role.data.module', '') }}/' },
                listeners:{
                    load: function(){
                        me.grid.getEl().unmask();
                        if(me.selected){
                            var node = me.grid.store.tree.getNodeById(me.selected);
                            if(node){
                                me.grid.expandPath(node.getPath());
                                me.grid.selectPath(node.getPath());
                            }
                        }
                    }
                }
            });

            me.grid = Ext.create('Ext.tree.Panel', {
                id               : 'grid-modules',
                region           : 'center',
                title            : 'Modules',
                rootVisible      : false,
                singleExpand     : true,
                border           : true,
                sortableColumns  : false,
                enableColumnHide : false,
                enableColumnMove : false,
                enableLocking    : false,
                store            : me.store,
                columns : [
                    {
                        text: '<img src="{{ asset('images/icons/home.png') }}">', dataIndex: 'home', width: 35, align:'center',
                        renderer: function(val, obj, rec){
                            if(val) return '<img src="{{ asset('images/icons/yes.png') }}">';
                        }
                    },
                    {text: 'Title', dataIndex: 'text', xtype: 'treecolumn', flex: 1},
                ],

                viewConfig: {
                    listeners: {
                        itemclick: me.setSelected,
                        checkchange: me.setAuth,
                        itemcontextmenu: function(obj, rec, node, index, e) {
                            me.setSelected(obj, rec);
                            e.stopEvent();
                            me.menus.showAt(e.getXY());
                        }
                    }
                }
            })
        }

        me.setSelected = function(obj, rec){ me.selected = rec.get('id'); }

        me.setAuth = function(){
            var role = gridRoles.getRec(true);
            var rec = me.getRec(true);
            if(rec){
                http.request({
                    method  : 'post',
                    url     : '{{ route('auth.role.set.auth', '')}}/' + role.id,
                    params  : {
                        module    : rec.id,
                        auth      : (rec.checked ? 1 : 0),
                        '_method' : 'PUT',
                        '_token'  : '{{ csrf_token() }}',
                    },
                    failure: function(){
                        Ext.example.msg('Failed!', 'Set Module Group!');
                        me.storeLoad();
                    }
                });
            }
            else {Ext.example.msg('Warning!', 'Please Select Data!');}
        }

        me.setHome = function(){
            var role = gridRoles.getRec(true);
            var rec = me.getRec(true);
            if(rec && role){
                http.request({
                    method    : 'post',
                    url       : '{{ route('auth.role.set.home', '') }}/' + role.id,
                    params    : {'_method': 'PUT',  '_token': '{{ csrf_token() }}', module: rec.id},
                    success: function(){
                        Ext.example.msg('Succsess!', 'Set Home!');
                        var model  = gridRoles.grid.getSelectionModel().getSelection();
                        if(model.length) model = model[0];
                        model.set('home', rec.id);
                        me.storeLoad();
                    },
                    failure: function(){
                        Ext.example.msg('Failed!', 'Set Home!');
                        me.storeLoad();
                    }
                })
            }
            else {Ext.example.msg('Warning!', 'Please Select Data!');}
        }

        me.storeLoad = function(){
            setTimeout(function(){
                var rec = gridRoles.getRec(true);
                me.group = rec;
                if(rec){
                    me.grid.getEl().mask('Proses');
                    me.store.proxy.url = '{{ route('auth.role.data.module', '') }}/' +rec.id
                    me.store.load();
                }
            }, 500);
        }
    };
</script>
