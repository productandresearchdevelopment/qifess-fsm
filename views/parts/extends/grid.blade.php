<script>
    var Grids = function(){
        let me = Ext.utils.grids(this);
        me.init = function(){
            me.store = me.httpStore('{{ route('part.data',$type) }}', [
                {name: 'id', type: 'string'},
                {name: 'wo_id', type: 'int'},
                {name: 'type', type: 'string'},
                {name: 'code', type: 'string'},
                {name: 'name', type: 'string'},
                {name: 'serial', type: 'string'},
                {name: 'model', type: 'string'},
                {name: 'description', type: 'string'},
                {name: 'wo', type: 'auto'},
                {name: 'files', type: 'auto'},
            ]);
            me.menus = Ext.create('Ext.menu.Menu', {
                items:[
                    @if($user->hasRoute('part.push'))
                   // {text: 'Create', iconCls: 'icon-add', handler: forms.create},
                    {text: 'Edit',   iconCls: 'icon-edit', handler: forms.edit},
                    @endif

                    @if($user->hasRoute('part.delete'))
                    {
                        text: 'Delete', iconCls: 'icon-remove',
                        handler: function(){
                            let data = me.getValues();
                            if(data.length){
                                Ext.ajaxConfirm('Remove Part', {
                                    mask: me.grid,
                                    url: '{{ route('part.delete') }}',
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
            let bbar = [{
                        text: 'Export',   iconCls: 'icon-excel', 
                        handler: function(){ 
                            let type=window.location.toString().split("/");
                            type = type[type.length - 1];
                            let filters = me.store.proxy.extraParams;
                            let query = '';
                            if(me.store.filters.items.length) query = me.store.filters.items[0].value;
                            filters.query = query;
                            filters.type = type;
                            let params = [];
                            for(var key in filters) {
                                var value = filters[key];
                                params.push(key+'='+value)
                            }

                            window.location = '{{ route('part.export.excel') }}?'+params.join('&');

                        } 
                    },'-'];
             bbar = bbar.concat(me.bbar([
                {
                    id: 'client', name: 'Client', items: client,
                        handler: function(val){
                            let fsite = Ext.getCmp('filter-site');
                            let msite = fsite.menu.items.items;
                            fsite.hide();
                            msite.forEach(function (menu, index) {
                                if(index) {
                                    menu.hide();
                                    if(val) {
                                        let st = find(site, menu.value);
                                                if (st.client_id == val) menu.show();
                                                fsite.show();

                                        }
                                }
                                else{
                                    fsite.setText(menu.text)
                                    menu.checked = true;
                                    me.extraParams['filter-site'] = null
                                    me.setExtraParams();
                                }
                            })
                        }
                },
                {id: 'site', name: 'Site', items: site,hidden: true},
            ]));                               

            me.grid  = Ext.create('Ext.grid.Panel', {
                region: 'center',
                store: me.store,
                selType: 'checkboxmodel',
                border: false,
                tbar: me.tbar(me.menus),
                cls: 'large-grid',
                columns: [
                    {text: "#", dataIndex: 'id', width: 80, hidden:true},
                    {text: "PART NO", dataIndex: 'code', minWidth: 80},
                    {text: "NAME", dataIndex: 'name', minWidth: 200},
                    {text: "MODEL", dataIndex: 'model', minWidth: 200},                    
                    {text: "SERIAL NO", dataIndex: 'serial', minWidth: 200},
                    {text: "DESCRIPTION", dataIndex: 'description', minWidth: 150, flex: 1},
                    {text: "INSTALLED AT", dataIndex: 'wo', width: 200,
                        renderer: function (val) {
                            let sites = find(site, val.site_id);
                            let clients = find(client, val.client_id);
                            return val ? '('+clients.alias+') '+sites.name : '';
                        }
                    },                    
                ],
               bbar: bbar,               
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
                               detailPart(data, '#view-detail');
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
