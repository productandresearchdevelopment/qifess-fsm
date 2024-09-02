@extends(config('site.template').'extjs')

@section('script')
    <script src="{{ asset('plugins/gum/uwa/uwa.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/gum/uwa/uwa.css') }}"/>
    @require('details.detail')
    @require('extends.grid')
    @require('extends.form')
    @require('extends.formDetail')

    <script>
        Ext.require(['Ext.ux.form.SearchField']);
        var vendor = @json($vendors);
        var activity = @json($activity);
        var service = @json($service);
        var grids = new Grids();
        var forms = new Forms();
        var formdetail = new FormDetail();

        Ext.onReady(function(){
            Ext.tip.QuickTipManager.init();
            Ext.override(Ext.form.Field, {
                setReadOnly: function(readOnly) {
                    if (readOnly == this.readOnly) {
                        return;
                    }
                    this.readOnly = readOnly;

                    if (readOnly) {
                        this.el.dom.setAttribute('readOnly', true);
                    } else {
                        this.el.dom.removeAttribute('readOnly');
                    }
                }
            });

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







