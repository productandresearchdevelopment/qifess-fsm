<script>
    var Grids = function(){
        let me = Ext.utils.grids(this);

        me.init = function(){
            @if($archive)
                me.extraParams.filterDate = '{{ date('Y-m-01') }}';
            @endif

            me.store = me.httpStore('{{ $archive ? route('wo.data.archive') : route('wo.data') }}', [
                {name: 'id', type: 'int'},
                {name: 'site_id', type: 'int'},
                {name: 'remove_site_id', type: 'int'},
                {name: 'activity_id', type: 'int'},
                {name: 'vendor_id', type: 'int'},
                {name: 'client_id', type: 'int'},
                {name: 'fieldtech_id', type: 'int'},
                {name: 'service_id', type: 'int'},
                {name: 'owner_id', type: 'int'},
                {name: 'no_wo', type: 'string'},
                {name: 'description', type: 'string'},
                {name: 'start_date', type: 'date'},
                {name: 'expire_date', type: 'date'},
                {name: 'slot_id', type: 'int'},
                {name: 'close_date', type: 'date'},
                {name: 'last_action', type: 'int'},
                {name: 'created_at', type: 'date'},
                {name: 'updated_at', type: 'date'},
                {name: 'deleted_at', type: 'date'},
                {name: 'created_by', type: 'auto'},
                {name: 'updated_by', type: 'auto'},
                {name: 'deleted_by', type: 'auto'},
                {name: 'last_action', type: 'auto'},
                {name: 'actions', type: 'auto'},
                {name: 'parts', type: 'auto'},
                {name: 'site', type: 'auto'},
                {name: 'remove_site', type: 'auto'},
                {name: 'fieldtech', type: 'auto'},
            ]);

            me.menus = Ext.create('Ext.menu.Menu', {
                items:[
                    @if($user->hasRoute('wo.create'))
                    {
                        text: 'Create', iconCls: 'icon-add',
                        handler: forms.create
                    },
                    @endif

                    @if($user->hasRoute('wo.edit'))
                    {
                        text: 'Edit', iconCls: 'icon-edit',
                        handler: function(){
                            forms.edit(me.getRec(true));
                        }
                    },
                    @endif

                    @if($user->hasRoute('wo.delete'))
                    {
                        text: 'Delete', iconCls: 'icon-remove',
                        handler: function(){
                            let data = me.getValues();
                            if(data.length){
                                Ext.ajaxConfirm('Remove WO', {
                                    mask: me.grid,
                                    url: '{{ route('wo.delete') }}',
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

                    {
                        id: 'separator-1',
                        xtype: 'menuseparator',
                        hidden: {{ ($user->hasRoute(['wo.create','wo.edit','wo.delete']) && !$archive) ? 'false' : 'true' }}
                    },

                    @foreach($status AS $sts)
                    @if(in_array($user->role_id, $sts->roles) && $sts->type)
                    {
                        id: 'status-menu-{{ $sts->id }}',
                        iconCls: 'icon-gear',
                        text: '{{ $sts->name }} &nbsp;',
                        value: @json($sts),
                        handler: function(obj){
                            forms.createStatus(me.getRec(true), obj.value);
                        }
                    },
                    @endif
                    @endforeach
                ],

                listeners: {
                    beforeshow: me.beforeShowMenu
                }
            });

            me.bbarFilter = [
                {
                    id: 'activity', name: 'Activity', items: activities,
                    property: {display: '<b style="color: #{color}">{alias}</b>'},
                    @if(!$archive)
                    handler: function(val){
                        let fstatus = Ext.getCmp('filter-status');
                        let mstatus = fstatus.menu.items.items;

                        fstatus.hide();
                        mstatus.forEach(function (menu, index) {
                            if(index) {
                                menu.hide();
                                if(val) {
                                    let sts = find(statusAction, menu.value);
                                    if(sts) {
                                        sts.activities.forEach(function (act) {
                                            if (act == val) menu.show();
                                            fstatus.show();
                                        });
                                    }
                                }
                            }
                            else{
                                fstatus.setText(menu.text)
                                menu.checked = true;
                                me.extraParams['filter-status'] = null
                                me.setExtraParams();
                            }
                        })
                    }
                    @endif
                },
                @if(!$archive)
                {
                    id: 'status',
                    name: 'Status',
                    hidden: true,
                    items: statusAction,
                    property: {display: '<b style="color: #{color}">{alias}</b>'},
                },
                @endif

                @if(!$user->vendor_id && !$user->fieldtech_id)
                {id: 'vendor', name: 'Area', items: vendors},
                @endif

                @if(!$user->client_id)
                {id: 'client', name: 'Client', items: clients},
                @endif


            ]

            me.grid  = Ext.create('Ext.grid.Panel', {
                region: 'center',
                store: me.store,
                border: true,
                dockedItems: [{
                    dock: 'top',
                    xtype: 'toolbar',
                    items: [
                        {text: 'Menu', iconCls: 'icon-menu', menu: me.menus},
                        @if($user->hasRoute('wo.export.excel'))
                        {
                            text: 'Export Excel',
                            iconCls: 'icon-excel',
                            handler: function() {
                                let filters = me.store.proxy.extraParams;
                                let query = '';
                                if (me.store.filters.items.length) query = me.store.filters.items[0].value;
                                filters.query = query;
                                filters.archive = '{{ $archive }}';
                                let params = [];
                                for(let key in filters) {
                                    var value = filters[key];
                                    params.push(key + '=' + value)
                                }
                                window.location = '{{ route('wo.export.excel') }}?' + params.join('&');
                            }
                        }
                        @endif
                    ]
                }],
                columns: [
                    {
                        text: "STS", dataIndex: 'last_action', width: 80, align: 'center',
                        renderer: function(data, meta){
                            let status = find(statusAction, data.status_id);
                            if(data) {
                                return me.renderBox(status.alias, status.color, status.name, meta);
                            }
                            return '';
                        }
                    },
                    {
                        text: "ACT", dataIndex: 'activity_id', width: 80, align: 'center',
                        renderer: function(val, meta){
                            let data = find(activities, val);
                            return data ? me.renderBox(data.alias, data.color, data.name, meta) : '';
                        }
                    },

                    {text: "ID", dataIndex: 'id', width: 115},
                    {
                        text: "CLIENT", dataIndex: 'client_id', width: 150,
                        renderer: function(val, meta, rec){
                            let removeSite = rec.get('remove_site');
                            let client = val ? val : (removeSite ? removeSite.client_id : null);
                            if(client) {
                                let data = find(clients, client);
                                return data ? data.name : '';
                            }
                            return '-';
                        }
                    },
                    {
                        text: "SITE", dataIndex: 'site', width: 200,
                        renderer: function(data){
                            return data ? data.name : '';
                        }
                    },
                    // {
                    //     text: "DISMANTLE SITE", dataIndex: 'remove_site', width: 200,
                    //     renderer: function(data){
                    //         return data ? data.name : '';
                    //     }
                    // },
                    {
                        text: "AREA", dataIndex: 'vendor_id', width: 150,
                        renderer: function(val){
                            if(val) {
                                let data = find(vendors, val);
                                return data ? data.name : '';
                            }
                            return '-';
                        }
                    },
                    {
                        text: "FIELDTECH", dataIndex: 'fieldtech', width: 200,
                        renderer: function(data){
                            return data ? data.name : '-';
                        }
                    },
                    {
                        text: "DURATION", dataIndex: 'close_date', align: 'center', width: 80,
                        renderer: function (val, meta, rec) {
                            let sla = dates.diffServer(rec.get('start_date'));
                            if(val) sla = dates.diff(rec.get('start_date'), val);
                            return sla.day
                        }
                    },
                    {
                        text: "BOOKING",
                        columns: [
                            {text: "DATE", dataIndex: 'start_date', align: 'center', width: 100, renderer: Ext.util.Format.dateRenderer('d/m/Y')},
                            {
                                text: "SLOT", dataIndex: 'slot_id', align: 'center', width: 100,
                                renderer: function (val){
                                    let slot = find(slots, val);
                                    if(slot){
                                        return slot.alias;
                                    }
                                    return '-';
                                }
                            },
                        ]
                    },
                    {text: "CLOSED", dataIndex: 'close_date', align: 'center', width: 110, renderer: Ext.util.Format.dateRenderer('d/m/Y')},

                    {text: "DESCRIPTION", dataIndex: 'description', minWidth: 250, flex: 1},
                    {
                        text: "OPEN AT",
                        columns: [
                            {
                                text: "OPEN BY", dataIndex: 'created_by', width: 200,
                                renderer: function(data){
                                    return data ? data.name : '';
                                }
                            },
                            {text: "DATE", dataIndex: 'created_at', align: 'center', width: 100, renderer: Ext.util.Format.dateRenderer('d/m/Y')},
                        ]
                    },

                ],
                bbar: me.bbar(me.bbarFilter,[
                    @if($archive)
                    {
                        xtype: 'monthfield', id: 'filter-date', format: 'F Y', value: '{{ date('Y-m-01') }}',
                        listeners: {
                            change: function(obj, val){
                                let date= Ext.Date.format(val, "Y-m-01");
                                me.extraParams.filterDate = date;
                                me.setExtraParams();
                                me.storeLoad();
                            }
                        }
                    },
                    @endif
                ]),
                viewConfig: {
                    stripeRows: false,
                    listeners: {
                        itemcontextmenu: function(obj, rec, node, index, e) {
                            e.stopEvent();
                            setTimeout(function () {
                                me.menus.showAt(e.getXY());
                            },100);
                        },
                        itemclick: function(obj, rec){
                            me.detailWo.load(rec.data);
                            Ext.getCmp('panel-detail').setTitle('View ('+rec.get('id')+')');
                        },
                        itemdblclick : function(obj, rec){
                            Ext.getCmp('panel-detail').expand();
                        },
                    }
                }
            });

            me.detailWo = new DetailWo('#view-detail', {
                partAdd: function(data){
                    formPart.create(data);
                },
                partEdit: function(data){
                    formPart.edit(data);
                },
                partDelete: true
            });
        }

        me.beforeShowMenu = function(obj) {
            Ext.getCmp('separator-1').hide();

            let resultShow = false;
            let recs = me.getRecs(true);
            let items = obj.items.items;
            items.forEach(function (item) {
                if(item.id.substr(0,11) == 'status-menu'){
                    item.hide();
                    if(recs.length == 1){
                        let rec = recs[0];
                        let activity = rec.activity_id;
                        let status = rec.last_action.status_id;
                        let menu = item.value;
                        if(menu.show_on.indexOf(status) >= 0){
                            if(menu.activities.indexOf(activity) >= 0){
                                resultShow = true;
                                item.show();

                                @if(($user->hasRoute(['wo.create','wo.edit','wo.delete']) && !$archive))
                                Ext.getCmp('separator-1').show();
                                @endif
                            }
                        }
                    }
                }
            })
            @if(!$user->hasRoute(['wo.create','wo.edit','wo.delete']))
                return resultShow;
            @endif
        }
    }
</script>
