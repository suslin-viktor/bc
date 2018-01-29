<?php

if ( ! class_exists( 'Wolf_Theme_FlexSlider' ) ) {

class Wolf_Theme_FlexSlider {


	function __construct() {

		$theme_slug = WOLF_THE_THEME;
		$uploads = wp_upload_dir();
		$uploads_dir = $uploads['basedir'] . '/' . $theme_slug;
		$uploads_url = $uploads['baseurl'] . '/' . $theme_slug;

		define('WOLF_FLEXSLIDER_URL', WOLF_THEME_URL . '/includes/features/' . basename( dirname( __FILE__ ) ) .'/' );
		define('WOLF_FLEXSLIDER_DIR', dirname(__FILE__) );
		define('WOLF_FLEXSLIDER_FILES_DIR', $uploads_dir . '/wolf-flexslider');
		define('WOLF_FLEXSLIDER_FILES_URL',  $uploads_url . '/wolf-flexslider/');
	
		$this->create_folder( $uploads_dir );
		$this->create_folder( WOLF_FLEXSLIDER_FILES_DIR );
		$this->create_folder( WOLF_FLEXSLIDER_FILES_DIR.'/slides' );

		add_action('init', array($this, 'options_init') );
		add_action('after_setup_theme',  array($this, 'flexslider_show') );

		add_action('admin_init', array($this, 'create_table') );
		add_action('admin_init', array($this, 'admin_init'));

		add_action('admin_menu', array($this, 'menu_init'));

		add_action('admin_print_styles', array($this, 'admin_styles'));
		add_action('admin_enqueue_scripts', array($this, 'admin_script'));

	}

	function create_folder($dir){
		
		if ( is_writable( $dir ) ) {
			return true;
		}

		elseif ( ! is_dir( $dir ) ) {
			
			$old_mask = umask(0);
			if ( ! mkdir( $dir, 0777 ) ) {
				$message = sprintf( __( 'Error while trying to find the folder <strong>%s</strong>. 
					Please create it manually and set the permission to 777 through your FTP client.<br>
					This folder is necessary to be able to allow users to upload avatar and ticket attachment.', 'wolf' ), $dir );
				$this->admin_notice( $message );
				return false;
			}
			umask( $old_mask );

		}
		

		elseif ( is_dir( $dir ) && ! is_writable( $dir ) ) {
			
			if ( chmod( $dir, 777 ) ) {
				return true;
			} else {
				$message = printf( __( 'To be able to allow user to upload avatar and ticket attachments, the <strong>%s</strong> folder has to be writable. 
					Set the folder permission to 777 through your FTP client.', 'wolf' ), $dir );
				$this->admin_notice( $message );
				return false;
			}
			
		}
	}

	// --------------------------------------------------------------------------

	/**
	* Create flexslider Table
	*/
	function create_table()
	{
		global $wpdb;
		
		$table_name = $wpdb->prefix.'wolf_flexslider';

		$slider_tbl ="CREATE TABLE IF NOT EXISTS `$table_name` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `img` varchar(255) NOT NULL,
		  `link` text NULL,
		  `caption` text NULL,
		  `position` int(11) NOT NULL DEFAULT 0,
		  `coordinates` varchar(255) NULL,
		  `language_code` varchar(7) NOT NULL DEFAULT 'en',
		  PRIMARY KEY (`id`)
		);";
		$wpdb->query($slider_tbl);

		/* Check language column (WPML plugin compatibility) for theme version < 1.3 */
		// $req = "SELECT * FROM `{$wpdb->prefix}wolf_flexslider` LIMIT 1";
		// $check_col = $wpdb->get_row($req);
		// if(!isset($check_col->language_code)){
		// 	$add_col = "ALTER TABLE `{$wpdb->prefix}wolf_flexslider` ADD COLUMN `language_code` VARCHAR(7) NOT NULL DEFAULT 'en'";
		// 	$wpdb->query($add_col);
		// }

	}

	// --------------------------------------------------------------------------


	function admin_styles() {
		if(isset( $_GET['page'] ) ){
			
			$page = $_GET['page'];
			
			if( $page == 'wolf-flexslider-panel' || $page == 'wolf-flexslider.php' ){

				wp_enqueue_style('wolf-flexslider-admin', WOLF_FLEXSLIDER_URL.'css/admin.css', array(), '0.1', 'all');
				wp_enqueue_style('imagareaselect', WOLF_FLEXSLIDER_URL.'css/imgareaselect/imgareaselect-animated.css', array(), '0.9.8', 'all');
			}
		}
	}

	// --------------------------------------------------------------------------

	function admin_script()
	{
		if( isset( $_GET['page'] ) ){
			$page = $_GET['page'];
			
			$page = $_GET['page'];
			
			if( $page == 'wolf-flexslider-panel' || $page == 'wolf-flexslider.php' ){

				wp_enqueue_script( 'imagareaselect', WOLF_FLEXSLIDER_URL.'js/jquery.imgareaselect.pack.js', 'jquery', '0.9.8', true );
				
				if( !wp_script_is( 'jquery-ui-sortable' ) )
					wp_enqueue_script( 'jquery-ui-sortable' );
			}
		}
	}

	// --------------------------------------------------------------------------

	function menu_init()
	{
		// Add Contextual menu
		add_menu_page('Header Slider', 'Header Slider', 'edit_themes', basename(__FILE__), array($this, 'flexslider_panel') );
		//add_submenu_page(basename(__FILE__),'','','administrator',basename(__FILE__),array($this, 'flexslider_panel'));
		add_submenu_page(basename(__FILE__), 'Images', 'Images', 'edit_themes', 'wolf-flexslider-panel',  array($this, 'flexslider_panel') );
		add_submenu_page(basename(__FILE__),  'Header Slider Options', 'Options', 'edit_themes', 'wolf-flexslider-settings', array($this, 'flexslider_settings'));

	}


	// --------------------------------------------------------------------------

	function admin_init()
	{
		register_setting( 'wolf-flexslider-settings', 'wolf_flexslider_settings', array($this, 'settings_validate') );
		add_settings_section( 'wolf-flexslider-settings', '', array($this, 'section_intro'), 'wolf-flexslider-settings' );
		add_settings_field( 'height', __( 'Height', 'wolf' ), array($this, 'setting_height'), 'wolf-flexslider-settings', 'wolf-flexslider-settings' );
		add_settings_field( 'effect', __( 'Transition effect', 'wolf' ), array($this, 'setting_effect'), 'wolf-flexslider-settings', 'wolf-flexslider-settings' );
		add_settings_field( 'pausetime', __( 'Time between animation in milisecond', 'wolf' ), array($this, 'setting_pausetime'), 'wolf-flexslider-settings', 'wolf-flexslider-settings' );
		add_settings_field( 'duration', __( 'Slide transition speed', 'wolf' ), array($this, 'setting_duration'), 'wolf-flexslider-settings', 'wolf-flexslider-settings' );
		add_settings_field( 'autoplay', __( 'Autoplay', 'wolf' ), array($this, 'setting_autoplay'), 'wolf-flexslider-settings', 'wolf-flexslider-settings' );
		add_settings_field( 'direction', __( 'Navigation arrows', 'wolf' ), array($this, 'setting_direction'), 'wolf-flexslider-settings', 'wolf-flexslider-settings' );
		add_settings_field( 'control', __( 'Navigation bullets', 'wolf' ), array($this, 'setting_control'), 'wolf-flexslider-settings', 'wolf-flexslider-settings' );
		add_settings_field( 'pausehover', __( 'Stop animation while the mouse is on the slide', 'wolf' ), array($this, 'setting_pausehover'), 'wolf-flexslider-settings', 'wolf-flexslider-settings' );
	}

	// --------------------------------------------------------------------------

	function options_init()
	{
		global $options;

		if ( false === get_option('wolf_flexslider_settings') ) {

			$default = array(
				'height' => 200,
				'effect' => 'fade',
				'pausetime' => 3500,
				'duration' => 800,
				'autoplay' => 'true',
				'direction' => 'true',
				'control' => 'true',
				'pausehover' => 'true',

			);

			add_option( 'wolf_flexslider_settings', $default );
		}
	}

	// --------------------------------------------------------------------------

	function section_intro()
	{
		// global $options;
		// debug(get_option('wolf_flexslider_settings'));
	}

	// --------------------------------------------------------------------------

	function settings_validate($input)
	{
		$input['height'] = intval($input['height']);
		$input['duration'] = intval($input['duration']);
		$input['pausetime'] = intval($input['pausetime']);
		return $input;
	}

	// --------------------------------------------------------------------------

	function setting_width()
	{
		?>
		<select name="wolf_flexslider_settings[width]">
			<option value="1920" <?php if($this->get_flexslider_option('width') == 1920) echo 'selected="selected"' ?>><?php _e('Full Width', 'wolf'); ?></option>
			<option value="1140" <?php if($this->get_flexslider_option('width') == 1140) echo 'selected="selected"' ?>><?php _e('Wrapped', 'wolf'); ?></option>
		</select>
		<?php
	}

	// --------------------------------------------------------------------------

	function setting_height()
	{
		
		echo '<input type="text" name="wolf_flexslider_settings[height]" class="regular-text" value="'. $this->get_flexslider_option('height') .'" />';
	}

	// --------------------------------------------------------------------------

	function setting_effect()
	{
		?>
		<select name="wolf_flexslider_settings[effect]">
			<option value="slide" <?php if($this->get_flexslider_option('effect') == 'slide') echo 'selected="selected"' ?>>Slide</option>
			<option value="fade" <?php if($this->get_flexslider_option('effect') == 'fade') echo 'selected="selected"' ?>>Fade</option>
		</select>
		<?php
	}

	// --------------------------------------------------------------------------


	function setting_pausetime()
	{
		
		echo '<input type="text" name="wolf_flexslider_settings[pausetime]" class="regular-text" value="'. $this->get_flexslider_option('pausetime') .'" />';
	}

	// --------------------------------------------------------------------------

	function setting_duration()
	{
		
		echo '<input type="text" name="wolf_flexslider_settings[duration]" class="regular-text" value="'. $this->get_flexslider_option('duration') .'" />';
	}

	// --------------------------------------------------------------------------


	function setting_autoplay()
	{
		echo '<input type="hidden" name="wolf_flexslider_settings[autoplay]" value="false" />
		<label><input type="checkbox" name="wolf_flexslider_settings[autoplay]" value="true"'. (($this->get_flexslider_option('autoplay') == 'true') ? ' checked="checked"' : '') .' /></label><br>';
	}

	// --------------------------------------------------------------------------


	function setting_direction()
	{
		echo '<input type="hidden" name="wolf_flexslider_settings[direction]" value="false" />
		<label><input type="checkbox" name="wolf_flexslider_settings[direction]" value="true"'. (($this->get_flexslider_option('direction') == 'true') ? ' checked="checked"' : '') .' /></label><br>';
	}

	// --------------------------------------------------------------------------

	function setting_control()
	{
		echo '<input type="hidden" name="wolf_flexslider_settings[control]" value="false" />
		<label><input type="checkbox" name="wolf_flexslider_settings[control]" value="true"'. (($this->get_flexslider_option('control') == 'true') ? ' checked="checked"' : '') .' /></label><br>';
	}

	// --------------------------------------------------------------------------

	function setting_pausehover()
	{
		echo '<input type="hidden" name="wolf_flexslider_settings[pausehover]" value="false" />
		<label><input type="checkbox" name="wolf_flexslider_settings[pausehover]" value="true"'. (($this->get_flexslider_option('pausehover') == 'true') ? ' checked="checked"' : '') .' /></label><br>';
	}

	// --------------------------------------------------------------------------

	function flexslider_settings()
	{
		?>
		<div class="wrap">
			<div id="icon-themes" class="icon32"></div>
			<h2><?php _e('Flexslider Settings', 'wolf'); ?></h2>
			<form action="options.php" method="post">
				<?php settings_fields( 'wolf-flexslider-settings' ); ?>
				<?php do_settings_sections( 'wolf-flexslider-settings' ); ?>
				<p class="submit"><input name="save" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wolf' ); ?>" /></p>
			</form>
			<p style="margin-top:20px"><?php _e('Visit the official Flexslider Website', 'wolf'); ?>:<br><a target="_blank" href="http://www.woothemes.com/flexslider/">http://www.woothemes.com/flexslider/</a></p>
		</div>
		<?php
	}

	// --------------------------------------------------------------------------

	function get_flexslider_option($value = null)
	{
		global $options;

		$wolf_flexslider_settings = get_option('wolf_flexslider_settings');
		
		if( isset($wolf_flexslider_settings[$value]) )
			return $wolf_flexslider_settings[$value];
		else
			return null;
	}

	// --------------------------------------------------------------------------

	function flexslider_panel()
	{
		include_once WOLF_FLEXSLIDER_DIR . '/wolf-flexslider-panel.php';
	}

	// --------------------------------------------------------------------------

	function flexslider_show()
	{
		include_once WOLF_FLEXSLIDER_DIR . '/wolf-flexslider-show.php';
	}


} // end class

new Wolf_Theme_FlexSlider;

} // end class check