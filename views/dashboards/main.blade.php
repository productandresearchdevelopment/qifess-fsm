<link href="{{ asset('plugins/architechui/architectui-html-free/main.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset('plugins/architechui/architectui-html-free/assets/scripts/main.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/fussionchart/js/fusioncharts.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/fussionchart/js/themes/fusioncharts.theme.ocean.js') }}"></script>
<style>
    .fixed-header .app-main {padding-top: 0px;}
    .fixed-sidebar .app-main .app-main__outer {padding-left: 0px;}
</style>

@include('headers.head')
<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
    <div class="app-main">
        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="app-page-title">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div class="page-title-icon">
                                <i class="pe-7s-display1 icon-gradient bg-mean-fruit">
                                </i>
                            </div>
                            <div>Analytics Dashboard
                                <div class="page-title-subheading">Dashboard, Monitoring Field Service System <br> <b>Captured At, {{ date('d F Y H:i') }}</b></div>
                            </div>
                        </div>

                    </div>
                </div>

                @require('extends.top')
                @require('extends.vendor_chart')
                @require('extends.total_db')
                @require('extends.last_wo')

            </div>
        </div>
    </div>
</div>
