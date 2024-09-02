<script>
    var Grids = function(){
        let me = Ext.utils.grids(this);
        me.init = function(){
            me.store = me.httpStore('{{ route('auth.user.data') }}', [
                {name: 'id', type: 'string'},
                {name: 'role_id', type: 'int'},
                {name: 'username', type: 'string'},
                {name: 'email', type: 'string'},
                {name: 'name', type: 'string'},
                {name: 'phone', type: 'string'},
                {name: 'photo', type: 'string'},
                {name: 'description', type: 'string'},
                {name: 'remember_token', type: 'string'},
                {name: 'last_ip', type: 'string'},
                {name: 'last_module', type: 'int'},
                {name: 'last_url', type: 'string'},
                {name: 'last_active', type: 'date'},
                {name: 'created_by', type: 'string'},
                {name: 'created_at', type: 'date'},
                {name: 'updated_by', type: 'string'},
                {name: 'updated_at', type: 'date'},
                {name: 'deleted_by', type: 'string'},
                {name: 'deleted_at', type: 'date'},
                {name: 'vendor_id', type: 'int'},
                {name: 'client_id', type: 'int'},
                {name: 'fieldtech', type: 'auto'},
                {name: 'owners', type: 'auto'},
                {name: 'activities', type: 'auto'},
                {name: 'vendors', type: 'auto'},
            ]);

            me.menus = Ext.create('Ext.menu.Menu', {
                items:[
                    @if($user->hasRoute('auth.user.push'))
                    {text: 'Create', iconCls: 'icon-add', handler: forms.create},
                    {text: 'Edit',   iconCls: 'icon-edit', handler: forms.edit},
                    @endif

                    @if($user->hasRoute('auth.user.delete'))
                    {
                        text: 'Delete', iconCls: 'icon-remove',
                        handler: function(){
                            let recs = me.getValues();
                            if(recs.length){
                                Ext.ajaxConfirm('Remove User', {
                                    mask: me.grid,
                                    url: '{{ route('auth.user.delete') }}',
                                    params: {
                                        '_method': 'DELETE',
                                        '_token': '{{ csrf_token() }}',
                                        data: Ext.encode(recs)
                                    },
                                    success: me.storeLoad
                                });
                            }
                            else Ext.msg.warning('Please select data!');
                        }
                    },
                    @endif

                    '-',

                    @if($user->hasRoute('auth.user.set.role')) {text: 'Set Role', iconCls: 'icon-user', handler: formRoles.show }, @endif

                    @if($user->hasRoute('auth.user.set.password'))
                    {
                        text: 'Reset Password', iconCls: 'icon-key',
                        handler: function(){
                            let recs = me.getValues();
                            if(recs.length){
                                Ext.MessageBox.prompt('Update Password', 'Enter Password', function(res, text){
                                    if(res == 'ok'){
                                        me.grid.getEl().mask('Sending');
                                        http.request({
                                            method  : 'post',
                                            url     : '{{ route('auth.user.set.password') }}',
                                            params  : {
                                                '_method'   : 'PUT',
                                                '_token'    : '{{ csrf_token() }}',
                                                'data'      : Ext.encode(recs),
                                                'password'  : text
                                            },
                                            success : function(r){
                                                Ext.msg.success('Update password!');
                                                me.storeLoad();
                                                me.grid.getEl().unmask();
                                            },
                                            failure : function(obj, res){
                                                Ext.msg.failed('Internal Server Error!');
                                                me.grid.getEl().unmask();
                                            }
                                        });
                                    }
                                });
                            }
                            else Ext.msg.warning('Please select data!');
                        }
                    },
                    @endif

                    @if($user->hasRoute('auth.user.restore') || $user->hasRoute('auth.users.forcedelete'))
                    {
                        text: 'Trashed', iconCls: 'icon-trash',
                        menu:{
                            items:[
                                @if($user->hasRoute('auth.user.restore'))
                                {
                                    text: 'Restore', iconCls: 'icon-refresh',
                                    handler: function(){
                                        let recs = me.getValues();
                                        if(recs.length){
                                            Ext.ajaxConfirm('Restore User', {
                                                mask: me.grid,
                                                url: '{{ route('auth.user.restore') }}',
                                                params: {
                                                    '_method': 'PUT',
                                                    '_token': '{{ csrf_token() }}',
                                                    data: Ext.encode(recs)
                                                },
                                                success: me.storeLoad
                                            });
                                        }
                                        else Ext.msg.warning('Please select data!');
                                    }
                                },
                                @endif

                                @if($user->hasRoute('auth.user.forcedelete'))
                                {
                                    text: 'Forever Remove', iconCls: 'icon-remove',
                                    handler: function(){
                                        let recs = me.getValues();
                                        if(recs.length){
                                            Ext.ajaxConfirm('Forever Remove User', {
                                                mask: me.grid,
                                                url: '{{ route('auth.user.forcedelete') }}',
                                                params: {
                                                    '_method': 'DELETE',
                                                    '_token': '{{ csrf_token() }}',
                                                    data: Ext.encode(recs)
                                                },
                                                success: me.storeLoad
                                            });
                                        }
                                        else Ext.msg.warning('Please select data!');
                                    }
                                }
                                @endif
                            ]
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
                    {
                        text: "<img src='{{ asset('images/icons/wifi.png') }}'>", dataIndex: 'last_active', width: 35,
                        renderer: function(val){
                            if(val){
                                let diff = dates.diffServer(val);
                                if((diff.distance*1) < (1000 * 60 * 5))
                                    return "<img src='{{ asset('images/icons/online.png') }}'>";
                                else return "<img src='{{ asset ('images/icons/offline.png') }}'>";
                            }
                            return "<img src='{{ asset ('images/icons/offline.png') }}'>";
                        }
                    },
                    {
                        text: "ROLE", dataIndex: 'role_id', width: 80, align: 'center',
                        renderer: function(val, meta, rec){
                            let role = find(roles, val);
                            return role ? me.renderBox(role.alias, role.color, role.name, meta) : '-';
                        }
                    },
                    {text: "UID", dataIndex: 'id', width: 80, hidden: true},
                    {text: "USER NAME", dataIndex: 'username', width: 150},
                    {text: "NAME", dataIndex: 'name', minWidth: 200, flex: 1},
                    {
                        text: "AREA", dataIndex: 'vendor_id', width: 200,
                        renderer: function(val, meta){
                            if(val) {
                                let data = find(vendors, val);
                                return data ? data.name : '';
                            }
                        }
                    },
                    {
                        text: "FIELDTECH", dataIndex: 'fieldtech', width: 200,
                        renderer: function(val, meta){
                            return val ? val.name : '';
                        }
                    },
                    {
                        text: "ACTIVITIES", dataIndex: 'activities', align: 'center', width: 120,
                        renderer: function(data, meta){
                            if(data) {
                                let result = [];
                                data.forEach(function (val) {
                                    val = find(activities, val);
                                    if(val) result.push(val.alias);
                                });
                                return result.join(', ');
                            }
                            else return '<span style="color: #ccc">ALL</span>';
                        }
                    },
                    {
                        text: "OWNERS", dataIndex: 'owners', align: 'center', width: 120,
                        renderer: function(data, meta){
                            if(data) {
                                let result = [];
                                data.forEach(function (val) {
                                    val = find(owners, val);
                                    if(val) result.push(val.alias);
                                });
                                return result.join(' , ');
                            }
                            else return '<span style="color: #ccc">ALL</span>';
                        }
                    },
                    {
                        text: "CLIENT", dataIndex: 'client_id', width: 150,
                        renderer: function(val, meta){
                            if(val) {
                                let data = find(clients, val);
                                return data ? data.name : '';
                            }
                        }
                    },
                    {text: "EMAIL", dataIndex: 'email', width: 250},
                    {text: "PHONE", dataIndex: 'phone', width: 150},
                    {
                        text: "<img src='{{ asset('images/icons/yes.png') }}'>", dataIndex: 'is_active', width: 35,
                        renderer: function(val, obj, rec){
                            if(val) return "<img src='{{ asset('images/icons/yes.png') }}'>";
                            else return "<img src='{{ asset('images/icons/no.png') }}'>";
                        }
                    },
                    {
                        text: "<img src='{{ asset('images/icons/key.png') }}'>", dataIndex: 'is_reset', width: 35,
                        renderer: function(val, obj, rec){
                            if(val) return "<img src='{{ asset('images/icons/key.png') }}'>";
                            else return '';
                        }
                    },
                ],
                bbar: me.bbar([
                    {
                        id: 'trash', name: 'Trash', param: 'trash', iconCls: 'icon-trash',
                        items: [
                            {id: 1, name: 'ACTIVE', checked: true},
                            {id: 2, name: 'TRASH'}
                        ]
                    },
                    {id: 'role', name: 'Role', param: 'role', items: roles},
                    {id: 'client', name: 'Client', param: 'client', items: clients},
                    {id: 'vendor', name: 'Vendor', param: 'vendor', items: vendors},
                    {id: 'activities', name: 'Activities', param: 'activities', items: activities},
                    {id: 'owners', name: 'Owners', param: 'owners', items: owners},
                ]),

                viewConfig: {
                    stripeRows  : false,
                    getRowClass : function(rec){ if(rec.get('deleted_at')) return 'disabled'; },
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
