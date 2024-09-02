@extends(config('site.template').'extjs')

@section('head')
    <link rel="stylesheet" href="{{ asset('plugins/adminlte3/plugins/fontawesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/adminlte3/plugins/Ionicons/css/ionicons.min.css') }}">
@endsection

@section('script')

    @require('extends.formIcons')
    @require('extends.grids')
    @require('extends.forms')
    @require('extends.roles')

    <script>
        Ext.require([
            'Ext.form.*', 'Ext.layout.container.Column',
            'Ext.fx.target.Element', 'Ext.window.MessageBox', 'Ext.dd.*',
            'Ext.data.*','Ext.grid.*','Ext.tree.*','Ext.ux.CheckColumn',
            'Ext.util.*','Ext.data.*','Ext.XTemplate'
        ]);

        var dataTypes    =  @json($types);
        var dataRoutes   =  @json($routes);
        var dataRoles    =  @json($roles);

        var grids        = new Grids();
        var forms        = new Forms();
        var roles        = new Roles();
        var formIcons    = new FormIcons();

        Ext.onReady(function(){
            Ext.tip.QuickTipManager.init();

            roles.init();
            grids.init();
            forms.init();

            Ext.create('Ext.container.Viewport', {
                id: 'main-container',
                layout: 'border',
                bodyPadding: '0',
                border: false,
                items: [grids.grid, roles.grid]
            });

        });
    </script>

@endsection
