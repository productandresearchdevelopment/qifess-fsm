<script src="{{ asset("plugins/signature/js/jquery-ui.min.js") }}"></script>
<script src="{{ asset("plugins/signature/js/jquery.signature.js") }}"></script>

<style>
    .fieldlabel-property .x-form-item-label{font-weight: 700; margin-bottom: 5px; font-size: 13px}
    .x-form-textarea, .x-form-text{background-color: #FFFFFF}
    .readonly .x-form-textarea, .readonly .x-form-text{background-color: #FAFAFA}
</style>

<script>
    var Forms = function(){
        let me = Ext.utils.windowForms(this);

        me.actionStatus = null;

        me.onSave = function(){ grids.storeLoad(); };

        me.onBackButton = function(){ me.close(); };

        me.init = function(opt){
            if(!isNull(opt)) me.onSave = opt.onSave;
            if(!isNull(opt)) me.onBackButton = opt.onBackButton;

            me.form = Ext.widget('form', {
                bodyPadding: '5',
                region: 'center',
                border: false,
                flex: 1,
                autoScroll: true,
                layout: {type: 'hbox', align: 'stretch'},
                defaultType: 'textfield',
                fieldDefaults:{labelAlign: 'top', allowBlank: false},
                dockedItems : [{
                    xtype: 'toolbar',
                    dock: 'top',
                    cls: 'action-bar',
                    style: 'background: #FFFFFF',
                    items: [
                        {cls: 'btn-transparent',  iconCls: 'icon-back', margin: '0 0 0 10',handler: me.onBackButton},
                        {xtype: 'tbtext', id:'form-title', text: 'NEW WO', id: 'form-title'},
                        '->',
                        {text: 'Save Data', cls: 'btn-blue', iconCls: 'icon-save-bright', handler: me.save},
                    ]
                }],
                items : [
                    {xtype: 'hidden', name: '_token', value: '{{ csrf_token() }}'},
                    {xtype: 'hidden', name: 'id'},
                    {xtype: 'hidden', name: 'client_id'},
                    {
                        xtype: 'fieldcontainer',
                        flex: 1,
                        autoScroll: true,
                        layout: {type: 'vbox', align: 'stretch'},
                        items: [
                            // NOMOR WO --------------------------------------------------------------------------------
                            {
                                xtype: 'textfield',
                                name: 'no_wo',
                                fieldLabel: 'WO Number',
                                allowBlank: true,
                                hidden: true,
                                margin: '5 15 5 15'
                            },

                            // SERVICE & ACTIVITY ----------------------------------------------------------------------
                            {
                                xtype: 'fieldcontainer', margin: '0 5',
                                fieldDefaults:{margin: '2 10'},
                                layout: {type: 'hbox', align: 'stretch'},
                                items: [
                                    {
                                        xtype: 'combo', name: 'activity_id', fieldLabel: 'Activity', flex: 1,
                                        forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all',
                                        displayField: 'name', valueField: 'id', value: 30,
                                        store: Ext.create('Ext.data.Store', {
                                            data: activities,
                                            fields : [
                                                {name: 'id', type: 'int'},
                                                {name: 'name', type: 'string'},
                                                {name: 'site_on', type: 'int'},
                                                {name: 'site_off', type: 'int'},
                                            ]
                                        }),
                                        listeners: {
                                            select: function(val, rec){
                                                let data = rec[0].data;
                                                me.getStatusOpen(data.id);
                                                me.loadDetails();
                                            },
                                            change: function(obj, val){
                                                if(val && obj.displayTplData.length){
                                                    let data = obj.displayTplData[0];

                                                    let site_on = Ext.getCmp('site_container');
                                                    data.site_on ? site_on.show() : site_on.hide();

                                                    let site_off = Ext.getCmp('remove_site_container');
                                                    data.site_off ? site_off.show() : site_off.hide();
                                                }
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'combo', name: 'service_id', fieldLabel: 'Service', flex: 1, allowBlank: true,
                                        forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all',
                                        displayField: 'name', valueField: 'id', value: 4,
                                        store: Ext.create('Ext.data.Store', {
                                            data: services,
                                            fields : [
                                                {name: 'id', type: 'int'},
                                                {name: 'name', type: 'string'},
                                            ]
                                        }),
                                    },
                                ]
                            },

                            // SITE INSTALL & REMOVE -------------------------------------------------------------------
                            {
                                xtype: 'fieldcontainer',
                                layout: {type: 'hbox', align: 'stretch'}, margin: '5 5',
                                items: [
                                    // SITE INSTALL -------------------------------------------
                                    {
                                        xtype: 'container', id: 'site_container',
                                        flex: 1, margin: '0 5',
                                        layout: {type: 'vbox', align: 'stretch'},
                                        items: [
                                            {
                                                xtype: 'combo',
                                                name: 'site_id',
                                                fieldLabel: 'Site Install',
                                                emptyText: 'Select Site',
                                                allowBlank: true,
                                                margin: '0 5',
                                                forceSelection: true,
                                                typeAhead: true,
                                                hideTrigger: true,
                                                queryMode: 'remote',
                                                minChars: 1,
                                                triggerAction: 'query',
                                                displayField: 'name',
                                                valueField: 'id',
                                                store: Ext.create('Ext.data.Store', {
                                                    extend: 'Ext.data.Model',
                                                    pageSize: 10,
                                                    fields: [
                                                        {name: 'id', type: 'int'},
                                                        {name: 'client_id', type: 'int'},
                                                        {name: 'link_id', type: 'string'},
                                                        {name: 'name', type: 'string'},
                                                        {name: 'address', type: 'string'},
                                                        {name: 'pic', type: 'string'},
                                                        {name: 'pic_phone', type: 'string'},
                                                        {name: 'pic_email', type: 'string'}

                                                    ],
                                                    proxy: {type: 'ajax', url: '{{ route('wo.data.site') }}'},
                                                }),
                                                listeners: {
                                                    change: function (combo) {
                                                        let rec = combo.displayTplData;
                                                        if (rec && rec.length) {
                                                            rec = rec[0];
                                                            let client = find(clients, rec.client_id);
                                                            me.setField('client_id', rec.client_id);
                                                            me.setField('form-site-client', client ? client.name : '-');
                                                            me.setField('form-site-linkid', rec.link_id ? rec.link_id : '-');
                                                            me.setField('form-site-address', rec.address);
                                                            me.setField('form-site-pic', rec.pic + ' (' + rec.pic_phone + ')');
                                                        }
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'fieldset',
                                                border: true,
                                                padding: 10,
                                                margin: 5,
                                                flex: 1,
                                                layout: {type: 'vbox', align: 'stretch'},
                                                fieldDefaults: {labelAlign: 'left'},
                                                items: [
                                                    { xtype: 'displayfield', name: 'form-site-client', fieldLabel: 'Client' },
                                                    { xtype: 'displayfield', name: 'form-site-linkid', fieldLabel: 'Link ID' },
                                                    { xtype: 'displayfield', name: 'form-site-pic', fieldLabel: 'PIC' },
                                                    { xtype: 'displayfield', name: 'form-site-address', fieldLabel: 'Address' },
                                                ]
                                            },
                                        ]
                                    },

                                    // SITE REMOVE --------------------------------------------
                                    {
                                        xtype: 'container', id: 'remove_site_container',
                                        flex: 1, margin: '0 5', hidden: true,
                                        layout: {type: 'vbox', align: 'stretch'},
                                        items: [
                                            {
                                                xtype: 'combo',
                                                name: 'remove_site_id',
                                                fieldLabel: 'Dismantle Site',
                                                emptyText: 'Select Dismantle Site',
                                                allowBlank: true,
                                                margin: '0 5',
                                                forceSelection: true,
                                                typeAhead: true,
                                                hideTrigger: true,
                                                queryMode: 'remote',
                                                minChars: 1,
                                                triggerAction: 'query',
                                                displayField: 'name',
                                                valueField: 'id',
                                                store: Ext.create('Ext.data.Store', {
                                                    extend: 'Ext.data.Model',
                                                    pageSize: 10,
                                                    fields: [
                                                        {name: 'id', type: 'int'},
                                                        {name: 'client_id', type: 'int'},
                                                        {name: 'service_id', type: 'int'},
                                                        {name: 'owner_id', type: 'int'},
                                                        {name: 'link_id', type: 'string'},
                                                        {name: 'name', type: 'string'},
                                                        {name: 'address', type: 'string'},
                                                        {name: 'pic', type: 'string'},
                                                        {name: 'pic_phone', type: 'string'},
                                                        {name: 'pic_email', type: 'string'}

                                                    ],
                                                    proxy: {type: 'ajax', url: '{{ route('wo.data.site') }}'},
                                                }),
                                                listeners: {
                                                    change: function (combo) {
                                                        let rec = combo.displayTplData;
                                                        if (rec && rec.length) {
                                                            rec = rec[0];
                                                            let client = find(clients, rec.client_id);
                                                            me.setField('form-remove-site-client', client ? client.name : '-');
                                                            me.setField('form-remove-site-linkid', rec.link_id ? rec.link_id : '-');
                                                            me.setField('form-remove-site-address', rec.address);
                                                            me.setField('form-remove-site-pic', rec.pic + ' (' + rec.pic_phone + ')');
                                                            if(rec.service_id) me.setField('service_id', rec.service_id);
                                                            if(rec.owner_id) me.setField('owner_id', rec.owner_id);
                                                        }
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'fieldset',
                                                border: true,
                                                padding: 10,
                                                margin: 5,
                                                flex: 1,
                                                layout: {type: 'vbox', align: 'stretch'},
                                                fieldDefaults: {labelAlign: 'left'},
                                                items: [
                                                    { xtype: 'displayfield', name: 'form-remove-site-client', fieldLabel: 'Client' },
                                                    { xtype: 'displayfield', name: 'form-remove-site-linkid', fieldLabel: 'Link ID' },
                                                    { xtype: 'displayfield', name: 'form-remove-site-pic', fieldLabel: 'PIC' },
                                                    { xtype: 'displayfield', name: 'form-remove-site-address', fieldLabel: 'Address' },
                                                ]
                                            },
                                        ]
                                    },
                                ]
                            },

                            // OWNER & DATE ----------------------------------------------------------------------------

                            {
                                xtype: 'fieldcontainer', margin: '0 15',
                                layout: {type: 'hbox', align: 'stretch'},
                                items: [
                                    // OWNER -----------------------------------------------------------------
                                    {
                                        xtype: 'combo', name: 'owner_id', fieldLabel: 'Owner', margin: '0 15 0 0', flex: 1, hidden: true, allowBlank:true,
                                        forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all',
                                        displayField: 'name', valueField: 'id', value: 4,
                                        store: Ext.create('Ext.data.Store', {
                                            data: owners,
                                            fields : [
                                                {name: 'id', type: 'int'},
                                                {name: 'name', type: 'string'},
                                            ]
                                        }),
                                    },

                                    // DATE CONTAINER ---------------------------------------------------------
                                    {
                                        xtype: 'fieldcontainer', flex: 1,
                                        layout: {type: 'hbox', align: 'stretch'},
                                        items: [
                                            {
                                                xtype: 'datefield', name: 'start_date', fieldLabel: 'Start Date',
                                                format: 'd/m/Y', submitFormat: 'Y-m-d',
                                                flex: 1, margin: '0 0 0 0',
                                            },
                                            {
                                                xtype: 'datefield', name: 'expire_date', fieldLabel: 'End Date',
                                                format: 'd/m/Y', submitFormat: 'Y-m-d',
                                                flex: 1, margin: '0 0 0 5',
                                            },
                                        ]
                                    },
                                ]
                            },

                            // DESCRIPTION -----------------------------------------------------------------------------
                            {
                                xtype: 'textarea',
                                name: 'description',
                                fieldLabel: 'Project Name / Description',
                                allowBlank: true,
                                flex: 1,
                                margin: '5 15 20 15',
                                minHeight: 70,
                            },
                        ]
                    },

                    {
                        xtype: 'container', width: 500, margin: '0 15',
                        layout: {type: 'vbox', align: 'stretch'},
                        items:[
                            // VENDOR  -------------------------------------------------------------------------
                            {
                                xtype: 'combo', name: 'vendor_id', fieldLabel: 'Area', margin: '5 0 0 0',
                                forceSelection: true, editable: false, queryMode: 'local', triggerAction: 'all',
                                displayField: 'name', valueField: 'id', allowBlank: true,
                                store: Ext.create('Ext.data.Store', {
                                    data: vendors,
                                    fields : [
                                        {name: 'id', type: 'int'},
                                        {name: 'name', type: 'string'},
                                        {name: 'alias', type: 'string'},
                                        {name: 'address', type: 'string'},
                                        {name: 'phone', type: 'string'},
                                        {name: 'email', type: 'string'},
                                        {name: 'description', type: 'string'}
                                    ]
                                }),
                                listeners: {
                                    change: function(){ me.setField('fieldtech_id', null) }
                                }
                            },

                            // FIELDTECH ---------------------------------------------------------------
                            {
                                xtype: 'combo',
                                name: 'fieldtech_id',
                                fieldLabel: 'Fieldtech',
                                emptyText: 'Select Fieldtech',
                                margin: '5 0 5 0',
                                allowBlank: true,
                                forceSelection: true, typeAhead: true, hideTrigger:true,
                                queryMode: 'remote', minChars: 1, triggerAction: 'query',
                                displayField: 'name', valueField: 'id',
                                store: Ext.create('Ext.data.Store', {
                                    extend: 'Ext.data.Model',
                                    pageSize: 10,
                                    fields : [
                                        {name: 'id', type: 'int'},
                                        {name: 'name', type: 'name'},
                                    ],
                                    proxy: {type: 'ajax', url: '{{ route('wo.data.fieldtech') }}'},
                                    listeners:{
                                        beforeload: function(obj){
                                            let client = me.getField('vendor_id').displayTplData;
                                            let clientId = (client && client.length) ? client[0].id : null;
                                            Ext.apply(obj.getProxy().extraParams, {'vendor': clientId});
                                        }
                                    }
                                }),
                            },

                            // PROPERTIES GRID ---------------------------------------------------------
                            {
                                xtype: 'container', flex: 2,
                                layout: {type: 'vbox', align: 'stretch'},
                                items:[
                                    {xtype: 'tbtext', text: '<b>Property Detail</b>', margin: '5 0 5 0'},
                                    {
                                        xtype: 'propertygrid',
                                        id: 'form-detail-property',
                                        cls: 'form-detail-property',
                                        flex: 1,
                                        margin: '2 0 0 0',
                                        border: true,
                                        sortableColumns: false,
                                        minHeight: 100,
                                    },
                                ]
                            },

                            // NOTES --------------------------------------------------------------------
                            {
                                xtype: 'textarea',
                                cls: 'fieldlabel-property',
                                name: 'note',
                                fieldLabel: 'Note / Description',
                                allowBlank: true,
                                flex: 1,
                                margin: '2 0 0 0',
                            },

                            {xtype: 'container', margin: '10'}
                        ]
                    }
                ],
            });

            me.detailProperty = new Uwa.GridProperty('form-detail-property');

            me.createWindowForm(null, me.form, {
                maximized: true,
                layout: 'border',
                header: false,
            });

            forms.clear();
        };

        me.clear = function(){
            me.actionStatus = null;
            me.reset();
            me.form.url = '{{ route('wo.create') }}';
            me.detailProperty.clear();
            me.setTbarTitle('CREATE NEW WORK ORDER');
            me.setReadOnly(false);
            me.getField('note').hide();

            let cmp = me.getField('fieldtech_id');
                cmp.setReadOnly(true);
                cmp.addCls('readonly');
                cmp.hide();

            Ext.getCmp('remove_site_container').hide();
        }

        me.getStatusOpen = function(activity){
            let result = null;
            let grepData = grep(statusAction, {'type': 0});
            grepData.forEach(function (sts) {
                if(find(sts.activities, activity)) {
                    me.actionStatus = sts;
                    result = sts.id;
                    return true;
                }
            });
            return result;
        }

        me.loadDetails = function(value){
            let status = me.actionStatus;
            if(status){
                me.detailProperty.loadSource({
                    sources: status.details,
                    values: value || [],
                    keyId: 'detail_id',
                    keyValue: 'value',
                    keyValueFile: 'files',
                    prefixFile: '{{ route('upload.file') }}',
                });

                let hideDetails = grep(status.details, {type: 'hide'});
                hideDetails.forEach(function (detail) {
                    if(detail.property == 'fieldtech'){
                        let cmp = me.getField('fieldtech_id');
                        cmp.show();
                        cmp.setReadOnly(false);
                        cmp.removeCls('readonly');
                        cmp.allowBlank = false;
                    }
                });
            }
        }

        me.loadData = function(rec){
            if(!isNull(rec)){
                me.setField('activity_id', rec.activity_id);
                me.setField('vendor_id', rec.vendor_id);
                me.setField('client_id', rec.client_id);
                me.setField('owner_id', rec.owner_id);
                me.setField('service_id', rec.service_id);
                me.setField('no_wo', rec.no_wo);
                me.setField('description', rec.description);
                me.setField('start_date', rec.start_date);
                me.setField('expire_date', rec.expire_date);

                me.getField('site_id').store.getProxy().extraParams = {query: rec.site_id};
                me.getField('site_id').store.load(function () {
                    me.setField('site_id', rec.site_id);
                });

                me.getField('remove_site_id').store.getProxy().extraParams = {query: rec.remove_site_id};
                me.getField('remove_site_id').store.load(function () {
                    me.setField('remove_site_id', rec.remove_site_id);
                });

                let fieldtech = me.getField('fieldtech_id');
                fieldtech.store.getProxy().extraParams = {query: rec.fieldtech_id, vendor: me.getValue('vendor_id')};
                fieldtech.store.load(function () {
                    me.setField('fieldtech_id', rec.fieldtech_id);
                });

                return true;
            }
            return false;
        }

        me.create = function(){
            me.clear();
            me.show();
        }

        me.edit = function(rec){
            if(rec) {
                let action = rec.actions[0];
                me.show();
                me.clear();
                me.form.url = '{{ route('wo.edit') }}/' + rec.id;
                me.setTbarTitle('EDIT WORK ORDER ('+rec.id+')');
                me.loadData(rec);
                me.actionStatus = find(statusAction, action.status_id);
                me.loadDetails(action.details);

                if(action.status_id != rec.last_action.status_id){
                    let cmp = me.getField('activity_id');
                    cmp.setReadOnly(true);
                    cmp.addCls('readonly');
                }
            }
            else Ext.example.msg('Warning!', 'Please select data!');
        }

        me.createStatus = function(rec, status){
            if(rec) {
                me.show();
                me.clear();
                me.actionStatus = status;
                me.form.url = '{{ route('wo.push.action') }}/' + rec.id + '/' + status.id;
                me.loadData(rec);
                me.loadDetails(rec.actions[0].details);
                me.setReadOnly(true);
                me.setTbarTitle(status.name + ' ('+rec.id+')');
                me.getField('note').show();
                if(rec.fieldtech_id) me.getField('fieldtech_id').show();
            }
            else Ext.example.msg('Warning!', 'Please select data!');
        }

        me.setReadOnly = function(readonly){
            me.getField('activity_id').setReadOnly(readonly);
            me.getField('vendor_id').setReadOnly(readonly);
            me.getField('service_id').setReadOnly(readonly);
            me.getField('owner_id').setReadOnly(readonly);
            me.getField('no_wo').setReadOnly(readonly);
            me.getField('description').setReadOnly(readonly);
            me.getField('start_date').setReadOnly(readonly);
            me.getField('expire_date').setReadOnly(readonly);
            me.getField('site_id').setReadOnly(readonly);
            me.getField('remove_site_id').setReadOnly(readonly);

            let cmp = null;
            cmp = me.getField('activity_id'); cmp.removeCls('readonly'); if(readonly) cmp.addCls('readonly');
            cmp = me.getField('vendor_id'); cmp.removeCls('readonly'); if(readonly) cmp.addCls('readonly');
            cmp = me.getField('service_id'); cmp.removeCls('readonly'); if(readonly) cmp.addCls('readonly');
            cmp = me.getField('owner_id'); cmp.removeCls('readonly'); if(readonly) cmp.addCls('readonly');
            cmp = me.getField('no_wo'); cmp.removeCls('readonly'); if(readonly) cmp.addCls('readonly');
            cmp = me.getField('description'); cmp.removeCls('readonly'); if(readonly) cmp.addCls('readonly');
            cmp = me.getField('start_date'); cmp.removeCls('readonly'); if(readonly) cmp.addCls('readonly');
            cmp = me.getField('expire_date'); cmp.removeCls('readonly'); if(readonly) cmp.addCls('readonly');
            cmp = me.getField('site_id'); cmp.removeCls('readonly'); if(readonly) cmp.addCls('readonly');
            cmp = me.getField('remove_site_id'); cmp.removeCls('readonly'); if(readonly) cmp.addCls('readonly');
        }

        me.setTbarTitle = function(text){
            Ext.getCmp('form-title').setText(text);
        }

        me.checkRequired = function(details){
            if(me.actionStatus && me.actionStatus.details) {
                for (let i = 0; i < me.actionStatus.details.length; i++) {
                    let rec = me.actionStatus.details[i];
                    let message = rec.name + ' Is Required';

                    if ((rec.type != 'hide') && rec.required) {
                        let detail = find(details, rec.id);
                        if(detail) {
                            if (detail.value) {
                                if (rec.type == 'signature') {
                                    if (detail.value.length < 2900) return message;
                                } else if ((typeof detail.value) == 'object' && !detail.value.length) {
                                    return message;
                                }
                            } else return message;
                        }
                        else return message;
                    }
                }
            }
            return null;
        }

        me.save = function(){
            let errorMessage = null;
            let details = me.detailProperty.get();
            errorMessage = me.checkRequired(details);

            let hideDetails = grep(me.actionStatus.details, {type: 'hide'});
            hideDetails.forEach(function (rec) {
                if(rec.property == 'fieldtech') {
                    let val = me.getValue('fieldtech_id');
                    if(rec.required && !val) {
                        console.log(val);
                        errorMessage = rec.name + ' Is Required';
                    }
                    else details.push({id: rec.id, value: val});
                }
            });

            if(errorMessage) Ext.msg.failed(errorMessage);
            else {
                me.submit(me.form.url, {
                    params: {details: Ext.encode(details)},
                    success: function () {
                        if (!isNull(me.onSave)) me.onSave();
                    }
                });
            }
        }
    }

</script>
