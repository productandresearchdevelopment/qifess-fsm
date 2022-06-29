<style>.x-border-box .reset-box-sizing * {
    box-sizing: content-box;
}</style>
<script>
    var map;
    var marker={};
    var lat = '-0.789275';
    var lng = '113.921327';
    var Forms = function(){
        let me = Ext.utils.windowForms(this);

        me.init = function(){
            me.form = Ext.widget('form', {
                bodyPadding: 10,
                flex: 1,
                autoHeight: true,
                maxHeight: 600,
                width: 800,
                border: false,
                autoScroll: true,
                defaultType: 'textfield',
                layout: {type: 'hbox', align: 'stretch'},
                fieldDefaults:{labelAlign: 'left', allowBlank: false},
                items : [
                    {xtype: 'hidden', name: '_token', value: '{{ csrf_token() }}'},
                    {xtype: 'hidden', name: 'is_active'},
                    {
                        xtype: 'fieldcontainer', flex: 1,
                        layout: {type: 'vbox', align: 'stretch'},
                        items: [
                            {
                                xtype: 'combo', name: 'client_id', fieldLabel: 'Client',
                                forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all',
                                displayField: 'name', valueField: 'id',
                                {{ $user->client_id ? "value: $user->client_id," : ''}}
                                store: Ext.create('Ext.data.Store', {
                                    data: clients,
                                    fields : [
                                        {name: 'id', type: 'int'},
                                        {name: 'name', type: 'string'}
                                    ]
                                }),
                            },
                            {
                                xtype: 'fieldcontainer', flex: 1,
                                layout: {type: 'hbox', align: 'stretch'},
                                items: [
                                    {xtype: 'textfield',name: 'link_id', fieldLabel: 'Link ID',width: 265, margin:"0 5 5 0", allowBlank:true, flex:1},
                                    {xtype:'checkboxfield',boxLabel  : 'Active',name: 'is_active',id:'active',flex:1, hidden: true},
                                ]
                            },
                           {
                                xtype: 'datefield', name: 'active_date', fieldLabel: 'Active Date',
                                format: 'd/m/Y', submitFormat: 'Y-m-d',allowBlank:true,
                            },
                            {xtype: 'textfield',name: 'name', fieldLabel: 'Name'},
                            {xtype: 'textfield',name: 'pic', fieldLabel: 'PIC', allowBlank: true},
                            {xtype: 'textfield',name: 'pic_phone', fieldLabel: 'PIC Phone', allowBlank: true},
                            {xtype: 'textfield',name: 'pic_email', fieldLabel: 'PIC Email', vtype: 'email', allowBlank: true},
                            {xtype: 'textarea', name: 'address', fieldLabel: 'Address', minHeight: 50, allowBlank:true},
                            {xtype: 'textarea', name: 'description', fieldLabel: 'Description', flex: 1,  minHeight: 50, allowBlank: true},
                        ]
                    },
                    {
                        xtype: 'fieldcontainer', flex: 1,margin: '0 20 0 20',
                        layout: {type: 'vbox', align: 'stretch'},
                        items: [
                            {xtype: 'textfield',name: 'terminal_name', fieldLabel: 'Terminal Name', allowBlank:true, hidden: true},
                            {xtype: 'textfield',name: 'airmac', fieldLabel: 'Airmac',allowBlank:true, hidden: true},
                            {xtype: 'textfield',name: 'beam', fieldLabel: 'Beam',allowBlank:true, hidden: true},
                            {xtype: 'textfield',name: 'serial_number', fieldLabel: 'Serial Number',allowBlank:true, hidden: true},
                            {
                                xtype: 'combo', name: 'service_id', fieldLabel: 'Service',allowBlank:true, hidden: true,
                                forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all',
                                displayField: 'name', valueField: 'id', value: 4,
                                store: Ext.create('Ext.data.Store', {
                                    data: service,
                                    fields : [
                                        {name: 'id', type: 'int'},
                                        {name: 'name', type: 'string'},
                                    ]
                                }),
                            },
                            {
                                xtype: 'container',
                                margin: '0 0 10 0',
                                width: 300,
                                flex: 1,
                                html:'<div style="height:100%" id="mapid"></div>',
                            },
                            {xtype: 'textfield', name: 'lat', fieldLabel: 'Latitude', allowBlank: true},
                            {xtype: 'textfield', name: 'long', fieldLabel: 'Longitude', allowBlank: true},
                        ]
                    },
                ],
                buttons: [
                    { text: 'Save', iconCls: 'icon-save-bright', handler: me.save },
                    { text: 'Cancel', iconCls:'icon-close', handler: me.closeForm }
                ]
            });

            me.createWindowForm('{{ $title }}', me.form, {width: 900});
        };

        me.create = function(){
            me.show();
            me.reset();
            me.form.url = '{{ route('site.push') }}';
            map = L.map('mapid').setView([lat, lng], 3);
            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: 'pk.eyJ1IjoiYWRlbXVnaWFudG8iLCJhIjoiY2ticXBpemJ6MGQzZTJ6cDhzcjFscWJ1YSJ9.jhsRAO8lIJok_pEG0WE4Xg'
            }).addTo(map);
            map.on('click', function(e) {
            lat = e.latlng.lat;
            lng = e.latlng.lng;
            if (marker != undefined) {
                map.removeLayer(marker);
            };

                marker = L.marker([lat,lng]).addTo(map).bindPopup("(lat:"+e.latlng.lat.toFixed(6)+",lng:"+e.latlng.lng.toFixed(6)+")").openPopup();
                me.setField('lat', e.latlng.lat.toFixed(6));
                me.setField('long', e.latlng.lng.toFixed(6));
            });
        }

        me.edit = function(){
            var rec = grids.getRec(true)
            if(rec){
                me.show();
                me.reset();
                me.form.url = '{{ route('site.push') }}/' + rec.id;
                me.setField('name', rec.name);
                me.setField('client_id', rec.client_id);
                me.setField('link_id', rec.link_id);
                Ext.getCmp('active').setValue(rec.is_active);
                me.setField('pic', rec.pic);
                me.setField('address', rec.address);
                me.setField('lat', rec.lat);
                me.setField('long', rec.long);
                me.setField('pic_phone', rec.pic_phone);
                me.setField('pic_email', rec.pic_email);
                me.setField('description', rec.description);
                me.setField('terminal_name', rec.terminal_name);
                me.setField('beam', rec.beam);
                me.setField('airmac', rec.airmac);
                me.setField('serial_number', rec.serial_number);
                me.setField('service_id', rec.service_id);
                me.setField('active_date', rec.active_date);
                if(rec.lat)lat=rec.lat;
                if(rec.long)lng=rec.long;

                map = L.map('mapid').setView([lat, lng], 6);
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: rec.address,
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: 'pk.eyJ1IjoiYWRlbXVnaWFudG8iLCJhIjoiY2ticXBpemJ6MGQzZTJ6cDhzcjFscWJ1YSJ9.jhsRAO8lIJok_pEG0WE4Xg'
                }).addTo(map);
                        if(rec.lat && rec.long)
                        L.marker([lat, lng]).addTo(map)
                            .bindPopup(rec.name)
                            .openPopup();

                map.on('click', function(e) {
                lat = e.latlng.lat;
                lng = e.latlng.lng;
                if (marker != undefined) {
                    map.removeLayer(marker);
                };

                    marker = L.marker([lat,lng]).addTo(map).bindPopup("(lat:"+e.latlng.lat.toFixed(6)+",lng:"+e.latlng.lng.toFixed(6)+")").openPopup();
                    me.setField('lat', e.latlng.lat.toFixed(6));
                    me.setField('long', e.latlng.lng.toFixed(6));
                });
            }
            else Ext.example.msg('Warning!', 'Please select data!');
        }

        me.save = function(){
            let active = 0;
            if(Ext.getCmp('active').getValue()==true)active=1;
            map.off();
            map.remove();
            me.submit(me.form.url, {
                params: {active: active},
                autoclose: true,
                success: grids.storeLoad
            });
        }

        me.closeForm = function(){
                me.win.hide();
                map.off();
                map.remove();
        };


    }

</script>
