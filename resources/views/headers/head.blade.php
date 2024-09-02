<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('site.title') }}</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="stylesheet" type="text/css" href="{{ asset('css/icons.css') }}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}" />

<script src="{{ asset('js/utils/date-utilities.js') }}"></script>
<script src="{{ asset('js/utils/string-format.js') }}"></script>
<script src="{{ asset('js/utils/replace-all.js') }}"></script>
<script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/file-utils/fileutil.js') }}"></script>

<style>
    .overlay{z-index: 100000; background: rgba(215, 214, 214, 0.56); position: fixed; top: 0; left: 0; bottom: 0; right: 0; display: none;}
</style>

<script type="text/javascript">
    var grep = function (data, search, boolean){
        /*
            Untuk memfilter Data Array
            Example :
            - let filter = grep(data, {type: 5})
            - let filter = grep(data, {type: 5}, false)
        */

        if(data && search){
            boolean = boolean || true;
            return $.grep(data, function(e, index) {
                if(typeof search == 'object'){
                    for (var key in search) {
                        if(!boolean && (e[key] == search[key])) return true;
                        else if(boolean && (e[key] != search[key])) return false;
                        else if(boolean) return true;
                    }
                    return false;
                }
                else if(typeof e == 'object'){
                    if(e.id != undefined) return (e.id == search);
                    return false;
                }
                else return (e == search);
            });
        }
        return null;
    };

    var find = function (data, search, boolean){
        if(data && search){
            if(boolean == undefined) logic = true;
            let result = grep(data, search, boolean);
            if(result.length) return result[0];
        }
        return null;
    };

    var dates = new DateUtils('{{ date('Y-m-d H:i:s') }}');

    var isUndef = function(val){
        return (val === undefined) ? true : false;
    }

    var isNull = function(val){
        return (isUndef(val) || !val) ? true : false;
    }

    var mask = {
        show: function (text){
            $('#masking').show();
            if(text) $('#masking-text').html('text');
        },

        hide: function (){
            $('#masking').hide();
        }
    }

    $(document).ready(function() {
        if(window.top.trigger != undefined) {
            window.top.trigger();
        }
    });
</script>

<div id="masking" class="overlay">
    <table style="height: 100%; width: 100%">
        <tr>
            <td align="center">
                <div>
                    <img src="{{ asset('images/loading.gif') }}">
                    <div id="masking-text" style="font-size: 12px; font-weight: 600; color: #555555; padding: 5px;">LOADING</div>
                </div>
            </td>
        </tr>
    </table>
</div>





