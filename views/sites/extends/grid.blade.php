@require('form_import')
<script>
    var Grids = function(){
        let me = Ext.utils.grids(this);
        var map;
        me.init = function(){
            me.formImport = new FormImport();

            me.store = me.httpStore('{{ route('site.data') }}', [
                {name: 'id', type: 'int'},
                {name: 'link_id', type: 'string'},
                {name: 'client_id', type: 'int'},
                {name: 'vendor_id', type: 'int'},
                {name: 'name', type: 'string'},
                {name: 'terminal_name', type: 'string'},
                {name: 'beam', type: 'string'},
                {name: 'airmac', type: 'string'},
                {name: 'serial_number', type: 'string'},
                {name: 'service_id', type: 'int'},
                {name: 'pic', type: 'string'},
                {name: 'pic_email', type: 'string'},
                {name: 'pic_phone', type: 'string'},
                {name: 'address', type: 'string'},
                {name: 'lat', type: 'string'},
                {name: 'long', type: 'string'},
                {name: 'description', type: 'string'},
                {name: 'is_active', type: 'int'},
                {name: 'active_date', type: 'date'},
                {name: 'inactive_date', type: 'date'},
                {name: 'workorders_count', type: 'int'},
                {name: 'workorders', type: 'auto'},
                {name: 'province', type: 'string'},
                {name: 'city', type: 'string'},
                {name: 'district', type: 'string'},
                {name: 'ward', type: 'string'},
                {name: 'postal_code', type: 'string'},
                {name: 'deleted_at', type: 'date'},
            ]);

            me.menus = Ext.create('Ext.menu.Menu', {
                items:[
                    @if($user->hasRoute('site.push'))
                    {text: 'Create', iconCls: 'icon-add', handler: forms.create},
                    {text: 'Edit',   iconCls: 'icon-edit', handler: forms.edit},
                    @endif

                    @if($user->hasRoute('site.push'))
                    {
                        text: 'Delete', iconCls: 'icon-remove',
                        handler: function(){
                            let data = me.getValues();
                            if(data.length){
                                Ext.ajaxConfirm('Remove Site', {
                                    mask: me.grid,
                                    url: '{{ route('site.delete') }}',
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

                    @if($user->hasRoute('site.import'))
                    '-',
                    {
                        text: 'Import Data', iconCls: 'icon-excel',
                        menu: [
                            {
                                text: 'Download Format', iconCls: 'icon-cloud',
                                handler: function(){
                                    window.location = '{{ route('site.export.excel.format.import') }}';
                                }
                            },
                            {
                                text: 'Upload File', iconCls: 'icon-excel',
                                handler: function(){
                                    me.formImport.open();
                                }
                            },
                        ]
                    },
                    @endif
                ]
            });

            me.grid  = Ext.create('Ext.grid.Panel', {
                region: 'center',
                store: me.store,
                selType: 'checkboxmodel',
                border: false,

                tbar: [
                    {text: 'Menu', iconCls: 'icon-menu', menu: me.menus},
                    @if($user->hasRoute('site.export.excel'))
                    {
                        text: 'Export Data',   iconCls: 'icon-excel',
                        handler: function(){
                            let filters = me.store.proxy.extraParams;
                            let query = '';
                            if(me.store.filters.items.length) query = me.store.filters.items[0].value;
                            filters.query = query;
                            let params = [];
                            for(var key in filters) {
                                var value = filters[key];
                                params.push(key+'='+value)
                            }

                            window.location = '{{ route('site.export.excel') }}?'+params.join('&');
                        }
                    },
                    @endif
                    '->', {xtype: 'searchfield', flex:1, maxWidth: 300, minWidth: 180, store: me.store}
                ],
                cls: 'large-grid',
                columns: [
                    {text: "#", dataIndex: 'id', width: 80, hidden:true},
                    {
                        text: "#", dataIndex: 'is_active', width: 35,
                        renderer: function(val, meta, rec){
                            if(val) {
                                meta.tdAttr = 'data-qtip="ACTIVE ('+Ext.Date.format(rec.get('active_date'), 'd M Y')+')"';
                                return "<img src='{{ asset('images/icons/online.png') }}'>";
                            }
                            else {
                                meta.tdAttr = 'data-qtip="INACTIVE ('+Ext.Date.format(rec.get('inactive_date'), 'd M Y')+')"';
                                return "<img src='{{ asset ('images/icons/offline.png') }}'>";
                            }
                        }
                    },
                    {
                        text: "SERV", dataIndex: 'service_id', width: 80,
                        renderer: function(val, meta){
                            let data = find(service, val);
                            if(data) {
                                return me.renderBox(data.alias, data.color, data.name, meta);
                            }
                            return '';

                        }
                    },
                    {
                        text: "CLIENT", dataIndex: 'client_id', width: 200,
                        renderer: function(val, meta){
                            let data = find(clients, val);
                            return data ? data.name : '';
                        }
                    },
                    {
                        text: "AREA", dataIndex: 'vendor_id', width: 200,
                        renderer: function(val, meta){
                            let data = find(vendors, val);
                            return data ? data.name : '';
                        }
                    },
                    {text: "LINK ID", dataIndex: 'link_id', minWidth: 100},
                    {text: "NAME", dataIndex: 'name', minWidth: 200},
                    {text: "TERMINAL NAME", dataIndex: 'terminal_name', minWidth: 100,hidden: true},
                    {text: "BEAM", dataIndex: 'beam', minWidth: 100,hidden: true},
                    {text: "AIRMAC", dataIndex: 'airmac', minWidth: 100,hidden: true},
                    {text: "SERIAL NUMBER", dataIndex: 'serial_number', minWidth: 100,hidden: true},
                    {text: "ADDRESS", dataIndex: 'address', width: 200,hidden: true},
                    {text: "ACTIVE DATE", dataIndex: 'active_date', width: 100,hidden: true},
                    {text: "PIC", dataIndex: 'pic', width: 250},
                    {text: "DESCRIPTION", dataIndex: 'description', minWidth: 200, flex: 1},
                ],
                bbar: me.bbar([
                    {
                        id: 'trash', name: 'Data', iconCls: 'icon-trash',
                        items: [
                            {id: 1, name: 'Data Active', checked: true},
                            {id: 2, name: 'Data Deleted'}
                        ]
                    },
                    {id: 'status', name: 'Status', items: [{"id":1,"name":"Active"},{"id":2,"name":"Inactive"}]},
                    {id: 'client', name: 'Client', items: @json($clients)},
                    {id: 'service', name: 'Services', items: @json($services)},
                    {
                        id: 'status', name: 'Status',
                        items: [
                            {id: 1, name: 'Active'},
                            {id: 2, name: 'Inactive'}
                        ]
                    },
                ]),
                viewConfig: {
                    stripeRows  : false,
                    getRowClass : function(rec){ if(rec.get('deleted_at')) return 'disabled'; },
                    listeners: {
                        itemcontextmenu: function(obj, rec, node, index, e) {
                            e.stopEvent();
                            me.menus.showAt(e.getXY());
                        },
                        itemclick: function(obj, rec){
                            data = rec.data;
                            if(data){
                                detailSite(data, '#view-detail');
                                Ext.getCmp('panel-detail').setTitle('View ('+data.name+')');
                            }else {

                                Ext.getCmp('panel-detail').update(`<div id="view-detail" style="position: absolute; top: 0; left:0; right:0; background: #FAFAFA">
                                        <div style="font-size: 11px; padding: 30px 0px; color: #ccc; text-align: center">
                                            NO DISPLAY DATA
                                        </div>
                                   </div>`);
                                Ext.getCmp('panel-detail').setTitle('View (No Data)');
                            }
                            if(data.workorders_count<=0)
                                setTimeout(function () {
                                $('#list-wo').hide();
                                },10);
                            else
                                setTimeout(function () {
                                $('#list-wo').show();
                                },10);
                            if ($('#maploc').length > 0) {
                               if(map)map.remove();
                               setTimeout(function () {
                                 map = L.map('maploc').setView([data.lat, data.long], 8);
                                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                                 maxZoom: 18,
                                 id: 'mapbox/streets-v11',
                                 tileSize: 512,
                                zoomOffset: -1,
                                accessToken: 'pk.eyJ1IjoiYWRlbXVnaWFudG8iLCJhIjoiY2ticXBpemJ6MGQzZTJ6cDhzcjFscWJ1YSJ9.jhsRAO8lIJok_pEG0WE4Xg'
                                }).addTo(map);
                                if(data.lat && data.long)
                                    L.marker([data.lat, data.long]).addTo(map)
                                    .bindPopup(data.name)
                                    .openPopup();
                                },100);

                            }

                        },
                        itemdblclick : function(obj, rec){
                            data = rec.data;
                            Ext.getCmp('panel-detail').expand();
                                setTimeout(function () {
                                    map = L.map('maploc').setView([data.lat, data.long], 8);
                                    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                                     maxZoom: 18,
                                     id: 'mapbox/streets-v11',
                                     tileSize: 512,
                                    zoomOffset: -1,
                                    accessToken: 'pk.eyJ1IjoiYWRlbXVnaWFudG8iLCJhIjoiY2ticXBpemJ6MGQzZTJ6cDhzcjFscWJ1YSJ9.jhsRAO8lIJok_pEG0WE4Xg'
                                    }).addTo(map);
                                    if(data.lat && data.long)
                                        L.marker([data.lat, data.long]).addTo(map)
                                        .bindPopup(data.name)
                                        .openPopup();
                                },100);

                    }

                    }
                },
            });
        }
    }
</script>
