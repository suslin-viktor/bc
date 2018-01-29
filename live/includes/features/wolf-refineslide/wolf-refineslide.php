<?php
class Wolf_Theme_RefineSlide {


	function __construct(){

		$theme_slug = WOLF_THE_THEME;
		$uploads = wp_upload_dir();
		$uploads_dir = $uploads['basedir'] . '/' . $theme_slug;
		$uploads_url = $uploads['baseurl'] . '/' . $theme_slug;

		define('WOLF_REFINESLIDE_URL', WOLF_THEME_URL . '/includes/features/' . basename( dirname( __FILE__ ) ) .'/' );
		define('WOLF_REFINESLIDE_DIR', dirname(__FILE__) );
		define('WOLF_REFINESLIDE_FILES_DIR', $uploads_dir . '/wolf-refineslide');
		define('WOLF_REFINESLIDE_FILES_URL',  $uploads_url . '/wolf-refineslide/');
	
		$this->create_folder( $uploads_dir );
		$this->create_folder(WOLF_REFINESLIDE_FILES_DIR);
		$this->create_folder(WOLF_REFINESLIDE_FILES_DIR.'/slides');

		add_action('init', array($this, 'options_init') );
		add_action('after_setup_theme',  array($this, 'refineslide_show') );

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
	* Create refineslide Table
	*/
	function create_table()
	{
		global $wpdb;
		$slider_tbl ="CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wolf_refineslide` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `img` varchar(255) NOT NULL,
		  `link` text NULL,
		  `caption` text NULL,
		  `caption_position` varchar(255) NOT NULL DEFAULT 'bottom-right',
		  `position` int(11) NOT NULL DEFAULT 0,
		  `coordinates` varchar(255) NULL,
		  `language_code` varchar(7) NOT NULL DEFAULT 'en',
		  PRIMARY KEY (`id`)
		);";
		$wpdb->query($slider_tbl);

		/* Check language column (WPML plugin compatibility) for theme version < 1.3 */
		// $req = "SELECT * FROM `{$wpdb->prefix}wolf_refineslide` LIMIT 1";
		// $check_col = $wpdb->get_row($req);
		// if(!isset($check_col->language_code)){
		// 	$add_col = "ALTER TABLE `{$wpdb->prefix}wolf_refineslide` ADD COLUMN `language_code` VARCHAR(7) NOT NULL DEFAULT 'en'";
		// 	$wpdb->query($add_col);
		// }

	}

	// --------------------------------------------------------------------------


	function admin_styles()
	{
		if(isset( $_GET['page'] ) ){
			
			$page = $_GET['page'];
			
			if( $page == 'wolf-refineslide-panel' || $page == 'wolf-refineslide.php' ){

				wp_enqueue_style('wolf-refineslide-admin', WOLF_REFINESLIDE_URL.'css/admin.css', array(), '0.1', 'all');
				wp_enqueue_style('imagareaselect', WOLF_REFINESLIDE_URL.'css/imgareaselect/imgareaselect-animated.css', array(), '0.9.8', 'all');
			}
		}
	}

	// --------------------------------------------------------------------------

	function admin_script()
	{
		if( isset( $_GET['page'] ) ){
			$page = $_GET['page'];
			
			$page = $_GET['page'];
			
			if( $page == 'wolf-refineslide-panel' || $page == 'wolf-refineslide.php' ){

				wp_enqueue_script( 'imagareaselect', WOLF_REFINESLIDE_URL.'js/jquery.imgareaselect.pack.js', 'jquery', '0.9.8', true );
				
				if( !wp_script_is( 'jquery-ui-sortable' ) )
					wp_enqueue_script( 'jquery-ui-sortable' );
			}
		}
	}

	// --------------------------------------------------------------------------

	function menu_init()
	{
		// Add Contextual menu
		add_menu_page('Home Slider', 'Home Slider', 'edit_themes', basename(__FILE__), array($this, 'refineslide_panel') );
		//add_submenu_page(basename(__FILE__),'','','administrator',basename(__FILE__),array($this, 'refineslide_panel'));
		add_submenu_page(basename(__FILE__), 'Images', 'Images', 'edit_themes', 'wolf-refineslide-panel',  array($this, 'refineslide_panel') );
		add_submenu_page(basename(__FILE__),  'Home Slider Options', 'Options', 'edit_themes', 'wolf-refineslide-settings', array($this, 'refineslide_settings'));

	}


	// --------------------------------------------------------------------------

	function admin_init()
	{
		register_setting( 'wolf-refineslide-settings', 'wolf_refineslide_settings', array($this, 'settings_validate') );
		add_settings_section( 'wolf-refineslide-settings', '', array($this, 'section_intro'), 'wolf-refineslide-settings' );
		add_settings_field( 'height', __( 'Height', 'wolf' ), array($this, 'setting_height'), 'wolf-refineslide-settings', 'wolf-refineslide-settings' );
		add_settings_field( 'effect', __( 'Transition Effect', 'wolf' ), array($this, 'setting_effect'), 'wolf-refineslide-settings', 'wolf-refineslide-settings' );
		add_settings_field( 'delay', __( 'Time between animation in milliseconds', 'wolf' ), array($this, 'setting_delay'), 'wolf-refineslide-settings', 'wolf-refineslide-settings' );
		add_settings_field( 'transition_duration', __( 'Transition Duration', 'wolf' ), array($this, 'setting_transition_duration'), 'wolf-refineslide-settings', 'wolf-refineslide-settings' );
		add_settings_field( 'navigation', __( 'Navigation', 'wolf' ), array($this, 'setting_navigation'), 'wolf-refineslide-settings', 'wolf-refineslide-settings' );
		add_settings_field( 'autoplay', __( 'Autoplay', 'wolf' ), array($this, 'setting_autoplay'), 'wolf-refineslide-settings', 'wolf-refineslide-settings' );
	}

	// --------------------------------------------------------------------------

	function options_init()
	{
		global $options;

		if ( false === get_option('wolf_refineslide_settings') ) {

			$default = array(
				'height' => 450,
				'autoplay' => 'true',
				'delay' => 4000,
				'effect' => 'random',
				'transition_duration' => 1000,
				'navigation' => 'arrows'

			);

			add_option( 'wolf_refineslide_settings', $default );
		}
	}

	// --------------------------------------------------------------------------

	function section_intro()
	{
		// global $options;
		// debug(get_option('wolf_refineslide_settings'));
	}

	// --------------------------------------------------------------------------

	function settings_validate($input)
	{
		$input['height'] = intval( $input['height'] );
		$input['delay'] = intval( $input['delay'] );
		$input['transition_duration'] = intval( $input['transition_duration'] );
		$input['effect'] = esc_attr( $input['effect'] );
		$input['navigation'] = esc_attr( $input['navigation'] );

		return $input;
	}

	// --------------------------------------------------------------------------

	function setting_height()
	{
		
		echo '<input type="text" name="wolf_refineslide_settings[height]" class="regular-text" value="'. $this->get_refineslide_option('height') .'">';
	}

	// --------------------------------------------------------------------------

	function setting_delay()
	{
		
		echo '<input type="text" name="wolf_refineslide_settings[delay]" class="regular-text" value="'. $this->get_refineslide_option('delay') .'">';
	}

	// --------------------------------------------------------------------------

	function setting_transition_duration()
	{
		
		echo '<input type="text" name="wolf_refineslide_settings[transition_duration]" class="regular-text" value="'. $this->get_refineslide_option('transition_duration') .'">';
	}

	// --------------------------------------------------------------------------


	function setting_autoplay()
	{
		echo '<input type="hidden" name="wolf_refineslide_settings[autoplay]" value="false" />
		<label><input type="checkbox" name="wolf_refineslide_settings[autoplay]" value="true"'. (($this->get_refineslide_option('autoplay') == 'true') ? ' checked="checked"' : '') .' /></label><br>';
	}

	// --------------------------------------------------------------------------

	function setting_effect()
	{	$effects = array('random', 'cubeH', 'cubeV', 'fade', 'sliceH', 'sliceV', 'slideH', 'slideV', 'scale', 'blockScale', 'kaleidoscope', 'fan', 'blindH', 'blindV');
		?>
		<select name="wolf_refineslide_settings[effect]">
			<?php foreach($effects as $e): ?>
			<option value="<?php echo $e; ?>" <?php if($this->get_refineslide_option('effect') == $e) echo 'selected="selected"' ?>><?php echo $e; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	// --------------------------------------------------------------------------

	function setting_navigation()
	{	$nav_type = array('arrows', 'thumbs', 'none');
		?>
		<select name="wolf_refineslide_settings[navigation]">
			<?php foreach($nav_type as $n): ?>
			<option value="<?php echo $n; ?>" <?php if($this->get_refineslide_option('navigation') == $n) echo 'selected="selected"' ?>><?php echo $n; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	// --------------------------------------------------------------------------


	function refineslide_settings()
	{
		?>
		<div class="wrap">
			<div id="icon-themes" class="icon32"></div>
			<h2><?php _e('RefineSlide Settings', 'wolf'); ?></h2>
			<form action="options.php" method="post">
				<?php settings_fields( 'wolf-refineslide-settings' ); ?>
				<?php do_settings_sections( 'wolf-refineslide-settings' ); ?>
				<p class="submit"><input name="save" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wolf' ); ?>" /></p>
			</form>
			<p style="margin-top:20px"><?php _e('Visit the official refineslide Website', 'wolf'); ?>:<br>
				<a target="_blank" href="http://refineslide.frebsite.nl/">http://refineslide.frebsite.nl/</a></p>
		</div>
		<?php
	}

	// --------------------------------------------------------------------------

	function get_refineslide_option($value = null)
	{
		global $options;

		$wolf_refineslide_settings = get_option('wolf_refineslide_settings');
		
		if( isset($wolf_refineslide_settings[$value]) )
			return $wolf_refineslide_settings[$value];
		else
			return null;
	}

	// --------------------------------------------------------------------------

	function refineslide_panel()
	{
		include_once WOLF_REFINESLIDE_DIR . '/wolf-refineslide-panel.php';
	}

	// --------------------------------------------------------------------------

	function refineslide_show()
	{
		include_once WOLF_REFINESLIDE_DIR . '/wolf-refineslide-show.php';
	}


} // end class
$wolf_refineslide = new Wolf_Theme_RefineSlide;