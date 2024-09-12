<style>
  #user-photo {
    max-height: 240px;
    max-width: 360px;
    display: block
  }
</style>

<script>
  Uwa.ready();
  var Forms = function() {
    let me = Ext.utils.windowForms(this);

    me.init = function() {
      me.form = Ext.widget('form', {
        bodyPadding: 10,
        flex: 1,
        autoHeight: true,
        maxHeight: 500,
        width: 500,
        border: false,
        autoScroll: true,
        defaultType: 'textfield',
        layout: {
          type: 'vbox',
          align: 'stretch'
        },
        fieldDefaults: {
          labelAlign: 'left',
          allowBlank: false
        },
        items: [{
            xtype: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
          },
          {
            xtype: 'combo',
            name: 'vendor_id',
            id: 'vendor',
            {{ $user->vendor_id ? 'readOnly: true,' : '' }} fieldLabel: 'Area',
            forceSelection: true,
            editable: false,
            queryMode: 'local',
            triggerAction: 'all',
            displayField: 'name',
            valueField: 'id',
            {{ $user->vendor_id ? "value: $user->vendor_id," : '' }}
            store: Ext.create('Ext.data.Store', {
              data: vendor,
              fields: [{
                  name: 'id',
                  type: 'int'
                },
                {
                  name: 'name',
                  type: 'string'
                }
              ]
            }),
          },
          {
            name: 'name',
            fieldLabel: 'Team Name'
          },
          {
            name: 'vendor_name',
            fieldLabel: 'Vendor Name'
          },
        ],
        buttons: [{
            text: 'Save',
            iconCls: 'icon-save-bright',
            handler: me.save
          },
          {
            text: 'Cancel',
            iconCls: 'icon-close',
            handler: me.close
          }
        ]
      });

      me.createWindowForm('{{ $title }}', me.form);
    };

    me.create = function() {
      me.show();
      me.reset();
      me.form.url = '{{ route('fieldtech.push') }}';
    }

    me.edit = function() {
      var rec = grids.getRec(true)
      if (rec) {
        me.show();
        me.reset();
        me.form.url = '{{ route('fieldtech.push') }}/' + rec.id;
        me.setField('name', rec.name);
        me.setField('vendor_id', rec.vendor_id);
        me.setField('vendor_name', rec.vendor_name);
      } else Ext.example.msg('Warning!', 'Please select data!');
    }

    me.save = function() {
      me.submit(me.form.url, {
        autoclose: true,
        success: grids.storeLoad
      });
    }
  }
</script>
