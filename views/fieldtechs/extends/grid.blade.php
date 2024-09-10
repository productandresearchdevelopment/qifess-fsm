@require('form_import')

<script>
  var Grids = function() {
    let me = Ext.utils.grids(this);
    me.init = function() {
      me.formImport = new FormImport();

      me.store = me.httpStore('{{ route('fieldtech.data') }}', [{
          name: 'id',
          type: 'int'
        },
        {
          name: 'nik',
          type: 'string'
        },
        {
          name: 'fieldtech1',
          type: 'string'
        },
        {
          name: 'fieldtech2',
          type: 'string'
        },
        {
          name: 'name',
          type: 'string'
        },
        {
          name: 'phone',
          type: 'string'
        },
        {
          name: 'address',
          type: 'string'
        },
        {
          name: 'email',
          type: 'string'
        },
        {
          name: 'photo',
          type: 'string'
        },
        {
          name: 'vendor_id',
          type: 'int'
        },
        {
          name: 'vendor_name',
          type: 'string'
        },
        {
          name: 'files',
          type: 'auto'
        },
        {
          name: 'users',
          type: 'auto'
        },
        {
          name: 'workorders',
          type: 'auto'
        },
        {
          name: 'workorders_count',
          type: 'int'
        },
      ], {
        beforeload: function(store, operation, opts) {
          let filters = me.store.proxy.extraParams;
          let query = '';
          if (me.store.filters.items.length) query = me.store.filters.items[0].value;
          filters.query = query;
          return true;
        }
      });

      me.menus = Ext.create('Ext.menu.Menu', {
        items: [
          @if ($user->hasRoute('fieldtech.push'))
            {
              text: 'Create',
              iconCls: 'icon-add',
              handler: forms.create
            }, {
              text: 'Edit',
              iconCls: 'icon-edit',
              handler: forms.edit
            },
          @endif

          @if ($user->hasRoute('fieldtech.delete'))
            {
              text: 'Delete',
              iconCls: 'icon-remove',
              handler: function() {
                let data = me.getValues();
                if (data.length) {
                  Ext.ajaxConfirm('Remove Fieldtech', {
                    mask: me.grid,
                    url: '{{ route('fieldtech.delete') }}',
                    params: {
                      '_method': 'DELETE',
                      '_token': '{{ csrf_token() }}',
                      data: Ext.encode(data)
                    },
                    success: me.storeLoad
                  });
                } else Ext.msg.warning('Please select data!');
              }
            },
          @endif

          @if ($user->hasRoute('fieldtech.import'))
            '-',
            {
              text: 'Import Data',
              iconCls: 'icon-excel',
              menu: [{
                  text: 'Download Format',
                  iconCls: 'icon-cloud',
                  handler: function() {
                    window.location = '{{ route('fieldtech.export.excel.format.import') }}';
                  }
                },
                {
                  text: 'Upload File',
                  iconCls: 'icon-excel',
                  handler: function() {
                    me.formImport.open();
                  }
                },
              ]
            },
          @endif
        ]
      });

      me.grid = Ext.create('Ext.grid.Panel', {
        region: 'center',
        store: me.store,
        selType: 'checkboxmodel',
        border: false,
        tbar: [{
            text: 'Menu',
            iconCls: 'icon-menu',
            menu: me.menus
          },
          @if ($user->hasRoute('fieldtech.export.excel'))
            {
              text: 'Export Data',
              iconCls: 'icon-excel',
              handler: function() {
                let filters = me.store.proxy.extraParams;
                let query = '';
                if (me.store.filters.items.length) query = me.store.filters.items[0].value;
                filters.query = query;
                let params = [];
                for (var key in filters) {
                  var value = filters[key];
                  params.push(key + '=' + value)
                }

                window.location = '{{ route('fieldtech.export.excel') }}?' + params.join('&');
              }
            },
          @endif
          '->', {
            xtype: 'searchfield',
            flex: 1,
            maxWidth: 300,
            minWidth: 180,
            store: me.store
          }
        ],
        cls: 'large-grid',
        columns: [{
            text: "ID",
            dataIndex: 'id',
            width: 80,
            hidden: true
          },
          {
            text: "AREA",
            dataIndex: 'vendor_id',
            minWidth: 200,
            renderer: function(val, meta, rec) {
              let data = find(vendor, val);
              return data ? me.renderText(data.name, data.name, meta) : '';
            }
          },
          {
            text: "NIK",
            dataIndex: 'nik',
            width: 200
          },
          {
            text: "NAME",
            dataIndex: 'name',
            width: 300
          },
          {
            text: "USER",
            dataIndex: 'users',
            minWidth: 200,
            flex: 1,
            renderer: function(val) {
              let result = [];
              val.forEach(function(e) {
                result.push(e.name)
              });

              return result.join('<br>')
            }
          },
          {
            text: "ADDRESS",
            dataIndex: 'address',
            minWidth: 250,
            flex: 1
          },
          {
            text: "EMAIL",
            dataIndex: 'email',
            minWidth: 200
          },
          {
            text: "FIELDTECH 1",
            dataIndex: 'fieldtech1',
            minWidth: 200
          },
          {
            text: "FIELDTECH 2",
            dataIndex: 'fieldtech2',
            minWidth: 200
          },
          {
            text: "VENDOR NAME",
            dataIndex: 'vendor_name',
            minWidth: 200
          },
          {
            text: "PHONE",
            dataIndex: 'phone',
            width: 150
          },
        ],
        @if (!$user->vendor_id)
          bbar: me.bbar([{
            id: 'vendor',
            name: 'Area',
            items: vendor
          }]),
        @endif

        viewConfig: {
          stripeRows: false,
          listeners: {
            itemcontextmenu: function(obj, rec, node, index, e) {
              e.stopEvent();
              me.menus.showAt(e.getXY());
            },
            itemclick: function(obj, rec) {
              data = rec.data;
              if (data) {
                detailFieldtech(data, '#view-detail');
                Ext.getCmp('panel-detail').setTitle('View (' + data.name + ')');
                if (data.workorders_count <= 0)
                  setTimeout(function() {
                    $('#list-wo').hide();
                  }, 10);
                else
                  setTimeout(function() {
                    $('#list-wo').show();
                  }, 10);
              } else {

                Ext.getCmp('panel-detail').update(`<div id="view-detail" style="position: absolute; top: 0; left:0; right:0; background: #FAFAFA">
                                        <div style="font-size: 11px; padding: 30px 0px; color: #ccc; text-align: center">
                                            NO DISPLAY DATA
                                        </div>
                                   </div>`);
                Ext.getCmp('panel-detail').setTitle('View (No Data)');
              }

            },
            itemdblclick: function(obj, rec) {
              Ext.getCmp('panel-detail').expand();

            }
          }
        }
      });
    }
  }
</script>
