/*
    created by : ANDIKA
    modified: June 2020
    email: andika2000@gmail.com
    libname: Uwa.GridProperty

    --------------------------------------------------------------------------------------
    EXAMPLES CREATE CLASS OBJECT COMPONENT
    --------------------------------------------------------------------------------------
    >   var gridProperty = new Uwa.GridProperty('my-id')
    >   var gridProperty = new Uwa.GridProperty({
            flex: 1,
            margin: '2 0 30 0',
            border: true,
            {etc}
        });
    >   var component = Ext.widget('propertygrid', {
            flex: 1,
            margin: '2 0 30 0',
            border: true,
            {etc}
        });
        var gridProperty = new Uwa.GridProperty(component)

    --------------------------------------------------------------------------------------
    EXAMPLES LOAD DATA SOURCE << EXAMPLE SOURCES "example.json" >>
    --------------------------------------------------------------------------------------

    me.gridProperty.loadSource({
        sources: [{
            "id":1,
            "type":"text",
            "name":"Text (text)",
            "group":null,
            "default":null,
            "options": null
        }],
        values: [{detail_id: 1, value: 'MY TEXT'}],
        keyId: 'detail_id',
        keyValue: 'value',
        prefixFile: 'http://localhost/images'
    });
*/

Uwa.GridProperty = function(property){
    let me = this;

    me.styleCls = 'uwa-grid-property';
    me.sources = { source: {}, sourceConfig: {}, files: {}, signatures: {}, map: [] };

    me.set = function(property){
        me.id = (typeof property == 'string') ? property : null;
        me.cmp = (typeof property == 'object') ? property : null;
        if(me.cmp || me.id) me.init();
    }

    me.get = function(){
        let result = [];
        let values = me.cmp.source;
        for(var sourceId in values) {
            let map = find(me.sources.map, {sourceId: sourceId});
            if(map && map.type != 'hide'){
                let value = values[sourceId];
                let file = map.file;
                let signature = map.signature;
                if(!isNull(file)){
                    value = file.resultFiles;
                }
                else if(!isNull(signature)) value = signature.get();
                else {
                    switch (map.source.type) {
                        case 'date':
                            value = Ext.Date.format(value, 'Y-m-d');
                            break;
                        case 'time':
                            value = Ext.Date.format(value, 'H:i:s');
                            break;
                        case 'datetime':
                            value = Ext.Date.format(value, 'Y-m-d H:i:s');
                            break;
                    }
                }
                result.push({
                    id: map.id,
                    value: value
                });
            }
        }
        return result;
    }

    me.init = function(){
        if(me.cmp){
            if(me.cmp.constructor.name == "Object"){
                me.cmp.xtype = "propertygrid";
                me.cmp = Ext.widget('propertygrid', me.cmp);
            }
            me.id = me.cmp.getId();
        }
        else if(me.id) me.cmp = Ext.getCmp(me.id);

        if(me.cmp){
            let cmp = me.cmp;
            if(!cmp.hasCls(me.styleCls)) cmp.addCls(me.styleCls);
            cmp.addListener('beforeedit', function (obj, rec) {
                let sourceName = rec.record.data.name;
                if(sourceName.substr(0,20) == 'source-property-file'){
                    return false;
                }
                else if(sourceName.substr(0,25) == 'source-property-signature'){
                    return false;
                }
                return true;
            });
        }

        me.clear();
    }

    me.clear = function(){
        me.sources = { source: {}, sourceConfig: {}, files: {}, signatures: {}, map: [] };
        me.cmp.setSource(me.sources.source, me.sources.sourceConfig);
    }

    me.loadSource = function(dataSource){
        me.clear();
        if(!isNull(dataSource)){
            let sources = [];
            let values = [];
            let keyId = 'detail_id';
            let keyValue = 'value';
            let keyValueFile = 'files';
            let prefixFile = '/file';

            if(dataSource instanceof Array) sources = dataSource;
            else{
                if(!isNull(dataSource.sources)) sources =  dataSource.sources;
                if(!isNull(dataSource.values)) values =  dataSource.values;
                if(!isNull(dataSource.keyId)) keyId =  dataSource.keyId;
                if(!isNull(dataSource.keyValue)) keyValue =  dataSource.keyValue;
                if(!isNull(dataSource.keyValueFile)) keyValueFile =  dataSource.keyValueFile;
                if(!isNull(dataSource.prefixFile)) prefixFile =  dataSource.prefixFile;
            }

            let lastGroup = null;
            sources.forEach(function (source) {
                if(source.required) source.name += ' (<i class="bi bi-asterisk" style="font-size: 7px"></i>)';

                if(source.type != 'hide') {
                    let textGroup = source.group;
                    if(textGroup && (textGroup != lastGroup)) {
                        let gid = Uwa.autoId('source-property');
                        me.sources.sourceConfig[gid] = me.sourceConfig.group(textGroup);
                        me.sources.source[gid] = null;
                        lastGroup = textGroup;
                    }

                    let sid = null;
                    if(source.type == 'file'){
                        sid = Uwa.autoId('source-property-file');
                        me.sources.source[sid] = me.sourceFileValue(sid, source, values, keyId, keyValueFile, prefixFile);
                        me.sources.sourceConfig[sid] = me.sourceConfig.file(source, prefixFile);
                    }
                    else if(source.type == 'signature'){
                        sid = Uwa.autoId('source-property-signature');
                        me.sources.source[sid] = me.sourceSignatureValue(sid, source, values);
                        me.sources.sourceConfig[sid] = me.sourceConfig.signature(source, prefixFile);
                    }
                    else{
                        sid = Uwa.autoId('source-property');
                        switch (source.type) {
                            case ('text'): me.sources.sourceConfig[sid] = me.sourceConfig.text(source); break;
                            case ('textarea'): me.sources.sourceConfig[sid] = me.sourceConfig.textArea(source); break;
                            case ('date'): me.sources.sourceConfig[sid] = me.sourceConfig.date(source); break;
                            case ('time'): me.sources.sourceConfig[sid] = me.sourceConfig.time(source); break;
                            case ('datetime'): me.sources.sourceConfig[sid] = me.sourceConfig.dateTime(source); break;
                            case ('check'): me.sources.sourceConfig[sid] = me.sourceConfig.check(source); break;
                            case ('number'): me.sources.sourceConfig[sid] = me.sourceConfig.number(source); break;
                            case ('combo'): me.sources.sourceConfig[sid] = me.sourceConfig.combo(source); break;
                            //default: me.configEditor.text(sid, source);
                        }

                        me.sources.source[sid] = me.sourceValue(source, values, keyId, keyValue);
                    }

                    me.sources.map.push({
                        id: source.id,
                        sourceId: sid,
                        source: source,
                        file: me.sources.files[sid],
                        signature: me.sources.signatures[sid]
                    });
                }
            });

            me.cmp.setSource(me.sources.source, me.sources.sourceConfig);
        }
    }

    me.sourceFileValue = function(sid, source, values, keyId, keyValueFile, prefixFile){
        let value = [];

        if(values){
            let search = {}; search[keyId] = source.id
            let val = find(values, search);
            if(val){
                val = val[keyValueFile];
                if(val) value = val;
            }
        }

        if(!Array.isArray(value)) value = [value];

        let prop = source.property || {};

        let fileType = prop.fileType || '*';
        let maxWidth = prop.maxWidth || 1024;
        let maxHeight = prop.maxHeight || 768;
        let maxFile = prop.maxFile || 6;
        let autoResize = prop.autoResize || true;

        me.sources.files[sid] = new Uwa.FileEditor({
            prefixFile: prefixFile,
            renderTo: '#display-file-'+sid,
            files: value,
            maxFile: maxFile,
            fileType: fileType,
            maxWidth: maxWidth,
            maxHeight: maxHeight,
            autoResize: autoResize
        });

        return '<div id="display-file-'+sid+'" class="display-file"></div>';
    }

    me.sourceSignatureValue = function(sid, source, values){
        let value = values || null;
        me.sources.signatures[sid] = new me.Signature(sid);
        return `
                    <div id="display-signature-`+sid+`" class="display-signature" style="height: 150px; width: 100%; border: 1px solid #DDD"></div>
                    <div id="display-signature-clear-`+sid+`" style="background: #EEEEEE; border: 1px solid #DDD; border-radius: 5px; padding: 7px; margin-top: 5px; text-align: center">CLEAR</div>
               `;
    }

    me.sourceValue = function(source, values, keyId, keyValue){
        let value = source.default;
        if(values && values.length){
            let search = {}; search[keyId] = source.id;
            let val = find(values, search);
            value = (val) ? val[keyValue] : null;
        }
        if(value){
            switch(source.type){
                case ('combo'): value = parseInt(value); break;
                case ('datetime'): value = dates.parse(value); break;
                case ('date'): value = dates.parse(value); break;
                case ('time'): value = dates.parseTime(value); break;
                case ('check'): value = value ? 1 : 0; break;
                case ('number'): value = parseFloat(value); break;
            }
        }
        return value;
    }

    me.Signature = function(sid){
        let m = this;

        m.sig = null;

        m.init = function(){
            setTimeout(function () {
                m.sig = $('#display-signature-'+sid).signature();
                $('#display-signature-clear-'+sid).click(function() {
                    m.sig.signature('clear');
                });
            },500);
        }

        m.get = function () {
            return m.sig.signature('toDataURL', 'image/jpeg');
        }

        m.init();
    }

    me.sourceConfig = {
        group: function (text, lastGroup) {
            if(text && (text != lastGroup)) {
                return { displayName: '<div class="display-group">' + text + '</div>' };
            }
            return null;
        },

        text: function (source) {
            return {
                displayName: source.name,
                type: 'string',
                editor: {xtype: 'textfield', allowBlank: true, cls: me.styleCls}
            }
        },

        textArea: function (source) {
            return {
                displayName: '<div class="display-textarea">' + source.name + '</div>',
                type: 'string',
                editor: {
                    xtype: 'textarea',
                    allowBlank: true,
                    height: 119,
                    margin: '1 0 0 0',
                    cls: me.styleCls
                },
                renderer: function (val) {
                    val = val || '';
                    return '<div class="display-textarea">' + val.replace(/\n/g, "<br/>") + '</div>';
                }
            }
        },

        date: function (source) {
            return {
                displayName: source.name,
                type: 'date',
                editor: {
                    xtype: 'datefield',
                    allowBlank: true,
                    cls: me.styleCls
                },
                renderer: function (val) {
                    return Ext.Date.format(val, 'd/m/Y');
                }
            }
        },

        time: function (source) {
            let format = 'H:i';
            return {
                displayName: source.name,
                type: 'date',
                editor: {
                    xtype: 'timefield',
                    format: format,
                    allowBlank: true,
                    cls: 'grid-property-detail'
                },
                renderer: function (val) {
                    return Ext.Date.format(val, format);
                }
            }
        },

        dateTime: function (source) {
            let format = 'd/m/Y H:i';
            return {
                displayName: source.name,
                type: 'date',
                editor: {
                    xtype: 'datetimefield',
                    format: format,
                    allowBlank: true,
                    cls: me.styleCls
                },
                renderer: function (val) {
                    return Ext.Date.format(val, format);
                }
            }
        },

        number: function (source) {
            return {
                displayName: source.name,
                type: 'int',
                editor: {
                    xtype: 'numberfield',
                    hideTrigger: true,
                    allowBlank: true,
                    cls: me.styleCls
                },
                renderer: function (val) {
                    if (source.property == 'currency') {
                        return Ext.util.Format.number(val, '0,000');
                    }
                    return val;
                }
            }
        },

        check: function (source) {
            return {
                displayName: source.name,
                type: 'boolean',
                editor: {
                    xtype: 'combo',
                    forceSelection: true,
                    editable: false,
                    queryMode: 'local',
                    triggerAction: 'all',
                    displayField: 'name',
                    valueField: 'id',
                    store: Ext.create('Ext.data.Store', {
                        data: [{id: 1, name: 'Yes'}, {id: 0, name: 'No'}],
                        fields: [
                            {name: 'id', type: 'int'},
                            {name: 'name', type: 'string'}
                        ]
                    })
                },
                renderer: function (val) {
                    if (val == 0) return 'No';
                    else if (val == 1) return 'Yes';
                    else return '';
                }
            }
        },

        combo: function (source) {
            let getProperty = function (property) {
                let prop = property;
                let name = 'name';
                let display = '{' + name + '}';
                if (!isNull(prop)) {
                    if (typeof prop == 'string') {
                        name = prop;
                        display = '{' + name + '}';
                    } else {
                        if (!isNull(prop.displayTpl)) display = prop.displayTpl;
                        else {
                            if (!isNull(prop.displayField)) name = prop.displayField;
                            else if (typeof prop == 'string') name = prop;
                            display = '{' + name + '}';
                        }
                    }
                }
                return {display: display, name: name}
            }

            let options = [];
            source.options.forEach(function (option) {
                let id = option.id;
                let opt = option.option;
                if (typeof opt == 'object') {
                    let prop = getProperty(source.property);
                    options.push({
                        id: id,
                        name: opt[prop.name],
                        display: String.format(prop.display, opt)
                    });
                } else options.push({id: id, name: opt, display: opt});
            });

            return {
                type: 'combo',
                displayName: source.name,
                editor: {
                    xtype: 'combo',
                    allowBlank: true,
                    forceSelection: true,
                    editable: true,
                    queryMode: 'local',
                    triggerAction: 'all',
                    displayField: 'name',
                    valueField: 'id',
                    cls: me.styleCls,
                    listConfig: {
                        getInnerTpl: function () {
                            return '{display}'
                        }
                    },
                    store: Ext.create('Ext.data.Store', {
                        data: options,
                        fields: [
                            {name: 'id', type: 'auto'},
                            {name: 'name', type: 'auto'},
                            {name: 'display', type: 'string'}
                        ]
                    })
                },
                renderer: function (val) {
                    if (val) {
                        let rec = find(options, {id: val});
                        if (rec) return rec.name
                    }
                    return val;
                }
            }
        },

        file: function (source, prefixFile) {
            return {
                displayName: source.name,
                editor: {xtype: 'displayfield'},
                renderer: function (val) { return val }
            }
        },

        signature: function (source) {
            return {
                displayName: source.name,
                editor: {xtype: 'displayfield'},
                renderer: function (val) { return val }
            }
        }
    }

    me.set(property);
}


