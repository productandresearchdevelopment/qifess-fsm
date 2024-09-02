<style>
    .client-detail{ margin: 10px; }
    .client-detail .header-title{font-size: 15px; font-weight: 700; padding: 0px 0px}
    .client-detail .container{font-size: 13px; border: 1px solid #ddd; background: #fff; border-radius: 5px}
    .client-detail .info-container{margin: 10px 0px; padding: 15px 15px;}
    .client-detail .container span{color: #666; font-size: 10px; display: block; font-weight: 700; padding-bottom: 2px}
    .client-detail .container .info{color: #666; font-size: 12px;}
    .client-detail .box-color{font-size: 10px; border-radius: 2px; padding: 0px 5px; color: #fff; background: #333; display: inline-block}
    .client-detail .container .circle-photo {width: 150px; height: 150px; border-radius: 10px; padding: 5px}

</style>

<script>

    var detailPart = function (data, dom) {
        let dataTpl = JSON.parse(JSON.stringify(data));
        let detailTpl = "";
        let woTpl = [];
        let wo =[];
        let url="";

        if(dataTpl.files[0]){
        dataTpl.photo = dataTpl.files[0].id ? '{{ route('upload.file') }}/' + dataTpl.files[0].id : '{{asset('images/nopart.png')}}';
        dataTpl.url = '{{ route('upload.file') }}/' + dataTpl.files[0].id
        } else {
            dataTpl.photo = '{{asset('images/nopart.png')}}';
             dataTpl.url = '{{asset('images/nopart.png')}}';
        }
        if(dataTpl.wo){
        wo.no_wo = dataTpl.wo.no_wo
        wo.activity = find(activity, dataTpl.wo.activity_id);
        wo.service = find(service, dataTpl.wo.service_id);
        wo.site = find(site, dataTpl.wo.site_id);
        wo.description = dataTpl.wo.description;
        wo.end_date = dataTpl.wo.end_date ? dataTpl.wo.end_date : 'On Progress'; 

        wo.start_date = dates.format(dataTpl.wo.start_date);
        wo.expire_date = dates.format(dataTpl.wo.expire_date);
        wo.created_at = dates.format(dataTpl.wo.created_at);   
        
        woTpl.push(String.format(`@require('tpl/list_wo')`,wo));           
        
        dataTpl.woTpl = woTpl.join(' ');  
        }  
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
                $('.dropdown-detail-wo').click(function () {
                    let id = this.id.substr((this.id.length - 40),40);
                    $('#wo-detail-'+id).toggle("slideDown", function(){
                        if($(this).is(":visible")) rotate('#toggle-detailWo-'+id, 90);
                        else rotate('#toggle-detailWo-'+id, 0);
                    });
                });             
            });
        }
        return true;  
    }
</script>
