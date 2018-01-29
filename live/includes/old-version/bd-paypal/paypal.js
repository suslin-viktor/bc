(function() {

	tinymce.PluginManager.requireLangPack('bd_paypal');
	tinymce.create('tinymce.plugins.buttonPlugin', {
		init : function(ed, url) {

		// Register commands
		ed.addCommand('mcebuttonPaypal', function() {
			ed.windowManager.open({
				file : url + '/paypal.php', // file that contains HTML for our modal window
				width : 500 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
				height : 600 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
				inline : 1
			}, {
				plugin_url : url
			});
		});

			// Register buttons
			ed.addButton('bd_paypal', {title : 'Paypal button', cmd : 'mcebuttonPaypal', image: url + '/paypal.png' });

		},
	       
	       
		createControl : function(n, cm){
			return null;
		},
		getInfo : function(){
		return {
			longname: 'BdPaypal Shortcode buttons',
			author: 'BrutalDesign',
			version: "1.0"
		};

		}


	});
     
	// Register plugin
	// first parameter is the button ID and must match ID elsewhere
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('bd_paypal', tinymce.plugins.buttonPlugin);

})();