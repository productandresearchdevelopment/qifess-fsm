@extends(config('site.template').'extjs')

@section('script')

    @require('extends.form')

    <script>
        Ext.require(['Ext.ux.form.SearchField', 'Ext.ux.DateTimeField']);

        var activities = @json($activities);
        var services = @json($services);
        var clients = @json($clients);
        var vendors = @json($vendors);
        var statusAction = @json($status);
        var owners = @json($owners);
        var slots = @json($slots);

        var forms = new Forms();

        Ext.onReady(function(){
            Ext.tip.QuickTipManager.init();
            forms.onBackButton = function () { window.location = '{{ route('wo') }}' }
            forms.onSave= function () { forms.clear(); }

            Uwa.ready(function () {
                forms.init();
                Ext.create('Ext.container.Viewport', {
                    layout: 'border',
                    bodyPadding: 0,
                    border: false,
                    items: [forms.form]
                });
            });
        });

    </script>

@endsection







