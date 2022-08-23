<style>
    .wo-detail{ margin: 10px; }
    .wo-detail .header-title{font-size: 15px; font-weight: 700; padding: 0px 0px}
    .wo-detail .container{font-size: 13px; border: 1px solid #ddd; background: #fff; border-radius: 5px}
    .wo-detail .info-container{margin: 10px 0px; padding: 15px 15px;}
    .wo-detail .container span{color: #666; font-size: 10px; display: block; font-weight: 700; padding-bottom: 2px}
    .wo-detail .container .info{color: #666; font-size: 10px;}
    .wo-detail .box-color{font-size: 10px; border-radius: 2px; padding: 0px 5px; color: #fff; background: #333; display: inline-block}
    .wo-detail .container .circle-photo {width: 40px; height: 40px; border: 1px solid #eee; border-radius: 50%; margin: 5px 15px 5px 0px;}

    .wo-detail .action-container{display: flex;}
    .wo-detail .action-container .indicator{margin: 0px 10px;}
    .wo-detail .action-container .indicator .icon{border-radius: 50%; border: 2px solid red; width: 8px; height: 8px;}
    .wo-detail .action-container .indicator .line{width: 2px; background: #EEE; height: calc(100% - 10px); margin: 0px 5px}
    .wo-detail .action-container .content{flex: auto; padding: 0px 10px; margin-top: -3px; margin-bottom: 20px}
    .wo-detail .action-container .indicator .startend{background: #eee; border-radius: 5px; display: inline-block; font-size: 10px; padding: 2px 5px}

    .wo-detail .action-detail-container{margin-top: 10px; display: none}
    .wo-detail .toggle-detail{*font-weight: normal; font-size: 12px; color: #666}
    .wo-detail .action-detail-container .item{margin: 10px 0px}
    .wo-detail .action-detail-container .group{font-size: 12px; border-top : 1px dotted #eee; padding-top: 10px; margin-top: 10px;}
    .wo-detail .action-detail-container .option{ margin-block-start: 0px !important; margin-block-end: 0px; margin-inline-start: 0px; margin-inline-end: 0px; padding-inline-start: 17px; font-size: 11px }

    .part-container .files img{width: 80px; height: 80px; border: 1px solid #ddd; border-radius: 5px; padding: 2px}
    .part-container .container{margin: 10px 0px; padding: 15px 15px; position: relative;}
    .part-container .button-menu-part {
        position: absolute;
        top: 0; right: 0;
        padding-top: 15px;
        padding-right: 10px;
        cursor: pointer;
    }
    .menu-part-container {
        position: absolute;
        top: 0; right: 0;
        margin-top: 35px;
        margin-right: 15px;
        background-color: #FAFAFA;
        border: 1px solid #eee;
    }
    .menu-part-container .menu{
        padding: 7px 15px; cursor: pointer;
        width: 100px;
    }

    .wo-detail .btn{
        text-align: center; color: #FFF; padding: 10px; background: #0e90d2; font-weight: 600;
        border-radius: 5px; margin: 10px 0px; cursor: pointer;
    }

</style>

@if(isMobile()) @require('style_mobile') @endif

<script>
    var DetailWo = function (dom, properties) {
        let me = this;
        properties = properties || {};

        me.dom_id = dom;
        me.data = null;

        if(typeof properties == 'string') me.id = properties;
        else{
            me.id = properties.id || null;
            me.partAdd = properties.partAdd || null;
            me.partEdit = properties.partEdit || null;
            me.partDelete = properties.partDelete || null;
        }

        let renderTpl = function(data) {
            let dataTpl = JSON.parse(JSON.stringify(data));

            dataTpl.activity = find(activities, data.activity_id);
            dataTpl.service = find(services, data.service_id);
            dataTpl.owner = find(owners, data.owner_id);

            // CLIENT TEMPLATE -----------------------------------------------
            dataTpl.client = find(clients, data.client_id);
            dataTpl.clientTpl = '';
            if (dataTpl.client) {
                dataTpl.clientTpl = String.format(`<div class="container info-container">
                        <span>Client</span> {name}
                        <div class="info"> {address} <br> <i>(Phone: {phone}, Email: {email})</i> </div>
                    </div>`, dataTpl.client)
            }

            // VENDOR TEMPLATE -----------------------------------------------
            dataTpl.vendor = find(vendors, data.vendor_id);
            dataTpl.vendorTpl = '';
            if (dataTpl.vendor) {
                dataTpl.vendorTpl = String.format(`<div class="container info-container">
                        <span>Area</span> {name}
                        <div class="info">{address} <br> <i>(Phone: {phone}, Email: {email})</i>
                        </div>
                    </div>`, dataTpl.vendor)
            }

            // SITE TEMPLATE -----------------------------------------------
            dataTpl.siteTpl = '';
            if (dataTpl.site) {
                dataTpl.site.linkTpl = dataTpl.site.link_id ? '(' + dataTpl.site.link_id + ')' : '';
                dataTpl.siteTpl = String.format(`<div class="container info-container">
                        <span>Site</span> {name} {linkTpl}
                        <div class="info">{address} <br> <i>PIC: {pic} <br> ({pic_phone}, {pic_email}))</i> </div>
                    </div>`, dataTpl.site);
            }

            // SITE REMOVE TEMPLATE ------------------------------------------
            dataTpl.removeSiteTpl = '';
            if (dataTpl.remove_site) {
                dataTpl.remove_site.linkTpl = dataTpl.remove_site.link_id ? '(' + dataTpl.remove_site.link_id + ')' : '';
                dataTpl.removeSiteTpl = String.format(`<div class="container info-container">
                        <span>Dismantle Site</span> {name} {linkTpl}
                        <div class="info">{address} <br> <i>PIC: {pic} <br> ({pic_phone}, {pic_email})</i> </div>
                    </div>`, dataTpl.remove_site);
            }

            // FIELDTECH -----------------------------------------------------
            dataTpl.fieldtechTpl = '';
            if (dataTpl.fieldtech) {
                dataTpl.fieldtech.photo = (dataTpl.fieldtech && dataTpl.fieldtech.photo) ? '{{ route('upload.file') }}/' + dataTpl.fieldtech.photo : '{{asset('images/nouser.png')}}';

                dataTpl.fieldtech.user = [];
                dataTpl.fieldtech.users.forEach(function(e){
                    dataTpl.fieldtech.user.push(e.name)
                });
                dataTpl.fieldtech.user.join('<br>');

                dataTpl.fieldtechTpl = String.format(`
                    <div class="container info-container">
                        <span>Team & Fieldtech</span>
                        <div style="display: flex">
                            <div style="flex: 1; text-align: left; display: inline-block; padding-top: 4px">
                                {name} <div class="info">{user}</div>
                            </div>
                        </div>
                    </div>`, dataTpl.fieldtech);
            }

            // DATE -------------------------------------------------------
            dataTpl.created_at = dates.format(dataTpl.created_at);

            let startDate = dataTpl.start_date;
            let durationTarget = '0';
            if(startDate) {
                dataTpl.start_date = dates.format(startDate);
                let durationTarget = dates.diffServer(startDate);
            }
            else dataTpl.start_date = '-';

            dataTpl.slot_name = '-';
            if(dataTpl.slot_id){
                let slot = find(slots, dataTpl.slot_id);
                if(slot){
                    dataTpl.slot_name = slot.name;
                }
            }


            dataTpl.duration_target = durationTarget.day;

            // LAST STATUS ------------------------------------------------
            dataTpl.last_action_status = find(statusAction, dataTpl.last_action.status_id);
            dataTpl.last_action.date = dates.format(dataTpl.last_action.created_at);
            dataTpl.last_action.by = dataTpl.last_action.created_by ? dataTpl.last_action.created_by.name : '';
            dataTpl.last_action.duration = dates.diffServer(startDate).day;
            if (dataTpl.last_action_status.type == 2) dataTpl.last_action.duration = dates.diff(startDate, dataTpl.last_action.created_at).day;
            dataTpl.last_action.duration_color = (dataTpl.duration_target < dataTpl.last_action.duration) ? 'DD0000' : '333333'

            dataTpl.link_bast = '{{ route('report.bast.pdf') }}/'+data.id;
            dataTpl.link_pdf = '{{ route('wo.export.pdf') }}/'+data.id;

            // ACTIONS TPL ------------------------------------------------
            let actionTpl = [];
            dataTpl.actions.forEach(function (action) {
                action.toggleDetailDisplay = "display: none;";
                action.status = find(statusAction, action.status_id);
                action.created_date = dates.format(action.created_at, 'M d, H:i');
                action.view_map = '';
                action.note = action.note ? action.note + '<br>' : '';
                if(action.lat && action.long){
                    action.view_map = String.format('<a style="text-decoration: none; color: #007fff" href="https://www.google.com/maps/search/?q={lat},{long}&z=9" target="_view_map">View Map</a>', action);
                }


                // ACTION DETAILS -----------------------------------------
                let actionDetails = [];
                let group = null;
                action.status.details.forEach(function (detail) {
                    let detailName = detail.name;
                    let detailValue = '';

                    let val = find(action.details, {detail_id: detail.id});
                    if (val) {
                        if (detail.type == 'hide') {
                            if (detail.property == 'fieldtech') {
                                if (val.fieldtech) {
                                    val.fieldtech.user = [];
                                    val.fieldtech.users.forEach(function(e){
                                        val.fieldtech.user.push(e.name)
                                    });
                                    val.fieldtech.user.join('<br>');

                                    let tpl = `<div style="display: flex">
                                                    <div style="flex: 1; text-align: left; display: inline-block; padding-top: 4px">
                                                        {name}
                                                        <div class="info">{user}</div>
                                                    </div>
                                               </div>`;
                                    detailValue = String.format(tpl, val.fieldtech);
                                }
                            }
                        } else if (detail.type == 'file') {
                            if (val.files && val.files.length) {
                                val.files.forEach(function (f) {
                                    f.target = '';
                                    switch (f.type) {
                                        case 'image':
                                            f.thumbUrl = '{{ route("upload.file") }}/' + f.id;
                                            f.target = 'target="_blank"';
                                            break;
                                        case 'pdf':
                                            f.thumbUrl = '{{asset("images/filetype/pdf.png")}}';
                                            break;
                                        default:
                                            f.thumbUrl = '{{asset("images/filetype/file.png")}}';
                                    }
                                    let tpl = `<a href="{{ route("upload.file") }}/{id}" {target}>
                                                    <img style="width: 40px; height: 40px" src="{thumbUrl}">
                                               </a>`;
                                    detailValue += String.format(tpl, f);
                                });
                            }
                        } else if (detail.type == 'signature') {
                            if (val.value) {
                                detailValue += '<img style="width: 100%; border: 1px solid #eee" src="{{ route("upload.file") }}/' + val.value + '">';
                            }
                        } else {
                            if (val.value) {
                                if (detail.type == 'combo') {
                                    if (detail.property) {
                                        let opt = find(detail.options, val.value);
                                        if (opt) {
                                            detailValue = opt.option[detail.property.displayField];
                                            detailValue += '<ul class="option">';
                                            detail.property.mapping.forEach(function (map) {
                                                detailValue += '<li>' + map.name + ': ' + opt.option[map.key] + '</li>';
                                            });
                                            detailValue += '</ul>';
                                        }
                                    } else {
                                        let opt = find(detail.options, val.value);
                                        detailValue = opt ? opt.option : null;
                                    }
                                } else {
                                    switch (detail.type) {
                                        case 'text':
                                            detailValue = val.value;
                                            break;
                                        case 'textarea':
                                            detailValue = val.value.replace(/\n/g, "<br/>");
                                            break;
                                        case 'datetime':
                                            detailValue = dates.format(val.value, 'd M Y, H:i a');
                                            break;
                                        case 'date':
                                            detailValue = dates.format(val.value, 'd M Y');
                                            break;
                                        case 'time':
                                            detailValue = dates.format(val.value, 'H:i a');
                                            break;
                                        default:
                                            detailValue = val.value;
                                            break;
                                    }
                                }
                            }
                        }

                        if (detailValue) {
                            if (group != detail.group) {
                                group = detail.group;
                                actionDetails.push('<span class="group">' + group + ':</span>');
                            }
                            actionDetails.push('<div class="item"><span>' + detailName + ':</span> ' + detailValue + '</div>');
                        }
                    }
                })

                if (actionDetails.length) {
                    action.toggleDetailDisplay = "";
                    action.actionDetails = actionDetails.join(' ');
                } else action.actionDetails = '';

                actionTpl.push(String.format(`@require('tpl/detail_action')`, action));
            });
            dataTpl.actionsTpl = actionTpl.join(' ');

            // SPAREPART TPL ----------------------------------------------
            let partTpl = [];
            dataTpl.parts.forEach(function (rec) {
                rec.date = dates.format(rec.created_at);
                let partPhoto = [];
                rec.files.forEach(function (file) {
                    let url = '{{ route('upload.file') }}/' + file.id;
                    partPhoto.push(String.format('<a href="{0}" target="_blank"><img src="{0}"></a>', url));
                })
                rec.photos = partPhoto.join(' ');
                partTpl.push(String.format(`@require('tpl/detail_part')`, rec));
            });
            dataTpl.partTpl = partTpl.join(' ');

            // DETAIL -----------------------------------------------------
            dataTpl = String.format(`@require('tpl/detail')`, dataTpl);

            if (!isNull(me.dom_id)) {
                $(function () {
                    // EVENT CLICK DETAIL ACTIONS HISTORY -----------------
                    let rotate = function (el, deg) {
                        $(el).animate({borderSpacing: deg}, {
                            step: function (now) {
                                $(this).css('-webkit-transform', 'rotate(' + now + 'deg)');
                                $(this).css('-moz-transform', 'rotate(' + now + 'deg)');
                                $(this).css('transform', 'rotate(' + now + 'deg)');
                            },
                            duration: 'fast'
                        }, 'linear');
                    };

                    $(me.dom_id).html(dataTpl);

                    $('.dropdown-detail').click(function () {
                        let id = this.id.substr((this.id.length - 36), 36);
                        $('#action-detail-' + id).toggle("slideDown", function () {
                            if ($(this).is(":visible")) rotate('#toggle-detail-' + id, 90);
                            else rotate('#toggle-detail-' + id, 0);
                        });
                    });

                    // EVENT CLICK MENU SPAREPART ----------------------------------------
                    $('.part-container').not('.button-menu-part').click(function (e) {
                        if (!$(e.target).closest(".button-menu-part").length) {
                            $('.menu-part-container').hide();
                        }
                    });
                    $('.button-menu-part').click(function (e) {
                        let id = this.id;
                        let dataPart = find(data.parts, id);

                        if ($('#menu-part-' + id).is(":visible")) {
                            $('.menu-part-container').hide();
                        }
                        else {
                            $('.menu-part-container').hide();
                            $('#menu-part-' + id).show();
                        }

                        // EDIT SPAREPART -------------------------------------------------
                        if(!isNull(me.partEdit)){
                            $('.menu-part-container .menu').off('click .menu-part-container .menu');
                            $('.menu-part-container .menu.edit').click(function () {
                                me.partEdit(dataPart);
                            })
                        }
                        else $('.menu-part-container .menu.edit').hide();

                        // DELETE SPAREPART ----------------------------------------------
                        if(!isNull(me.partDelete)){
                            $('.menu-part-container .menu.delete').click(function () {
                                let conf = confirm('Delete Part ('+dataPart.name+')');
                                if(conf) {
                                    $.ajax({
                                        url: '{{ route('wo.delete.part') }}',
                                        type: 'post',
                                        data: {
                                            '_method': 'DELETE',
                                            '_token': '{{ csrf_token() }}',
                                            id: dataPart.id
                                        },
                                        success: function (res) { me.load(); }
                                    });
                                }
                            })
                        }
                        else $('.menu-part-container .menu.delete').hide();

                    });

                    // EVENT CLICK ADD SPAREPART ------------------------------------------

                    if(!isNull(me.partAdd)){
                        $('#add-part-btn').click(function () {
                            me.partAdd(me.data);
                            //formPart.create();
                        });
                    }
                    else $('#add-part-btn').hide();

                    if(isNull(me.partEdit) && isNull(me.partDelete)) $('.button-menu-part').hide();


                });
            }
            return true;
        }

        me.load = function(data){
            if(isNull(data)){
                if(me.id){
                    $.ajax({
                        url: '{{ route('wo.get') }}/'+me.id,
                        type: 'get',
                        success: function (res) {
                            me.load(res);
                        }
                    });
                }
            }
            else {
                me.id = data.id;
                me.data = data;
                renderTpl(data);
            }
        }
    }
</script>
