<script>
	var Forms = function(){
		var me = this;

		me.init = function(){
			me.form = Ext.widget('form', {
				bodyPadding 	: 10,
				autoHeight  	: true,
				border      	: false,
				autoScroll  	: true,
				defaultType 	: 'textfield',
				fieldDefaults 	: { labelAlign: 'top',  labelWidth: 90,  msgTarget: 'side', anchor: '100%', allowBlank: false},
				items: [
					{xtype: 'hidden', name: '_method'},
					{xtype: 'hidden', name: '_token', value : '{{csrf_token()}}' },
					{xtype: 'hidden', name: 'id', allowBlank:true},

					{
	                    xtype: 'fieldcontainer', defaultType: 'textfield', layout: {type: 'hbox', align: 'stretch'},
	                    items: [
	                        {fieldLabel: 'Name', name: 'name', flex: 1},
	                        {fieldLabel: 'Alias', name: 'alias', width: 100, margin : '0 10', maxLength: 10},
	                        {
	                            xtype: 'button', text: 'COLOR', id:'field-button-color', margin : '21 0 0 0',
	                            menu: {
	                                items: [
	                                	{xtype: 'hidden', name: 'color', id: 'field-color'},
	                                    {
	                                        xtype: 'colorpicker',
	                                        handler: function(obj, val){
	                                            Ext.getCmp('field-button-color').getEl().dom.style.background = '#'+val;
	                                            Ext.getCmp('field-color').setValue(val);
	                                        }
	                                    }
	                                ]
	                            }
	                        }
	                    ]
	                },
					{xtype: 'textarea', name: 'description', fieldLabel: 'Description', allowBlank: true}
				],

				buttons:[
				 	@if($user->hasRoute(['auth.role.create', 'auth.role.update']))
					{text: 'Save', iconCls: 'icon-save-bright', handler: me.save},
					{text: 'Close', iconCls: 'icon-close', handler: me.close}
					@endif
				]

			});

			me.win = Ext.widget('window', {
				title 		 : 'GROUP',
				closeAction  : 'hide',
				width 		 : 480,
				resizable 	 : false,
				modal 		 : true,
				autoScroll 	 : true,
				autoHeight 	 : true,
				items 		 : me.form
			});
		};

		me.show =  function(){
        me.win.show();
    };

	me.close = function(){
		me.win.hide();
	};

	me.create = function(){
	    var form = me.form.getForm();
	    me.show();
	    me.win.setTitle('Create Group');
	    form.url = '{{ route('auth.role.create')}}';
	    form.reset();
	    form.findField('_method').setValue('POST');
	    Ext.getCmp('field-button-color').getEl().dom.style.background = '#DDDDDD';
    }

	me.edit = function(){
        var rec = gridRoles.getRec(true);
        if(rec){
            var form = me.form.getForm();
            me.show();
            me.win.setTitle('Update ('+rec.name+')');
            form.url = '{{ route('auth.role.update', '') }}/' + rec.id;
            form.reset();
            form.findField('_method').setValue('PUT');
            form.findField('name').setValue(rec.name);
            form.findField('alias').setValue(rec.alias);
            form.findField('color').setValue(rec.color);
            form.findField('description').setValue(rec.description);

            Ext.getCmp('field-button-color').getEl().dom.style.background = '#'+rec.color
        }
        else Ext.msg.warning('Please select data!');
    }

    me.save = function(){
        var form = me.form.getForm();
        if(form.isValid()){
            form.submit({
                waitMsg: 'Proses',
                failure: Ext.msg.failed,
                success: function(){
                    gridRoles.storeLoad();
                    me.close();
                }
            });
        }
        else {Ext.msg.warning('Please check your input data!');}
    }

}
</script>
