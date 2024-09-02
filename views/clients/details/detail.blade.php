<style>
    .client-detail{ margin: 10px; }
    .client-detail .header-title{font-size: 15px; font-weight: 700; padding: 0px 0px}
    .client-detail .container{font-size: 13px; border: 1px solid #ddd; background: #fff; border-radius: 5px}
    .client-detail .info-container{margin: 10px 0px; padding: 15px 15px;}
    .client-detail .container span{color: #666; font-size: 10px; display: block; font-weight: 700; padding-bottom: 2px}
    .client-detail .container .info{color: #666; font-size: 12px;}
    .client-detail .box-color{font-size: 10px; border-radius: 2px; padding: 0px 5px; color: #fff; background: #333; display: inline-block}
    .client-detail .container .circle-photo {width: 75px; height: 75px; border: 1px solid #eee; border-radius: 50%; margin: 5px 15px 5px 0px;}

</style>

<script>

    var detailClient = function (data, dom) {
        let dataTpl = JSON.parse(JSON.stringify(data));
        let detailTpl = "";
        let woTpl = [];
        let order = 1;

        dataTpl.workorders.forEach(function (wo) {
        wo.id = wo.id
        wo.order = order ++;
        wo.activity = find(activity, wo.activity_id);
        wo.service = find(service, wo.service_id);
        wo.site = find(site, wo.site_id);
        wo.description = wo.description;
        wo.close_date = wo.close_date ? dates.format(wo.close_date, 'd/m/Y') : 'On Progress'; 

        wo.start_date = dates.format(wo.start_date);
        wo.expire_date = dates.format(wo.expire_date);
        wo.created_at = dates.format(wo.created_at);   
        
        woTpl.push(String.format(`@require('tpl/list_wo')`,wo));
        })          
        
        dataTpl.woTpl = woTpl.join(' ');    
        dataTpl.attachment = detailTpl;
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
