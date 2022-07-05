@extends(config('site.template').'extjs')

@section('script')
 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
   integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
   crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
   integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
   crossorigin=""></script>
    @require('details.detail')
    @require('extends.grid')
    @require('extends.form')
    @require('extends.formDetail')

    <script>
        Ext.require(['Ext.ux.form.SearchField','Ext.ux.GMapPanel']);
        var activity = @json($activities);
        var service = @json($services);
        var vendors = @json($vendors);
        var statusAction = @json($status);
        var clients = @json($clients);
        var grids = new Grids();
        var forms = new Forms();
        var formdetail = new FormDetail();

        Ext.onReady(function(){
            Ext.tip.QuickTipManager.init();

            grids.init();

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







