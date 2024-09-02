<style>
    .site-detail{ margin: 10px; }
    .site-detail .header-title{font-size: 15px; font-weight: 700; padding: 0px 0px}
    .site-detail .container{font-size: 13px; border: 1px solid #ddd; background: #fff; border-radius: 5px}
    .site-detail .info-container{margin: 10px 0px; padding: 15px 15px;}
    .site-detail .container span{color: #666; font-size: 10px; display: block; font-weight: 700; padding-bottom: 2px}
    .site-detail .container .info{color: #666; font-size: 10px;}
    .site-detail .box-color{font-size: 10px; border-radius: 2px; padding: 0px 5px; color: #fff; background: #333; display: inline-block}
    .site-detail .container .circle-photo {width: 40px; height: 40px; border: 1px solid #eee; border-radius: 50%; margin: 5px 15px 5px 0px;}
</style>

<script>

    var detailSite = function (data, dom) {
        let dataTpl = JSON.parse(JSON.stringify(data));
        let woTpl = [];
        let order = 1;
        dataTpl.client = find(clients, dataTpl.client_id);
        dataTpl.vendor = find(vendors, dataTpl.vendor_id); dataTpl.vendor = dataTpl.vendor ? dataTpl.vendor.name : '-';
        dataTpl.active = dataTpl.is_active ? 'active' : 'inactive';
        dataTpl.active_color = dataTpl.is_active ? 'green' : 'red';
        dataTpl.inactive_date = dataTpl.inactive_date ? dates.format(dataTpl.inactive_date, 'd M Y') : '';

        dataTpl.active_date = dataTpl.active_date ? dates.format(dataTpl.active_date, 'd M Y') : dataTpl.inactive_date;
        dataTpl.service = find(service, dataTpl.service_id);
        dataTpl.workorders.forEach(function (wo) {
        wo.id = wo.id
        wo.order = order ++;
        wo.activity = find(activity, wo.activity_id);
        wo.service = find(service, wo.service_id);
        wo.vendor = find(vendors, wo.vendor_id);
        wo.description = wo.description;
        wo.close_date = wo.close_date ? dates.format(wo.close_date, 'd/m/Y') : 'On Progress';

        wo.start_date = dates.format(wo.start_date);
        // wo.expire_date = dates.format(wo.expire_date);
        wo.created_at = dates.format(wo.created_at);
        woTpl.push(String.format(`@require('tpl/list_wo')`,wo));
        })
        dataTpl.woTpl = woTpl.join(' ');
        dataTpl = String.format(`@require('tpl/detail')`, dataTpl);
        if(!isNull(dom)) {
            $(function () {
                let rotate = function(el, deg){
                    $(el).animate({  borderSpacing: deg }, {
                        step: function(now) {
                            $(this).css('-webkit-transform','rotate('+now+'deg)');
                            $(this).css('-moz-transform','rotate('+now+'deg)');
                            $(this).css('transform','rotate('+now+'deg)');
                        },
                        duration:'fast'
                    },'linear');
                };

                $(dom).html(dataTpl);
                $('.detail-wo').click(function () {
                    let id = this.id.substr((this.id.length - 40),40);
                    formdetail.init();
                    $('#iframe-detailWo').attr('src', '{{ route('wo.detail') }}/'+id);
                });
            });
        }
        return true;
    }
</script>
