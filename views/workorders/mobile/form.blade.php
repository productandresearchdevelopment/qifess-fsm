<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="{{ asset('js/autocomplete/dist/latest/bootstrap-autocomplete.js') }}"></script>

<link rel="stylesheet" type="text/css" href="{{ asset('js/datetimepicker/jquery.datetimepicker.css') }}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/gum/uwa/uwa.css') }}"/>

{{--<script src="{{ asset("plugins/signature/js/jquery-ui.min.js") }}"></script>--}}
<script src="{{ asset("plugins/jsignature/src/jSignature.js") }}"></script>

<script src="{{ asset('js/datetimepicker/jquery.datetimepicker.full.js') }}"></script>
<script src="{{ asset('js/currency/src/jquery.maskMoney.js') }}"></script>
<script src="{{ asset('plugins/gum/uwa/uwa.js') }}"></script>

<script type="text/javascript" src="https://raw.githubusercontent.com/furf/jquery-ui-touch-punch/master/jquery.ui.touch-punch.min.js"></script>

<style>
    .forms-header{margin: 0px; padding: 20px 30px; background: #666; color: #FAFAFA;}
    .forms-header .title{font-size: 20px}
    .forms-header .subtitle{font-size: 16px}
    .forms-header .tags{ color: #fff; font-size: 10px; margin-top: 5px; display: inline-block}
    .forms-header .tags div{display: inline-block; padding: 2px 5px; margin-right: 5px; }

    .forms-action-details .box.add{background-color: #ddd}

    .jSignature{height: 150px !important; margin: 0px !important;}

    .alert-dialog-content--material {
        overflow: auto;
        max-height: 300px;
    }
</style>

<template modifier="material" id="forms.html">
    <ons-page modifier="material" id="forms" class="ai">
        <ons-toolbar id="forms-toolbar" modifier="material">
            <div class="left">
                <ons-back-button modifier="material"></ons-back-button>
            </div>
            <div id="forms-title" class="center">Form</div>
        </ons-toolbar>

        <div id="forms-content" class="ai-form">
            <div class="forms-header">
                <div class="client" id="forms-client"></div>
                <div class="title" id="forms-woid"></div>
                <div class="subtitle" id="forms-site"></div>
                <div class="tags"></div>
            </div>

            <div style="padding: 20px 20px">
                <div id="forms-fieldtech">
                    <div class="group-title">Fieldtech</div>
                    <ons-input id="forms-input-fieldtech" class="input-fieldtech" modifier="material underbar"></ons-input>
                </div>

                <div id="forms-action-details" class="forms-action-details"></div>

                <div class="group-title">Note / Description</div>
                <textarea id="form-input-note" class="textarea" rows="6"></textarea>

                <ons-button id="forms-submit" modifier="material" style="width: 100%; margin: 10px 0px">UPDATE</ons-button>
            </div>
        </div>
    </ons-page>
</template>

<script>
    Uwa.ready();
    var Forms = function(store){
        let me = this;
        let prefixId = 'forms-input-detail-';

        me.status = null;
        me.data = null;
        me.files = [];
        me.signatures = [];
        me.fieldtech_id = null;

        me.show = function(){
            ai.showPage('forms');
        }

        me.hide = function () {
            $('#forms ons-back-button').trigger('click');
        }

        me.setHeader = function(){
            let status = me.status;
            let data = me.data;
            $(".forms-header").css('background-color', '#'+status.color);
            $("#forms-submit").css('background-color', '#'+status.color);

            $('#forms-title').html(status.name);
            $('#forms-submit').html('UPDATE ' + status.name);

            if(data.site) $('#forms-site').html(data.site.name);
            else if(data.remove_site) $('#forms-site').html(data.remove_site.name);

            $('#forms-woid').html(data.wo_no);

            $('.forms-header .tags').html('');

            let activity = find(activities, data.activity_id);
            if(activity) {
                let tpl = '<div style="background: #{color}">{alias}</div>';
                $('.forms-header .tags').append(String.format(tpl, activity));
            }

            let service = find(services, data.service_id);
            if(service) {
                let tpl = '<div style="background: #{color}">{alias}</div>';
                $('.forms-header .tags').append(String.format(tpl, service));
            }

            let client = find(clients, data.client_id);
            if(client){ $('#forms-client').append(client.name) }
        }

        me.setDetails = function(values){
            let status = me.status;
            let dom = $('#forms-action-details');

            let fields = {
                group: function(text){
                    return '<div class="group-title">'+text+'</div>';
                },
                text: function(detail){
                    let tpl = '<label>{label}</label><ons-input id="{id}" modifier="material underbar" value="{value}"></ons-input>';
                    return String.format(tpl, {
                        id: prefixId+detail.id,
                        label: detail.name,
                        value: detail.default
                    });
                },
                textarea: function(detail){
                    let tpl = '<label>{label}</label><textarea id="{id}" class="textarea" rows="3">{value}</textarea>';
                    return String.format(tpl, {
                        id: prefixId+detail.id,
                        label: detail.name,
                        value: detail.default
                    });
                },
                number: function(detail){
                        let tpl = '<label>{label}</label><ons-input id="{id}" type="number" modifier="material underbar" value="{value}"></ons-input>';
                        return String.format(tpl, {
                            id: prefixId+detail.id,
                            label: detail.name,
                            value: detail.default,
                            inputnumber: detail.property=='currency' ? 'inputcurrency' : 'inputnumber'
                        });
                    },
                datetime: function(detail, type){
                    let tpl = '<label>{label}</label><ons-input id="{id}" class="{type}" modifier="material underbar" value="{value}" readonly></ons-input>';
                    return String.format(tpl, {
                        id: prefixId+detail.id,
                        label: detail.name,
                        value: detail.default,
                        type: type
                    });
                },
                check: function(detail){
                    let tpl = '<div class="checkbox">' +
                        '<ons-checkbox modifier="material" input-id="{id}" {checked}></ons-checkbox>' +
                        '<label for="{id}">{label}</label>' +
                        '</div>';
                    return String.format(tpl, {
                        id: prefixId+detail.id,
                        label: detail.name,
                        checked: detail.default ? 'checked' : ''
                    });
                },
                combo: function(detail){
                    let value = detail.default;
                    let tplOptions = [];
                    detail.options.forEach(function (opt) {
                        let display = opt.option;
                        if(detail.property){
                            if(!isNull(detail.property.displayField)){
                                display = opt.option[detail.property.displayField];
                            }
                            else display = opt.option['name'];
                        }
                        let tpl = '<option value="{id}" {selected}>{display}</option>';
                        tplOptions.push(String.format(tpl, {
                            id: opt.id,
                            display: display,
                            selected: (value == opt.id) ? 'selected' : '',
                        }));
                    })

                    let tpl = '<label>{label}</label><ons-select id="{id}" class="select" modifier="material ">{options}</ons-select>';
                    return String.format(tpl, {
                        id: prefixId+detail.id,
                        label: detail.name,
                        options: tplOptions.join('')
                    });
                },

                list: function(detail) {
                    let listData = window[detail.property] || [];

                    if (me.data && me.data.fieldtech.listvendor_id) {
                        listData = listData.filter(opt => opt.listvendor_id === me.data.fieldtech.listvendor_id);
                    }

                    listData = [{
                        id: '1',
                        name: '-'
                    }, ...listData];

                    let options = listData.map(opt => {
                        let selected = (detail.default == opt.id) ? 'selected' : '';
                        return `<option value="${opt.id}" ${selected}>${opt.name}</option>`;
                    }).join('');

                    return `
                            <label>${detail.name}</label>
                            <ons-select id="${prefixId + detail.id}" class="select" modifier="material">
                                ${options}
                            </ons-select>
                        `;
                },
            }

            let group = null;
            dom.html('');
            $('#forms-fieldtech').hide();
            status.details.forEach(function (detail) {
                let fieldsTpl = null;

                if (detail.required && !detail.name.includes('<i class="bi bi-asterisk"')) {
          detail.name += ' (<i class="bi bi-asterisk" style="font-size: 6px; color: #1d4bc0"></i>)';
        }


                if(detail.type == 'hide'){
                    if(detail.property == 'fieldtech') $('#forms-fieldtech').show();
                }
                else{
                    if(detail.group && group != detail.group){
                        group = detail.group;
                        fieldsTpl = fields.group(group);
                        dom.append(fieldsTpl);
                    }

                    if(detail.type == 'file'){
                        let id = prefixId+detail.id;
                        dom.append('<label>'+detail.name+'</label><div id="'+id+'" style="margin-bottom: 20px"></div>');
                        let fileEditor = new Uwa.FileEditor({
                            prefixFile: '{{ route('upload.file') }}',
                            maxFile: !isNull(detail.property.maxFile) ? detail.property.maxFile : 1,
                            renderTo: '#'+id,
                            files: detail.default,
                            fileType: !isNull(detail.property.fileType) ? detail.property.fileType : '*',
                        });
                        me.files['file'+detail.id] = fileEditor;
                    }

                    else if(detail.type == 'signature'){
                        let pid = prefixId+detail.id;
                        let tpl = ` <label>{label}</label>
                                    <div id="{id}" style="padding-top: 5px; background: #fff; border: 1px dotted #999; border-radius: 5px"></div>
                                    <div id="reset-{id}" style="background: #999; color: #fff; border: 1px solid #DDD; border-radius: 5px; padding: 7px; margin-top: 5px; text-align: center">RESET SIGNATURE</div>`;
                        dom.append(String.format(tpl, {id: pid, label: detail.name}));
                        $(function() {
                            let sig = $('#'+pid).jSignature();
                            $('#reset-'+pid).click(function() { sig.jSignature('reset') });
                            me.signatures['signature'+detail.id] = sig;
                        });
                    }

                    else{
                        switch (detail.type) {
                            case 'text': fieldsTpl = fields.text(detail); break;
                            case 'textarea': fieldsTpl = fields.textarea(detail); break;
                            case 'number': fieldsTpl = fields.number(detail); break;
                            case 'datetime': fieldsTpl = fields.datetime(detail, 'datetimepicker'); break;
                            case 'date': fieldsTpl = fields.datetime(detail, 'datepicker'); break;
                            case 'time': fieldsTpl = fields.datetime(detail, 'timepicker'); break;
                            case 'check': fieldsTpl = fields.check(detail); break;
                            case 'combo': fieldsTpl = fields.combo(detail); break;
                            case 'list': fieldsTpl = fields.list(detail); break;
                        }

                        if(fieldsTpl) dom.append(fieldsTpl);
                    }
                }
            });

            $(function () {
                $('.datetimepicker input').datetimepicker();
                $('.datepicker input').datetimepicker({timepicker: false, format: 'Y-m-d'});
                $('.timepicker input').datetimepicker({datepicker: false, format: 'H:i'});
                $('.inputnumber input').maskMoney({allowNegative: true, thousands:'', decimal:',', precision: 0});
                $('.inputcurrency input').maskMoney({
                    allowNegative: false,
                    thousands:'.',
                    decimal:',',
                    precision: 0,
                    affixesStay: false
                });
                $('.input-fieldtech input').autoComplete({
                    resolverSettings: {
                        url: '{{ route('wo.data.fieldtech') }}?vendor='+me.data.vendor_id
                    },
                    formatResult: function (item) {
                        return {
                            value: item.id,
                            text: item.name,
                        };
                    },
                });
                $('.input-fieldtech input').on('autocomplete.select', function (evt, item) {
                    me.fieldtech_id = item.id;
                });
            });
        }

        me.getDetails = function(){
            let status = me.status;
            let result = [];
            status.details.forEach(function (detail) {
                if(detail.type == 'hide'){
                    if(detail.property == 'fieldtech'){
                        result.push({id: detail.id, value: me.fieldtech_id})
                    }
                }
                else{
                    if(detail.type == 'file'){
                        let f = me.files['file'+detail.id];
                        result.push({id: detail.id, value: f.resultFiles})
                    }
                    else if(detail.type == 'signature'){
                        let val = me.signatures['signature'+detail.id].jSignature("getData");
                        result.push({id: detail.id, value: val})
                    }
                    else{
                        let id = '#'+prefixId+detail.id;
                        if(detail.type == 'number') {
                            let val = $(id).val();
                            val = val.replaceAll('.','');
                            val = val.replaceAll(',','.');
                            result.push({id: detail.id, value: val})
                        }
                        else if(detail.type == 'check') {
                            let val = $(id).is(":checked");
                            result.push({id: detail.id, value: val ? 1 : 0});
                        }
                        else if(detail.type == 'combo' || detail.type == 'list'){
                            let val = $(id+' option:selected').val();
                            result.push({id: detail.id, value: val});
                        }
                        else result.push({id: detail.id, value: $(id).val()})

                    }
                }
            });

            return result;
        }

        me.createAction = function(status, data){
            me.status = status;
            me.data   = data;
            me.files  = [];
            me.show();
            $(function () {
                me.setHeader();
                me.setDetails();
                $('#forms-submit').click(me.save)
            });
        }
        me.checkNumber = function(details) {

            if (me.status && me.status.details) {
            for (let i = 0; i < me.status.details.length; i++) {
                let rec = me.status.details[i];

                let message = rec.name + ' harus diisi dengan angka.';
                if (rec.group === "ADDITIONAL MATERIAL") {
                    let detail = find(details, rec.id);
                        if (detail && (isNaN(detail.value) || detail.value.trim() === "")) {
                            return message
                        }
                    }
                }
            }
            return null;
        }

        me.checkRequired = function(details){
            if(me.status && me.status.details) {
                for (let i = 0; i < me.status.details.length; i++) {
                    let rec = me.status.details[i];
                    let message = rec.name + ' Is Required';
                    if (rec.required) {
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
            ons.notification.confirm('Apakah data yang anda masukan sudah benar?').then(function(confirm) {
                if(confirm){
                    mask.show();
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            let details = me.getDetails();
                            let errorMessage = me.checkRequired(details);
                            let numberErrorMessage = me.checkNumber(details);
                            if(errorMessage) {
                                mask.hide();
                                ons.notification.alert(errorMessage);
                            }else if (numberErrorMessage) {
                                mask.hide();
                                ons.notification.alert(numberErrorMessage);
                            }
                            else {
                                $.ajax({
                                    url: '{{ route('wo.push.action') }}/' + me.data.id + '/' + me.status.id,
                                    type: 'post',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        details: JSON.stringify(details),
                                        note: $('#form-input-note').val(),
                                        lat: position.coords.latitude,
                                        long: position.coords.longitude
                                    },
                                    success: function (res) {
                                        mask.hide();
                                        if (res.success) {
                                            me.hide();
                                            store.load();

                                        } else {
                                            let message = res.message;
                                            ons.notification.alert(message);
                                            //ai.toast('<div style="font-size: 18px; color: #ffa19b">ddddd</div>rrr');
                                        }
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        let message = errorThrown;
                                        ons.notification.alert(message);
                                        mask.hide();
                                    }
                                });
                            }
                        });
                    }
                    else {
                        alert("Geolocation is not supported by this browser.");
                        mask.hide();
                    }
                }
            });
        }
    }
</script>
