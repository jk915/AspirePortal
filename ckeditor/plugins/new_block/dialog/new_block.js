( function()
{	
	var new_block = function(editor){
		return {
			title : 'Add new block',
			minWidth : 200,
			minHeight : 200,
			buttons: 
				[
				 	{
					    type:'button',
					    id:'someButtonID', /* note: this is not the CSS ID attribute! */
					    label: 'Button',
					    onClick: function(){
					       //action on clicking the button
				 		}
				 	},
				 	CKEDITOR.dialog.okButton, 
				 	CKEDITOR.dialog.cancelButton 
				 ],
				 onOk: function(){},
				 onLoad: function(){},
				 onShow: function(){},
				 onHide: function(){},
				 onCancel: function(){},
				 resizable: CKEDITOR.DIALOG_RESIZE_NONE/* none,width,height or both  */,
				 contents: 
					 /*content definition, basically the UI of the dialog*/
			     [
			      	{ /* content 1 */
			      		id: 'panel1',  /* not CSS ID attribute! */
			      		label: 'Panel1',
			      		accessKey: 'P1',
			      		elements:
			        	[ 
			        	 	/*element 1 */
			        	 	{
			                    type : 'hbox',
			                    widths : [ '100px', '100px', '100px' ],
			                    children :
			                    	[
			                    	 	{
			                    	 		type:'html',
			                    	 		html:'<div>Cell1</div>'
			                    	 	},
			                    	 	
			                    	 	{
			                    	 		type:'html',
			                    	 		html:'<div>Cell2</div>'
			                    	 	},
			                    	 	
			                    	 	{
			                    	 		type: 'vbox',
			                    	 		children:
			                    	 			[
			                    	 		 		{
			                    	 		 			type:'html',
			                    	 		 			html:'<div>Cell3</div>'
			                    	 		 		},
			                    	 		 		
			                    	 		 		{
			                    	 		 			type:'html',
			                    	 		 			html:'<div>Cell4</div>'
			                    	 		 		}
			                    	 		 	]
			                    	 	}
			                    	 ]
			        	 	} /* end of element 1 */
			            ]
			      	}, /* end of content 1 */
			      	
			      	{
			      		id:'page2',
			      		label:'Page2', 
			      		accessKey: 'Q',
			      		elements:
			      			[
			      			 	/* element*/
			      			]
			      	}
			      ]
		};
	};
	
	CKEDITOR.dialog.add('new_block', function(editor) {
		return new_block(editor);
	});
		
})();