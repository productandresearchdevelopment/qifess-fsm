<script>
    var Grids = function(){
        let me = Ext.utils.grids(this);
        me.init = function(){
            me.store = me.httpStore('{{ route('workschedule.data') }}?date1={{ $date1 }}&date2={{ $date2 }}', [
                {name: 'id', type: 'int'},
                {name: 'name', type: 'string'},
                {name: 'vendor_id', type: 'int'},
                {name: 'vendor_name', type: 'string'},
                {name: 'data', type: 'auto'},
            ]);

            me.menus = Ext.create('Ext.menu.Menu', {
                items:[]
            });

            me.grid  = Ext.create('Ext.grid.Panel', {
                region: 'center',
                store: me.store,
                border: false,
                tbar: [
                    {
                        xtype: 'monthfield',
                        id: 'filter-month',
                        format: 'F Y',
                        submitFormat: 'Y-m-01',
                        value: '{{ $date1 }}',
                        listeners: {
                            select: function (obj, val){
                                let date = Ext.Date.format(val, "Y-m-01");
                                window.location = '{{ route('workschedule') }}?date='+date;
                            }
                        }
                    },
                    '->',
                    {xtype: 'searchfield', flex:1, maxWidth: 300, minWidth: 180, store: me.store}
                ],
                cls: 'large-grid',
                columnLines: true,
                columns: [
                    {text: "NAME", dataIndex: 'name', locked: true, width: 250},
                    {
                        text: "<b>{{ strtoupper(date('Y - F', strtotime($date1))) }}</b>",
                        align: 'center',
                        columns:[
                            @php $date = $date1; @endphp
                            @while($date < $date2)
                            {
                                text: "{{ strtoupper(date('D', strtotime($date))) }} <br> {{ date('d', strtotime($date)) }}",
                                width: 70,
                                align: 'center',
                                style: 'font-size: 12px; color: {{ (date('N', strtotime($date)) == 7) ? 'red' : '#999' }}',
                                dataIndex: 'data',
                                renderer: function (r){
                                    let rec = find(r, {start_date: '{{ $date }}'});
                                    if(rec){
                                        return rec.count;
                                    }
                                    return null;
                                }
                            },
                            @php $date = date('Y-m-d', strtotime($date . ' +1 day')); @endphp
                            @endwhile
                        ]
                    },
                ],
                bbar: me.bbar([]),
                viewConfig: {
                    stripeRows  : false,
                    listeners: {
                        itemcontextmenu: function(obj, rec, node, index, e) {
                            e.stopEvent();
                            me.menus.showAt(e.getXY());
                        },
                    }
                }
            });
        }
    }
</script>
