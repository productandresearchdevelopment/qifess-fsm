<style>
    .vendor-detail{ margin: 10px; }
    .vendor-detail .header-title{font-size: 15px; font-weight: 700; padding: 0px 0px}
    .vendor-detail .container{font-size: 13px; border: 1px solid #ddd; background: #fff; border-radius: 5px}
    .vendor-detail .info-container{margin: 10px 0px; padding: 15px 15px;}
    .vendor-detail .container span{color: #666; font-size: 10px; display: block; font-weight: 700; padding-bottom: 2px}
    .vendor-detail .container .info{color: #666; font-size: 10px;}
    .vendor-detail .box-color{font-size: 10px; border-radius: 2px; padding: 0px 5px; color: #fff; background: #333; display: inline-block}
    .vendor-detail .container .circle-photo {width: 40px; height: 40px; border: 1px solid #eee; border-radius: 50%; margin: 5px 15px 5px 0px;}
</style>

<script>

    var detailVendor = function (data, dom) {
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
        wo.client = find(client, wo.client_id);        
        wo.description = wo.description;
        wo.close_date = wo.close_date ? dates.format(wo.close_date, 'd/m/Y') : 'On Progress'; 

        wo.start_date = dates.format(wo.start_date);
        wo.expire_date = dates.format(wo.expire_date);
        wo.created_at = dates.format(wo.created_at);   
        
        woTpl.push(String.format(`@require('tpl/list_wo')`,wo));
        })

        dataTpl.files.forEach(function (val) {
        if(val && val!="") {
                val.target = '';
                switch (val.type) {
                    case 'image':
                        val.thumbUrl = '{{ route("upload.file") }}/' + val.id;
                        val.target = 'target="_blank"';
                        break;
                    case 'application': {
                                         if(val.extension=="pdf")
                                            val.thumbUrl = '{{asset("images/filetype/pdf.png")}}';
                                         else val.thumbUrl = '{{asset("images/filetype/file.png")}}';
                                       } break;
                    default: val.thumbUrl = '{{asset("images/filetype/file.png")}}';
                }
                let tpl = `<a href="{{ route("upload.file") }}/{id}" style="text-decoration:none" {target}>
                                <img style="width: 75px; height: 75px" src="{thumbUrl}">
                           </a>`;
                detailTpl += String.format(tpl, val);
        }
  
        
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
