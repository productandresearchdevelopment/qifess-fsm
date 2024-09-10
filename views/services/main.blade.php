@extends(config('site.template') . 'extjs')

@section('script')
  @require('extends.grid')
  @require('extends.form')

  <script>
    Ext.require(['Ext.ux.form.SearchField']);
    var grids = new Grids();
    var forms = new Forms();

    Ext.onReady(function() {
      Ext.tip.QuickTipManager.init();

      grids.init();
      forms.init();

      Ext.create('Ext.container.Viewport', {
        layout: 'border',
        bodyPadding: 0,
        border: false,
        items: [
          grids.grid,
        ]
      });

      grids.storeLoad();
    });
  </script>
@endsection
