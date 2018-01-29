<?php

class BdPaypalButton{

	function __construct()
	{
		define('BD_PAYPAL_URL', WOLF_THEME_URL . '/includes/old-version/bd-paypal/' );
		define('BD_PAYPAL_PRODUCTION', true);

		$this->widget();

		add_action('admin_init', array($this, 'admin_init'));
		add_action('admin_init', array($this, 'mce_init'));
		add_action('admin_menu',  array($this, 'add_menu'));
		add_action('admin_print_styles', array($this, 'admin_styles'));
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));

		add_shortcode('bd_paypal', array($this, 'shortcode_buy'));
		add_shortcode('bd_paypal_checkout', array($this, 'shortcode_checkout'));
	}

	// --------------------------------------------------------------------------

	/**
	* Add Contextual Menu
	*
	*/
	function add_menu(){

		add_menu_page('Paypal', 'Paypal', 'administrator', basename(__FILE__), array($this, 'paypal_settings') );

	}

	// --------------------------------------------------------------------------


	/**
	* Add Settings
	*
	*/
	function admin_init()
	{
		register_setting( 'bd-paypal', 'bd_paypal_settings', array($this, 'settings_validate') );
		add_settings_section( 'bd-paypal', '', array($this, 'section_intro'), 'bd-paypal' );
		add_settings_field( 'email', __( 'Your paypal seller email or merchant ID', 'wolf' ), array($this, 'setting_email'), 'bd-paypal', 'bd-paypal' );
		add_settings_field( 'currency', __( 'Currency code', 'wolf' ), array($this, 'setting_currency'), 'bd-paypal', 'bd-paypal' );
		add_settings_field( 'confirm', __( 'Send a confirmation email to the buyer after a sale.', 'wolf' ), array($this, 'setting_confirm'), 'bd-paypal', 'bd-paypal' );
		add_settings_field( 'instructions', __( 'Infos', 'wolf' ), array($this, 'setting_instructions'), 'bd-paypal', 'bd-paypal' );
	}

	// --------------------------------------------------------------------------

	/**
	* Intro section used for debug
	*
	*/
	function section_intro()
	{
		// echo "<pre>";
		// print_r(get_option('bd_paypal_settings'));
		// echo "</pre>";
	}

	// --------------------------------------------------------------------------

	/**
	* Seller Email
	*
	*/
	function setting_email()
	{
		echo '<input name="bd_paypal_settings[email]" type="text" value="' . $this->get_paypal_option('email') . '">';
	}

	// --------------------------------------------------------------------------

	/**
	* Confirmation
	*
	*/
	function setting_confirm()
	{
		echo '<input type="hidden" name="bd_paypal_settings[confirm]" value="false" />
		<label><input type="checkbox" name="bd_paypal_settings[confirm]" value="true"'. (($this->get_paypal_option('confirm') == 'true') ? ' checked="checked"' : '') .' /></label><br>';
	}

	// --------------------------------------------------------------------------

	/**
	*  Instructions
	*
	*/
	function setting_instructions()
	{
		?>
		<p><?php printf( __('You can find the confirmation email template in the <br><code>%s</code> file', 'wolf'),  str_replace( site_url(), '', BD_PAYPAL_URL . 'email_template.html' ) ); ?></p>
		<?php
	}

	// --------------------------------------------------------------------------

	/**
	* Seller Email
	*
	*/
	function setting_currency()
	{
		$currency = array(
			'USD',
			'EUR',
			'CHF',
			'GBP',
			'AUD',
			'CAD',
			'BRL',
			'CZK',
			'DKK',
			'HKD',
			'HUF',
			'JPY',
			'MYR',
			'MXN',
			'NOK',
			'NZD',
			'PHP',
			'PLN',
			'SGD',
			'SEK',
			'TWD',
			'THB'

		);
		?>
		<select name="bd_paypal_settings[currency]">
			<?php foreach($currency as $c): ?>
				<option value="<?php echo $c; ?>" <?php if($this->get_paypal_option('currency') == $c ) echo 'selected="selected"'; ?>><?php echo $c; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	// --------------------------------------------------------------------------

	/**
	* Validate data
	*
	*/
	function settings_validate($input)
	{
		$input['currency'] = esc_attr( $input['currency'] );
		return $input;
	}


	// --------------------------------------------------------------------------

	
	/**
	* Get Option
	*
	*/
	function get_paypal_option($value = null)
	{
	            global $options;
	            
		$bd_paypal_settings = get_option('bd_paypal_settings');
		
		if( isset($bd_paypal_settings[$value]) )
			return $bd_paypal_settings[$value];
		else
			return null;
	}

	// --------------------------------------------------------------------------

	/**
	* Settings Form
	*
	*/
	function paypal_settings()
	{
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2><?php _e('Paypal Settings', 'wolf'); ?></h2>
			<form action="options.php" method="post">
				<?php settings_fields( 'bd-paypal' ); ?>
				<?php do_settings_sections( 'bd-paypal' ); ?>
				<p class="submit"><input name="save" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wolf' ); ?>" /></p>
			</form>
		</div>
		<?php
	}

	// --------------------------------------------------------------------------

	/**
	* Registers TinyMCE rich editor buttons
	*
	*/
	function mce_init()
	{
		
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;
	
		if ( get_user_option('rich_editing') == 'true' )
		{
			add_filter( 'mce_external_plugins', array($this, 'add_rich_plugins') );
			add_filter( 'mce_buttons', array($this, 'register_rich_buttons') );
		}
	}

	// --------------------------------------------------------------------------
	
	/**
	* Defines TinyMCE rich editor js plugin
	*
	*/
	function add_rich_plugins( $plugin_array ){
		$plugin_array['bd_paypal'] = BD_PAYPAL_URL . 'paypal.js';
		return $plugin_array;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	* Adds TinyMCE rich editor buttons
	*
	*/
	function register_rich_buttons( $buttons ){
		array_push( $buttons, "|", 'bd_paypal');
		return $buttons;
	}

	// --------------------------------------------------------------------------
	
	/**
	* Enqueue Popup Styles
	*
	*/
	function admin_styles(){
		wp_enqueue_style( 'bd-popup', BD_PAYPAL_URL . 'popup.css', false, '1.0', 'all' );		
	}

	// --------------------------------------------------------------------------

	/**
	* Enqueue Admin Script
	*
	*/
	function admin_scripts(){
		wp_localize_script( 'jquery', 'bd_paypal', array('plugin_folder' => BD_PAYPAL_URL) );
	}

	// --------------------------------------------------------------------------

	/**
	* Include Widget
	*
	*/
	function widget(){
		include_once 'bd-paypal-checkout-widget.php';
	}


	// --------------------------------------------------------------------------

	function do_button( $text , $product_name, $amount, $type, $shipping, $tax, $opt1_name, $opt1, $opt2_name, $opt2 )
	{
		$production = BD_PAYPAL_PRODUCTION;
		$email = $this->get_paypal_option('email');
		$currency = $this->get_paypal_option('currency');
		
		$root_url = BD_PAYPAL_URL;
		$success = $root_url .'paypal_success.php';
		$cancel = $root_url .'paypal_cancel.php';
		$ipn = $root_url .'ipn.php';
		

		$cmd = $type;
		

		if( !$production )	
			$urlPaypal = "https://www.sandbox.paypal.com/cgi-bin/webscr"; 
		else												
			$urlPaypal = "https://www.paypal.com/cgi-bin/webscr"; 

		/* Paypal Button
		-----------------------------*/

		$output = '';
		$output .= '<form action="'.$urlPaypal.'" method="post" id="bd-paypal">';
		$output .= '<input name="amount" type="hidden" value="'.$amount.'" />
		<input name="currency_code" type="hidden" value="'.$currency.'" />
		<input name="shipping" type="hidden" value="'.$shipping.'" />
		<input name="tax" type="hidden" value="'.$tax.'" />
		<input name="return" type="hidden" value="'.$success.'" />
		<input name="cancel_return" type="hidden" value="'.$cancel.'" />
		<input name="notify_url" type="hidden" value="'.$ipn.'" />';
		// OPTION 0 : 
		 if( $opt1_name && $opt1 ){
			$output .= $opt1_name.' <input name="on0" type="hidden" value="'.$opt1_name.'" />';
			$opt1 = str_replace(  array(', ', ' ,', ' , '), array(','), $opt1  );
			if( stripos($opt1, ",") !== false ){
				$output .='<select name="os0">';
					$tbl_os = explode(",", $opt1);
					foreach( $tbl_os as $k){ 
						$output .='<option value="'.$k.'">'.$k.'</option>';
					} 
				$output .='</select><div style="height:5px"></div>';
			}else{
				$output .='<input name="os0" type="hidden" value="'.$opt1.'" />';
			} 
		}
		// OPTION 1 : 
		 if( $opt2_name && $opt2 ){
			$output .= $opt2_name.' <input name="on1" type="hidden" value="'.$opt2_name.'" />';
			$opt2 = str_replace(  array(', ', ' ,', ' , '), array(','), $opt2  );
			if( stripos($opt2, ",") !== false ){
				$output .='<select name="os1">';
					$tbl_os = explode(",", $opt2);
					foreach( $tbl_os as $k){ 
						$output .='<option value="'.$k.'">'.$k.'</option>';
					} 
				$output .='</select><br />';
			}else{
				$output .='<input name="os1" type="hidden" value="'.$opt2.'" />';
			} 
		}


		$output .= '<input name="business" type="hidden" value="'.$email.'" />
		<input name="item_name" type="hidden" value="'.$product_name.'" />
		<input name="no_note" type="hidden" value="0" />
		<input name="bn" type="hidden" value="" />
		<input name="cmd" type="hidden" value="'.$cmd.'" />';

		$output .= '<input name="custom" type="hidden" value="item_id=0" />';
		
		if($cmd = '_cart')
			$output .= '<input name="add" type="hidden" value="1" />';

		$output .='<input alt="Make payments with PayPal" name="submit"  value="'.$text.'" type="submit" />
		<img src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" border="0" alt="" width="1" height="1" />';
		$output .='</form>';

		if( $currency && $email ){

			if( $amount == null || $product_name == null ){

				if(is_user_logged_in())
					return __('You must set a price and a name for your product', 'wolf');
				
			}else{

				return $output;

			}

		}else{

			if(is_user_logged_in())
				return __('You must set your seller email and your currency in the paypal options.', 'wolf');

		}

	}

	// --------------------------------------------------------------------------

	function do_checkout($text = 'Checkout')
	{
		$production = BD_PAYPAL_PRODUCTION;
		$email = $this->get_paypal_option('email');
		if( !$production )	
			$urlPaypal = "https://www.sandbox.paypal.com/cgi-bin/webscr"; 
		else												
			$urlPaypal = "https://www.paypal.com/cgi-bin/webscr"; 

		$success = BD_PAYPAL_URL .'paypal_success.php';
		$cancel = BD_PAYPAL_URL .'paypal_cancel.php';
		$ipn = BD_PAYPAL_URL .'ipn.php';

		$output = '<div style="text-align:center">';
		$output .= '<form action="'.$urlPaypal.'" method="post" id="bd-paypal">';
		$output .='<input type="hidden" name="cmd" value="_cart">
			<input name="return" type="hidden" value="'.$success.'" />
			<input name="cancel_return" type="hidden" value="'.$cancel.'" />
			<input name="notify_url" type="hidden" value="'.$ipn.'" />
			<input type="hidden" name="business" value="'.$email.'">
			<input type="hidden" name="display" value="1">';

		$output .='<input alt="Make payments with PayPal" name="submit" class="bd-checkout"  type="submit" value="' . $text . '" />
		<img src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" border="0" alt="" width="1" height="1" />';
		$output .='</form></div>';

		return $output;
	}


	// --------------------------------------------------------------------------

	function shortcode_buy( $atts = null )
	{
		extract( shortcode_atts(array(
			'text' => 'Buy Now',
			'item_name' => 'product',
			'amount' => 'amount',
			'type' => '_xclick',
			'shipping' => '0.00',
			'tax' => '0.00',
			'opt1_name' => '',
			'opt1' => '',
			'opt2_name' => '',
			'opt2' => '',

		), $atts ));

		return $this->do_button($text, $item_name, $amount, $type, $shipping, $tax, $opt1_name, $opt1, $opt2_name, $opt2);
	}

	// --------------------------------------------------------------------------

	function shortcode_checkout( $atts = null )
	{
		extract( shortcode_atts(array(
			'text' => 'Checkout'
		), $atts ));
		return $this->do_checkout($text);
	}


} // End class
global $bd_paypal_button;
$bd_paypal_button = new BdPaypalButton;

function bd_paypal($text = 'Add to Cart', $product_name = null, $amount = null, $type = '_xclick',  $shipping = '0.00', $tax = '0.00', $opt1_name = null, $opt1= null, $opt2_name = null, $opt2= null){
	global $bd_paypal_button;
	
	echo $bd_paypal_button->do_button($text, $product_name, $amount, $type, $shipping, $tax, $opt1_name, $opt1, $opt2_name, $opt2);
}

function bd_checkout( $text = 'Checkout' ){
	global $bd_paypal_button;
	echo $bd_paypal_button->do_checkout($text);
}
?>
