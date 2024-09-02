@extends(config('site.template').'extjs')

@section('script')
    @require('extends.forms')
    @require('extends.roles')
    @require('extends.modules')

    <script>
        Ext.require(['Ext.util.*']);

        var forms 		= new Forms();
        var gridRoles 	= new GridRoles();
        var gridModules = new GridModules();

        Ext.onReady(function(){
            Ext.tip.QuickTipManager.init();

            forms.init();
            gridRoles.init();
            gridModules.init();

            Ext.create('Ext.container.Viewport', {
                id 		: 'main-container',
                layout	: 'border',
                padding	: '5',
                border	: true,
                items	: [gridModules.grid, gridRoles.grid]
            });

            gridRoles.storeLoad();
        });
    </script>
@endsection
