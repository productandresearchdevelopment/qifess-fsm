@extends(config('site.template').'extjs')

@section('script')

    <link rel="stylesheet" href="{{ asset('plugins/adminlte3/plugins/fontawesome/css/font-awesome.min.css') }}">

    @require('details.detail')
    @require('extends.grid')
    @require('extends.form')
    @require('extends.form_part')

    <style>
        .wo-detail .action-container .indicator .icon{width: 11px; height: 11px;}
    </style>

    <script>
        Ext.require(['Ext.ux.form.SearchField', 'Ext.ux.DateTimeField', 'Ext.ux.form.field.MonthField']);

        var activities = @json($activities);
        var services = @json($services);
        var owners = @json($owners);
        var clients = @json($clients);
        var vendors = @json($vendors);
        var slots = @json($slots);
        var statusAction = @json($status);
        var user = @json($user);
        var technicianvendor = @json($technicianvendors);

        var grids = new Grids();
        var forms = new Forms();
        var formPart = new FormPart();

        Ext.onReady(function(){
            Ext.tip.QuickTipManager.init();
            forms.onBackButton = function () { forms.close() }
            forms.onSave= function () {
                grids.storeLoad();
                forms.close();
            }

            grids.init();
            formPart.init();

            Uwa.ready(function () {
                forms.init();
                Ext.create('Ext.container.Viewport', {
                    layout: 'border',
                    padding: 5,
                    border: false,
                    items: [
                        grids.grid,
                        {
                            xtype: 'panel',
                            id: 'panel-detail',
                            region: 'east',
                            title: 'VIEW',
                            split: true,
                            width: 350,
                            minWidth: 300,
                            border: true,
                            collapsible: true,
                            collapsed: true,
                            autoScroll: true,
                            html: `<div id="view-detail" style="position: absolute; top: 0; left:0; right:0; background: #FAFAFA">
                                        <div style="font-size: 11px; padding: 30px 0px; color: #ccc; text-align: center">
                                            NO DISPLAY DATA
                                        </div>
                                   </div>`
                        }
                    ]
                });
            });

            grids.storeLoad();


        });

    </script>

@endsection







