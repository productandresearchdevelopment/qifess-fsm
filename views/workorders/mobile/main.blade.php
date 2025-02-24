@extends('headers.head-onsjs')

@section('content')
  @require('../details/detail')
  @require('tpl.style')
  @require('form')
  @require('form_part')

  <template modifier="material" id="page-detail.html">
    <ons-page modifier="material" id="page-detail">
      <ons-toolbar modifier="material">
        <div class="left">
          <ons-back-button modifier="material"></ons-back-button>
        </div>
        <div class="center">Detail</div>
      </ons-toolbar>
      <div id="detail-content" style="background: #FAFAFA; position: absolute; width: 100%"></div>
    </ons-page>
  </template>

  <script>
    var activities = @json($activities);
    var services = @json($services);
    var clients = @json($clients);
    var owners = @json($owners);
    var vendors = @json($vendors);
    var statusAction = @json($status);
    var slots = @json($slots);
    var user = @json($user);


    var detailWo = null;

    var uniqueClients = Array.from(
      new Map(user?.fieldtech?.workorders?.map(wo => [wo.client?.id, wo.client]) || []).values()
    ) || [];

    ai.require = '*';
    ai.ready(function() {
      var store = ai.create('store', {
        url: '{{ route('wo.data') }}',
        limit: 10,
      });
      var toolbar = ai.create('actionbar', {
        id: 'main-actionbar',
        title: 'Work Order',
        store: store,
        search: true,
        // topFilter: {
        //   items: [{
        //       id: "all",
        //       text: "All",
        //       handler: function() {
        //         if (store) {
        //           let param = {};
        //           param["filter-client"] = "";
        //           store.extraParams(param);
        //           store.load();
        //         }
        //       }
        //     },
        //     ...uniqueClients.map(client => ({
        //       id: `${client.id}`,
        //       text: client.name,
        //       handler: function() {
        //         if (store) {
        //           let param = {};
        //           param["filter-client"] = client.id;
        //           store.extraParams(param);
        //           store.load();
        //         }
        //       }
        //     }))
        //   ]
        // },
        menu: {
          id: 'main-actionbar-menu',
          items: [{
              type: 'filter',
              prefixText: '',
              items: [{
                  id: 'filter-activity',
                  name: 'filter-activity',
                  text: 'Activity',
                  items: activities
                },
                {
                  id: 'filter-service',
                  name: 'filter-service',
                  text: 'Service',
                  items: services
                },
                {
                  id: 'filter-client',
                  name: 'filter-client',
                  text: 'Client',
                  items: clients
                },
                {
                  id: 'filter-vendor',
                  name: 'filter-vendor',
                  text: 'Vendor',
                  items: vendors
                },
              ]
            },
            '-',
            {
              type: 'sort',
              text: 'Sort By',
              icon: 'fa-sort-amount-asc',
              items: [{
                  text: 'New Update',
                  sort: 'updated_at',
                  dir: 'DESC'
                },
                {
                  text: 'Oldest Update',
                  sort: 'updated_at'
                },
                {
                  text: 'New Ticket',
                  sort: 'id',
                  dir: 'DESC'
                },
                {
                  text: 'Oldest Ticker',
                  sort: 'id'
                },
              ],
            },
          ],
        }
      });

      var listView = ai.create('listview', {
        store: store,
        renderItem: {
          tpl: `@require('tpl.tpl')`,
          renders: [{
              id: 'site_name',
              render: function(rec) {
                if (rec.site) return rec.site.name;
                else if (rec.remove_site) return rec.remove_site.name;
                return '-';
              }
            },
            {
              id: 'fieldtech_name',
              render: function(rec) {
                return rec.fieldtech ? rec.fieldtech.name : '';
              }
            },
            {
              id: 'client_name',
              render: function(rec) {
                let data = find(clients, rec.client_id);
                if (data) return data.name;
                else {
                  let data = find(clients, rec.remove_site.client_id);
                  return data ? data.name : '-';
                }
              }
            },
            {
              id: 'vendor_name',
              render: function(rec) {
                let data = find(vendors, rec.vendor_id);
                return data ? data.name : '';
              }
            },
            {
              id: 'activity',
              render: function(rec) {
                let data = find(activities, rec.activity_id);
                let tpl = '<div class="small-box bright-text" style="background: #{color}">{alias}</div>';
                return String.format(tpl, data);
              }
            },
            {
              id: 'service',
              render: function(rec) {
                let data = find(services, rec.service_id);
                let tpl = '<div class="small-box bright-text" style="background: #{color}">{alias}</div>';
                return String.format(tpl, data);
              }
            },
            {
              id: 'status',
              render: function(rec) {
                let data = find(statusAction, rec.last_action.status_id);
                let tpl = '<div class="status" style="background: #{color}">{alias}</div>';
                return String.format(tpl, data);
              }
            },
            {
              id: 'open',
              render: function(rec) {
                return dates.format(rec.created_at);
              }
            },
            {
              id: 'expire',
              render: function(rec) {
                let tpl =
                  '<div class="small-text" style="color: #{color};"><i class="fa fa-hourglass-o"></i> {expire}</div>';
                let day = dates.shortDay(rec.expire_date);
                if (day.substr(0, 1) == '-') day = {
                  expire: day.substr(1, day.length),
                  color: 'FF0000'
                }
                else day = {
                  expire: day,
                  color: '0072ff'
                }
                return String.format(tpl, day);
              }
            },
          ],
        },
        loadMore: true,
        pullHook: true,
        handler: showDetail,
        listeners: [{
          target: '.popup-action',
          // action: function(rec){
          //     let actions = [];
          //     statusAction.forEach(function (status) {
          //         if(find(status.activities, rec.activity_id)){
          //             if(status.show_on && find(status.show_on, rec.last_action.status_id)){
          //                 if(find(status.roles, {{ $user->role_id }})) {
          //                     actions.push({
          //                         text: status.name,
          //                         value: status,
          //                         handler: function (val) {
          //                             forms.createAction(val, rec);
          //                         }
          //                     });
          //                 }
          //             }
          //         }
          //     });

          //     actions.push({
          //         text: 'VIEW (WO)',
          //         value: 'OK',
          //         handler: function(){
          //             showDetail(rec);
          //         }
          //     });

          //     // actions.push({
          //     //     text: 'ADD EQUIPMENT',
          //     //     handler: function(){
          //     //         formPart.create('EQUIPMENT', rec);
          //     //     }
          //     // });
          //     //
          //     // actions.push({
          //     //     text: 'ADD MATERIAL',
          //     //     handler: function(){
          //     //         formPart.create('MATERIAL',rec);
          //     //     }
          //     // });
          //     //
          //     // actions.push({
          //     //     text: 'DOWNLOAD BAST (PDF)',
          //     //     value: 'OK',
          //     //     handler: function(){
          //     //         console.error("ERROR PAGES INCREMENT");
          //     //         //showDetail(rec);
          //     //     }
          //     // });


          //     let popup = ai.popupModal({items: actions});
          //     popup.show();
          // }

          action: function(rec) {
            let actions = [];

            if (rec.is_hold == 1) {
              actions.push({
                text: 'HOLD, WAITING FROM PARTNER ACKNOWLEDGEMENT',
              });
            } else {
              statusAction.forEach(function(status) {
                if (find(status.activities, rec.activity_id)) {
                  if (status.show_on && find(status.show_on, rec.last_action.status_id)) {
                    if (find(status.roles, {{ $user->role_id }})) {
                      actions.push({
                        text: status.name,
                        value: status,
                        handler: function(val) {
                          forms.createAction(val, rec);
                        }
                      });
                    }
                  }
                }
              });
            }


            actions.push({
              text: 'VIEW (WO)',
              value: 'OK',
              handler: function() {
                showDetail(rec);
              }
            });

            if (actions.length > 0) {
              let popup = ai.popupModal({
                items: actions
              });
              popup.show();
            }
          }
        }],
      });

      ai.create('page', {
        id: 'main-page',
        mainPage: true,
        actionBar: toolbar,
        listView: listView,
        intro: true,
      });

      store.load();

      var forms = new Forms(store);
      var formPart = new FormPart(store);
      detailWo = new DetailWo('#detail-content', {
        partAdd: function(data) {
          let popup = ai.popupModal({
            items: [{
                text: 'EQUIPMENT',
                handler: function() {
                  formPart.create('EQUIPMENT', data);
                }
              },
              {
                text: 'MATERIAL',
                handler: function() {
                  formPart.create('MATERIAL', data);
                }
              }
            ]
          });
          popup.show();
        },
        partEdit: function(data) {
          formPart.edit(data);
        },
        partDelete: true,
      });

    });

    var showDetail = function(rec) {
      ai.showPage('page-detail');
      detailWo.load(rec);
    }
  </script>
@endsection
