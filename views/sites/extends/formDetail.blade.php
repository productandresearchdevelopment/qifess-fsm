<script>
    var FormDetail = function(){
        let me = Ext.utils.windowForms(this);

        me.init = function(){
        let me = Ext.create("Ext.window.Window", {
                title: 'Detail WO',
                modal: true,
                html:'<iframe id="iframe-detailWo" style="height:100%; width:100%; border:0"></iframe>',
                width: 700,
                height: 500,
                padding: 5,
                bbar: ['->',
                    {
                        text: 'Close',margin:"0 30 10 0",
                        handler: function () { this.up('window').close(); }
                    }
                ],                
            }).show();
        };


    }

</script>
