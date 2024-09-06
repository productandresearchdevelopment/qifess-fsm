@require('form_import')

<script>
  var Grids = function() {
    let me = Ext.utils.grids(this);
    me.init = function() {
      me.formImport = new FormImport();

      me.store = me.httpStore('{{ route('vendor.data') }}', [{
          name: 'id',
          type: 'int'
        },
        {
          name: 'name',
          type: 'string'
        },
        {
          name: 'alias',
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
          name: 'description',
          type: 'string'
        },
        {
          name: 'workorders',
          type: 'auto'
        },
        {
          name: 'files',
          type: 'auto'
        },
        {
          name: 'workorders_count',
          type: 'int'
        },
        {
          name: 'fieldteches_count',
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
          @if ($user->hasRoute('vendor.push'))
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
          @if ($user->hasRoute('vendor.delete'))
            {
              text: 'Delete',
              iconCls: 'icon-remove',
              handler: function() {
                let data = me.getValues();
                if (data.length) {
                  Ext.ajaxConfirm('Remove Vendor', {
                    mask: me.grid,
                    url: '{{ route('vendor.delete') }}',
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

          @if ($user->hasRoute('vendor.import'))
            '-',
            {
              text: 'Import Data',
              iconCls: 'icon-excel',
              menu: [{
                  text: 'Download Format',
                  iconCls: 'icon-cloud',
                  handler: function() {
                    window.location = '{{ route('vendor.export.excel.format.import') }}';
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
          @if ($user->hasRoute('vendor.export.excel'))
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

                window.location = '{{ route('vendor.export.excel') }}?' + params.join('&');
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
            text: "#",
            dataIndex: 'id',
            width: 80,
            hidden: true
          },
          {
            text: "NAME",
            dataIndex: 'name',
            minWidth: 200
          },
          {
            text: "ALIAS",
            dataIndex: 'alias',
            minWidth: 80
          },
          {
            text: "PHONE",
            dataIndex: 'phone',
            width: 150
          },
          {
            text: "EMAIL",
            dataIndex: 'email',
            minWidth: 200
          },
          {
            text: "ADDRESS",
            dataIndex: 'address',
            width: 250
          },
          {
            text: "DESCRIPTION",
            dataIndex: 'description',
            minWidth: 150,
            flex: 1
          },
        ],
        viewConfig: {
          stripeRows: false,
          listeners: {
            itemcontextmenu: function(obj, rec, node, index, e) {
              e.stopEvent();
              me.menus.showAt(e.getXY());
            },
            itemclick: function(obj, rec) {
              /*
              data = rec.data;
              if(data){
                 detailVendor(data, '#view-detail');
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
              */
            },
            itemdblclick: function(obj, rec) {
              //Ext.getCmp('panel-detail').expand();

            },
          }
        }
      });
    }
  }
</script>
