@include('headers.head')

<link rel="stylesheet" type="text/css" href="{{ asset('plugins/extjs/resources/ext-theme-neptune/ext-theme-neptune-all-debug.css') }}"  />
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/extjs/examples/shared/example.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/gum/uwa/uwa.css') }}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('css/ext-style.css') }}" />

<script type="text/javascript" src="{{ asset('plugins/extjs/ext-all.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/extjs/packages/ext-theme-neptune/build/ext-theme-neptune.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/extjs/examples/shared/examples.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/gum/uwa/uwa.js') }}"></script>


<script type="text/javascript">
    Ext.Loader.setConfig({enabled: true});
    Ext.Loader.setPath('Ext.ux', '{{ asset('plugins/extjs/examples/ux') }}/');
    Ext.util.Format.thousandSeparator = '.';
    Ext.util.Format.decimalSeparator = ',';

    // CUSTOM EXT-JS ===================================================================

    http = new Ext.data.Connection();

    Ext.ajaxConfirm = function(title, properties){
        if(properties != undefined){
            title = title || '';
            Ext.MessageBox.confirm('Confirm', 'Confirm ' + title + ' ?', function(res){
                if(res == 'yes'){
                    if(properties.mask != undefined) properties.mask.getEl().mask('Loading');
                    http.request({
                        method  : (properties.method == undefined) ? 'post' : properties.method,
                        url     : properties.url,
                        params  : properties.params,
                        success : function(respon, request){
                            Ext.example.msg('Success!', title + ' Success!');
                            if(properties.mask != undefined) properties.mask.getEl().unmask();
                            if(typeof properties.success == 'function') {
                                properties.success(respon);

                            }
                        },
                        failure : function(obj, res){
                            Ext.responFailure(obj, res);
                            if(properties.mask != undefined) properties.mask.getEl().unmask();
                        }
                    });
                }
            });
        }
        else console.log('httpPostConfirm Error', title, properties);
    }

    Ext.menuFilter = function(id){
        let cmp   = Ext.getCmp(id);
        let items = cmp.menu.items.items;
        let item  = null;
        for(let i=0; i < items.length; i++){
            if(items[i].checked){
                item = items[i];
                cmp.setText('<b>'+item.text+'</b>');
                return item.value;
            }
        }
        return null;
    }

    Ext.responFailure = function(obj, res){
        let message = "Error";
        let respon = res.response;

        if(respon.status == 404) message = "Server Not Found...";
        else if(respon.status == 403) message = "Access Denied (403)";
        else if(respon.status == 500) message = "Internal Server Error (500)";
        else if(respon.status == 200) {
            message = "Error (200)";
            if(res.result != undefined && res.result.message != undefined) message =  res.result.message;
            else if(respon.responseText != undefined) message = respon.responseText;
        }
        Ext.example.msg('<span style="color: red">Failed!</span>', message);
    }

    Ext.renderBox = function(text, color, hint, meta){
        if(text){
            hint  = hint || null;
            color = color || 'DDDDDD';
            if(hint) meta['tdAttr'] = 'data-qtip="' + hint + '"';
            return String.format('<div class="box-color" style="background: #{0}">{1}</div>', color, text);
        }
        return null;
    }

    Ext.msg = {
        show: function(title, message){
            Ext.example.msg(title, message);
        },

        success: function(message, time){
            let title = '<span style="color: #3387CC">Success</span>';
            this.show(title, message, time);
        },

        warning: function(message){
            let title = '<span style="color: #FF960D">Warning</span>';
            message = '<span style="font-size: 12px; line-height: 20px">'+message+'</span>';
            this.show(title, message);
        },

        error: function(message){
            let title = '<span style="color: #CC0000">Error</span>';
            message = '<span style="font-size: 12px; line-height: 20px">'+message+'</span>';
            this.show(title, message);
        },

        failed: function(message, time){
            let title = '<span style="color: #FF3366">Failed</span>';
            message = '<span style="font-size: 12px; line-height: 20px">'+message+'</span>';
            this.show(title, message);
        }
    }

    Ext.utils = {
        grids: function(me){
            me.extraParams = {}

            me.httpStore = function(uri, fields, action){
                return Ext.create('Ext.data.Store', {
                    pageSize: 50,
                    fields: fields,
                    remoteSort: true,
                    proxy: {
                        type: 'ajax',
                        url: uri,
                        reader: {root:'data', totalProperty:'count'},
                        simpleSortMode: true
                    },
                    listeners: (action != undefined) ? action : {}
                });
            }

            me.getRecs = function(pluck){
                let recs = me.grid.getSelectionModel().getSelection();
                if(pluck != undefined && pluck) {
                    recs = Ext.pluck(recs, 'data');
                }
                return recs;
            }

            me.getRec = function(pluck){
                let recs = me.getRecs(pluck);
                if(recs.length) return recs[0];
                return null;
            }

            me.getValues = function(field){
                field = field || 'id';
                let recs = me.getRecs(true);
                if(recs.length){
                    recs = Ext.pluck(recs, field);
                }
                return recs;
            }

            me.filters = function(filters){
                /*
                    EXAMPLES
                    [
                        {
                            id: 'database',
                            param: 'db', // PARAMETER ON SEND AJAX
                            name: 'Database Variant',
                            hidden: true / false,
                            iconCls: 'icon-filter',
                            property: {value: 'id', name: 'name' (OR) display: '{id} - {name}'},
                            handler: function(val, rec, obj){},
                            items: [
                                {id: 1, name: 'Mysql'},
                                {id: 2, name: 'Maria DB'},
                                {id: 3, name: 'Sqlite'},
                            ]
                        }
                    ]
                */
                let result = [];
                filters = (filters != undefined && filters) ? filters : [];
                filters.forEach(function (rec) {
                    let pname  = '{name}';
                    let pvalue = 'id';
                    let param  = (rec.param != undefined && rec.param) ? rec.param : 'filter-'+rec.id;

                    let checkHandler = function(obj){
                        obj.parentMenu.ownerButton.setText(obj.text)
                        let val =  obj.checked ? obj.value : null;

                        if(!isNull(rec.handler)) rec.handler(val, rec, obj);

                        me.extraParams[param] = val
                        me.setExtraParams();
                        me.storeLoad();
                    }

                    if(rec.property != undefined){
                        if(rec.property.value != undefined && rec.property.value) pvalue = rec.property.id;
                        if(rec.property.name != undefined && rec.property.name) pname = '{'+rec.property.name+'}';
                        if(rec.property.display != undefined && rec.property.display) pname = rec.property.display;
                    }

                    let text = '<b>ALL '+rec.name.toUpperCase()+'</b>';

                    let items  = [{
                        text: '<b>ALL '+rec.name.toUpperCase()+'</b>',
                        value: null,
                        checked: true,
                        group :'group'+rec.id,
                        handler: checkHandler
                    }];

                    rec.items.forEach(function (item){
                        if(item.checked != undefined && item.checked){
                            items[0].checked = false;
                            text = String.format(pname, item);
                            me.extraParams[param] = item[pvalue];
                        }

                        items.push({
                            text: String.format(pname, item),
                            value: item[pvalue],
                            checked: (item.checked != undefined && item.checked) ? true : false,
                            group :'group'+rec.id,
                            handler: checkHandler
                        });
                    });

                    let iconCls = (rec.iconCls != undefined && rec.iconCls) ? rec.iconCls : 'icon-filter';
                    result.push({
                        id:'filter-'+rec.id,
                        text: text,
                        iconCls: iconCls,
                        hidden: !isUndef(rec.hidden) ? rec.hidden : false,
                        menu: {items: items}
                    });
                });
                return result;
            }

            me.tbar = function(menus, text){
                menus = menus || null;
                text = (text == undefined) ? 'Menu' : (text ? text : '');
                let mainMenu = [];
                if(menus && menus.items.length) mainMenu.push({text: text, iconCls: 'icon-menu', menu: menus});
                mainMenu.push('->');
                mainMenu.push({xtype: 'searchfield', flex:1, maxWidth: 300, minWidth: 180, store: me.store});

                return mainMenu;
            }

            me.bbar = function(filters, bbar){
                let result = (!isNull(bbar)) ? bbar : [];
                filters = (filters != undefined && filters) ? filters : [];
                result = result.concat(me.filters(filters));
                result.push('->', Ext.create('Ext.PagingToolbar',{store: me.store, displayInfo: true, displayMsg: 'Displaying Data : {0} - {1} of {2}', emptyMsg: "No Display Data"}));
                return result;
            }

            me.setExtraParams = function(){
                Ext.apply(me.store.getProxy().extraParams, me.extraParams);
            }

            me.storeLoad = function(action){
                me.setExtraParams();
                me.store.load(action);
                me.grid.getSelectionModel().clearSelections();
            }

            me.renderBox = function(text, color, hint, meta){
                if(text){
                    hint  = hint || null;
                    color = color || 'DDDDDD';
                    if(hint) meta['tdAttr'] = 'data-qtip="' + hint + '"';
                    return String.format('<div class="box-color" style="background: #{0}">{1}</div>', color, text);
                }
                return null;
            }

            me.renderText = function(text, hint, meta, color){
                if(text){
                    hint  = (hint != undefined) ? hint : null;
                    color = (color != undefined) ? color : null; '333333';
                    if(hint) meta['tdAttr'] = 'data-qtip="' + hint + '"';
                    return String.format('<div style="color: #{0}">{1}</div>', color, text);
                }
                return null;
            }

            me.contextMenu = function(obj, rec, node, index, e) {
                if(me.menus != undefined && me.menus.items.length){
                    e.stopEvent();
                    me.menus.showAt(e.getXY());
                }
            }

            return me;
        },

        forms: function(me){
            me.getForm = function(){
                return me.form.getForm();
            }

            me.getField = function(name){
                let form = me.getForm();
                return form.findField(name);
            }

            me.getValue = function(name){
                let field = me.getField(name);
                return field.getValue();
            }

            me.getValueCombo = function(name){
                let field = me.getField(name);
                let data = field.displayTplData;
                if(data.length) return data[0];
                return null;
            }

            me.setField = function(name, value){
                let field = me.getField(name);
                field.setValue(value);
            }

            me.reset = function(){
                let form = me.getForm();
                form.reset();
            }

            me.submit = function(url, properties, autoclose){
                let property = (properties !== undefined  && properties) ? properties : {};
                let form = me.getForm();
                let params = (property.params != undefined) ? property.params : {};
                if(form.isValid()){
                    form.submit({
                        url: url,
                        waitMsg: 'Proses',
                        params: params,
                        submitEmptyText: false,
                        success: function(obj, result){
                            Ext.msg.success('Data Is Saved!');
                            if(property.success != undefined){
                                property.success(Ext.decode(result.response.responseText));
                            }
                            if(autoclose == undefined || autoclose) me.close();
                        },
                        failure: Ext.responFailure
                    });
                }
                else { Ext.msg.warning('Please check your input data!'); }
            }

            return me;
        },

        windowForms: function(me){
            me = Ext.utils.forms(me);

            me.createWindowForm = function(title, form, properties){
                let property = (properties !== undefined) ? properties : {};

                property.items = form;
                property.title = title;
                property.closeAction = 'hide';
                property.resizable = (property.resizable != undefined) ? property.resizable : false;
                property.modal = (property.modal != undefined) ? property.modal : true;
                property.autoScroll = (property.autoScroll != undefined) ? property.autoScroll : true;
                property.header = (property.header != undefined) ? property.header : true;
                property.maximized = (property.maximized != undefined) ? property.maximized : {{ isMobile() ? 'true' : 'false' }};
                property.layout = {type: 'vbox', align: 'stretch'};

                if(!property.header && title){
                    property.dockedItems = [{
                        xtype: 'toolbar', dock: 'top', padding: '10 5',
                        cls: 'win-toolbar-hide-header',
                        items: [
                            {id: 'win-tbtext-title', text: title, cls: 'btn-transparent', iconCls: 'icon-back', handler: me.close, iconAlign: 'left'},
                        ]
                    }];
                }

                if(property.width != undefined){
                    property.width = property.width;
                    property.autoWidth = false;
                }
                else property.autoWidth = true;

                if(property.height != undefined){
                    property.height = properties.height;
                    property.autoHeight = false;
                }
                else property.autoHeight = true;

                me.win = Ext.create('Ext.window.Window', property);
            }

            me.show =  function(){
                me.win.show();
            };

            me.close = function(){
                me.win.hide();
            };

            me.setTitle = function(text){
                if(me.win.header) me.win.setTitle(text);
                else Ext.getCmp('win-tbtext-title').setText(text);
            };

            return me;
        }
    }

</script>
