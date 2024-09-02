@extends(config('site.template').'extjs')

@section('script')

    @require('extends.grid')
    @require('extends.form')
    @require('extends.form-role')

    <script>
        Ext.require(['Ext.ux.form.SearchField']);

        var roles = @json($roles);
        var vendors = @json($vendors);
        var clients = @json($clients);
        var owners = @json($owners);
        var activities = @json($activities);

        var grids = new Grids();
        var forms = new Forms();
        var formRoles = new FormsRole();

        Ext.onReady(function(){
            Ext.tip.QuickTipManager.init();

            grids.init();
            forms.init();
            formRoles.init();

            Ext.create('Ext.container.Viewport', {
                layout: 'border',
                bodyPadding: 0,
                border: false,
                items: [grids.grid]
            });

            grids.storeLoad();
        });

    </script>

@endsection





