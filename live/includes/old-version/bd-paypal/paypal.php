<?php
$absolute_path = __FILE__;
$path_to_file = explode( 'wp-content', $absolute_path );

if( count($path_to_file) > 1){
	/*got wp-content dir*/
	$path_to_wp = $path_to_file[0];

}else{
	/* dev environement */
	$path_to_file = explode( 'content', $absolute_path );
	$path_to_wp = $path_to_file[0] .'/wp';
}

// Access WordPress
require_once( $path_to_wp . '/wp-load.php' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php _e('Insert a Paypal Button', 'wolf'); ?></title>
<link rel='stylesheet' id='colors-css'  href='<?php echo admin_url('css/wp-admin.css'); ?>' type='text/css' media='all' />
<link rel='stylesheet' id='colors-css'  href='<?php echo admin_url('css/color-fresh.css'); ?>' type='text/css' media='all' />
<link rel="stylesheet" href="popup.css">
<script type="text/javascript" src="<?php echo includes_url( 'js/jquery/jquery.js' ); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo includes_url( 'js/tinymce/tiny_mce_popup.js' ); ?>"></script>
<script type="text/javascript">
jQuery(function($){
 	
 	
 	$("#bd-popup").on('click', '#add-panel', function() {
 		if($('.box').length>1){
 			alert('Two options maximum, e.g : size & color');
 			return false;
 		}
 		if(($('.box').length==1) && $('.box').is(':hidden')){
 			$('.box').show();
 		}else if(($('.box').length==1) && $('.box').is(':visible')){
 			$('.box').clone().appendTo('#newfields').val('');
 			
 		}else if($('.box').length>1){
 			$('.box:last-child').clone().appendTo('#newfields');
 		}
 		

 		$('.box:last-child').find('.options-name').val('');
 		$('.box:last-child').find('.options').val('');
 		return false;
 	});

 	$("#bd-popup").on('click', '.close', function(){
 		box = $(this).parent();
  		if($('.box').length>1){
  			box.remove();
  		}else{
  			box.hide();
  		}
 		return false;
 	});
	
 });


var ButtonDialog = {
	local_ed : 'ed',
	init : function(ed) {
		ButtonDialog.local_ed = ed;
		tinyMCEPopup.resizeToInnerSize();
	},
	insert : function insertButton(ed) {
	 
		// Try and remove existing style / blockquote
		tinyMCEPopup.execCommand('mceRemoveNode', false, null);
		 
		// set up variables to contain our input values
		var text = jQuery('#paypal-text').val();
		var price = jQuery('#paypal-price').val();
		var name = jQuery('#paypal-name').val();
		var type = jQuery('#paypal-type').val();
		var shipping = jQuery('#paypal-shipping').val();
		var tax = jQuery('#paypal-tax').val();
		var os0 = jQuery('#paypal-options0').val();
		var on0 = jQuery('#paypal-options0-name').val();
		format = name.replace(/ /g,'%');
		// var align = jQuery('#paypal-align').val();
 
		var output = '';
		
		// setup the output of our shortcode
		output = '[bd_paypal ';
			output += ' text="' + text + '"';
			output += ' item_name="' + name + '"';
			output += ' amount="' + price + '"';
			output += ' shipping="' +shipping + '"';
			output += ' tax="' +tax + '"';
			if(os0!=''){
				i=0;
				jQuery('.box').each(function(){
				i++;	
				optitle = jQuery(this).find('.options-name').val();
				opt = jQuery(this).find('.options').val();
				output += ' opt'+i+'_name="' +optitle+ '" opt'+i+'="' +opt + '"';
			 });
			}
			// output += 'align=' +align + '';
			output += ']';

		//tinyMCEPopup.execCommand('mceReplaceContent', false, output);
		 ed.selection.setContent (output);
		// Return
		tinyMCEPopup.close();
	}
};
tinyMCEPopup.onInit.add(ButtonDialog.init, ButtonDialog);
 
</script>

</head>
<body>
<div id="bd-popup-head"><strong><?php _e('Insert a Paypal Button', 'wolf'); ?></strong></div>
	<div id="bd-popup">
		<form action="#" method="get">
			<div class="bd-field">
				<label><?php _e('Button text', 'wolf'); ?></label>
				<input type="text" name="paypal-text" value="" id="paypal-text" />
			</div>
			<div class="bd-field">
				<label><?php _e('Item name', 'wolf'); ?></label>
				<input type="text" name="paypal-name" value="" id="paypal-name" />
			</div>
			<div class="bd-field">
				<label><?php _e('Price  (e.g : 15)', 'wolf'); ?></label>
				<input type="text" name="paypal-price" value="" id="paypal-price" />
			</div>
			<div class="bd-field">
				<fieldset class="bd-tab-field">
					<label><?php _e('Type', 'wolf'); ?></label>
					<select name="paypal-type" id="paypal-type">
						<option value="_xclick"><?php _e('Buy Now', 'wolf'); ?></option>
						<option value="_cart"><?php _e('Add to cart', 'wolf'); ?></option>
					</select>
				</fieldset>
			</div>
			<div class="bd-field">
				<label><?php _e('Shipping', 'wolf'); ?></label>
				<input type="text" name="paypal-shipping" value="0.00" id="paypal-shipping" />
			</div>
			<div class="bd-field">
				<label><?php _e('Tax', 'wolf'); ?></label>
				<input type="text" name="paypal-tax" value="0.00" id="paypal-tax" />
			</div>

			<div class="box" style=" display:none">
				
				<fieldset class="bd-tab-field">
					<label><?php _e('Option name (e.g : color)', 'wolf'); ?></label>
					<input type="text" name="panel-title" class="options-name" value="" id="paypal-options0">
				</fieldset>
			
				<fieldset>
					<label><?php _e('Options (separate by a comma e.g: red,black,yellow)', 'wolf'); ?></label>
					<input type="text" name="panel-title" class="options" value="" id="panel-title0">
				</fieldset>
				<a href="#" class="close"><?php _e('remove', 'wolf'); ?></a>	
				<hr>					
			</div>
			<div id="newfields"></div>
			
			
			<p>
				<a href="#" id="add-panel" class="add"><?php _e('Add options', 'wolf'); ?></a>
			</p>

			<p>	
				<a href="javascript:ButtonDialog.insert(ButtonDialog.local_ed)" id="insert" class="button-primary"><?php _e('Insert', 'wolf'); ?></a>
			</p>
		</form>
	</div>
</body>
</html>