@extends(config('site.template').'extjs')

@section('script')
    @require('details.detail')
    @require('extends.grid')
    @require('extends.form')
    
    <script>
        Ext.require(['Ext.ux.form.SearchField']);
        var vendor = @json($vendors);
        var activity = @json($activities);
        var service = @json($services); 
        var client = @json($clients);   
        var site = @json($sites);     
        var grids = new Grids();
        var forms = new Forms();

        Ext.onReady(function(){
            Ext.tip.QuickTipManager.init();

            grids.init();
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

            grids.storeLoad();
        });

    </script>

@endsection







