Ext.define('Ext.ux.FitToParent',
{
    alias: 'fittoparent',
    extend: 'Object',
    fitWidth: true,
    fitHeight: true,
    offsets: [0, 0],
    
    constructor: function(config){
        if(typeof config == 'string' || config instanceof String) config = Ext.get(config);
        config = config || {};
        if (config.tagName || config.dom || Ext.isString(config)){
            config = {parent: config};
        }
        Ext.apply(this, config);
    },

    init: function(c) 
    {
        this.component = c;
        c.on('render', function(c)
        {
            this.parent = Ext.get(this.parent || c.getPositionEl().dom.parentNode);
            if (c.doLayout)
            {
                c.monitorResize = true;
                c.doLayout = Ext.Function.createInterceptor(c.doLayout, this.fitSize, this);
            }
            this.fitSize();
            Ext.EventManager.onWindowResize(this.fitSize, this);
        }, this, {single: true});
    },

    fitSize: function() 
    {
        var pos = this.component.getPosition(true),
        size = this.parent.getViewSize();
        this.component.setSize(
        this.fitWidth ? size.width - pos[0] - this.offsets[0] : undefined,
        this.fitHeight ? size.height - pos[1] - this.offsets[1] : undefined);
    }
});