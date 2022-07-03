@extends(config('site.template').'extjs')

@section('script')
    <script src="{{ asset('plugins/gum/uwa/uwa.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/gum/uwa/uwa.css') }}"/>
    @require('extends.grid')
    <script>
        Ext.require(['Ext.ux.form.SearchField','Ext.ux.form.field.MonthField']);
        var grids = new Grids();

        Ext.onReady(function(){
            Ext.tip.QuickTipManager.init();

            grids.init();
            Uwa.ready(function () {
                Ext.create('Ext.container.Viewport', {
                    layout: 'border',
                    padding: 5,
                    border: false,
                    items: [grids.grid]
                });
            });

            grids.storeLoad();
        });

    </script>

@endsection







