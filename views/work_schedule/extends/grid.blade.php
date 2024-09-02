<script>
    var Grids = function(){
        let me = Ext.utils.grids(this);
        let activities = @json($activities);
        let slots = @json($slots);

        me.init = function(){
            me.store = Ext.create('Ext.data.Store', {
                fields:  [
                    {name: 'id', type: 'int'},
                    {name: 'name', type: 'string'},
                    {name: 'fieldtech1', type: 'string'},
                    {name: 'fieldtech2', type: 'string'},
                    {name: 'name', type: 'string'},
                    {name: 'vendor_id', type: 'int'},
                    {name: 'vendor_name', type: 'string'},
                    {name: 'workorders', type: 'auto'},
                ],
                remoteSort: true,
                proxy: {
                    type: 'ajax',
                    url: '{{ route('workschedule.data') }}?date1={{ $date1 }}&date2={{ $date2 }}',
                    simpleSortMode: true
                },
            });

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
                columnLines: true,
                columns: [
                    {
                        text: "NAME", dataIndex: 'name', locked: true, width: 250,
                        renderer: function (val, meta, rec){
                            console.log(rec);
                            let tpl = `<div style="font-weight: 600">{0}</div><div style="color: #666; font-size: 11px">{1}, {2}</div>`;
                            return String.format(tpl, val, rec.get('fieldtech1'), rec.get('fieldtech2'));
                        }
                    },
                    {
                        text: "<b>{{ strtoupper(date('Y - F', strtotime($date1))) }}</b>",
                        align: 'center',
                        columns:[
                            @php $date = $date1; @endphp
                            @while($date < $date2)
                            {
                                text: "{{ strtoupper(date('D', strtotime($date))) }} <br> {{ date('d', strtotime($date)) }}",
                                width: 100,
                                align: 'center',
                                style: 'font-size: 12px; color: {{ (date('N', strtotime($date)) == 7) ? 'red' : '#999' }}',
                                dataIndex: 'workorders',
                                renderer: function (r){
                                    let rec = grep(r, {start_date: '{{ $date }}'});
                                    if(rec.length){
                                        let tpl = `<table width="100%" cellpadding="0" cellspacing="0" style="margin: 5px 0px;  color: #fff; font-size: 10px;">
                                                      <tr>
                                                          <td style="padding: 2px;" bgcolor="#{slot_color}" width="20">{slot}</td>
                                                          <td bgcolor="#{actitivy_color}">{activity}</td>
                                                      </tr>
                                                   </table>`;
                                        let result = [];
                                        rec.forEach(function (e){
                                            let slot = find(slots, e.slot_id);
                                            let activity = find(activities, e.activity_id);
                                            let data = {
                                                slot_color: slot ? slot.color : '',
                                                actitivy_color: activity ? activity.color : '',
                                                slot: slot.id,
                                                activity: activity.alias
                                            };
                                            result.push(String.format(tpl, data));
                                        });

                                        return result.join(' ');
                                    }
                                    return '<div style="height: 50px;"></div>';
                                }
                            },
                            @php $date = date('Y-m-d', strtotime($date . ' +1 day')); @endphp
                            @endwhile
                        ]
                    },
                ],
                bbar: me.bbar([
                    @if(!$user->vendor_id) {id: 'vendor',  name: 'Area', items: vendors } @endif
                ]),
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
