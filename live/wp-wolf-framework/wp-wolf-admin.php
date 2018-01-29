<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Wolf_Framework_Admin' ) ) {
	/**
	 * Admin Theme Class
	 *
	 * @class Wolf_Framework_Admin
	 * @since 1.4.2
	 * @package WolfFramework
	 * @author WolfThemes
	 */
	class Wolf_Framework_Admin {

		/**
		 * Wolf_Framework_Admin Constructor.
		 */
		public function __construct() {

			// Auto-load admin classes on demand
			if ( function_exists( '__autoload' ) ) {
				spl_autoload_register( '__autoload' );
			}

			spl_autoload_register( array( $this, 'autoload' ) );

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_menu', array( $this, 'menu' ), 8 );
			add_action( 'admin_notices', array( $this, 'display_notice' ) );
		}

		/**
		 * Auto-load classes on demand to reduce memory consumption.
		 *
		 * @param string $class
		 */
		public function autoload( $class ) {
			$path  = null;
			$class = strtolower( $class );
			$file  = 'class-' . str_replace( '_', '-', $class ) . '.php';

			if ( strpos( $class, 'wolf_theme_admin_' ) !== false ) {
				$file = str_replace( 'wolf-theme-admin-', '', $file );
				$path = WOLF_FRAMEWORK_DIR . '/classes/';
			}

			if ( $path && is_readable( $path . $file ) ) {
				include_once( $path . $file );
				return;
			}
		}

		/**
		 * Includes admin functions, scripts and files from the theme includes/admin folder
		 */
		public function includes() {

			// Core admin functions
			require_once( 'includes/admin/admin-functions.php' );
			require_once( 'includes/admin/scripts.php' );

			$inc_dir = WOLF_THEME_DIR . '/includes/admin';

			if ( is_dir( $inc_dir ) ) {
				foreach ( glob( $inc_dir . '/*.php' ) as $filename ) {
					include_once( $filename );
				}
			}
		}

		/**
	  	 * Includes admin files
		 */
		public function init() {

			$this->includes();
			$this->default_options();

		}

		/**
		 * Add the Theme menu to the WP admin menu
		 */
		public function menu() {

			$current_theme_name = wp_get_theme()->Name;

			add_menu_page( $current_theme_name, $current_theme_name, 'manage_options', 'wolf-theme-options', array( $this, 'options' ), 'dashicons-welcome-view-site' );
			add_submenu_page( 'wolf-theme-options', __( 'Options', 'wolf' ), __( 'Options', 'wolf' ), 'manage_options', 'wolf-theme-options', array( $this, 'options' ) );

			/* If update notice is enabled, we add a theme update page */
			if ( WOLF_UPDATE_NOTICE ) {

				$menu_title = __( 'Updates', 'wolf' );
				if ( $xml = wolf_get_theme_changelog() ) {
					if ( version_compare( WOLF_THEME_VERSION, $xml->latest ) == -1 ) {
						$menu_title = __( 'Updates', 'wolf' ) . '<span class="update-plugins count-1 wolf-custom-count"><span class="update-count">1</span></span>';
					}
				}

				add_submenu_page( 'wolf-theme-options', __( 'Updates', 'wolf' ), $menu_title, 'manage_options', 'wolf-theme-update', array( $this, 'update_page' ) );
			}

			// Support forum link/page
			if ( WOLF_SUPPORT_PAGE ) {

				add_submenu_page( 'wolf-theme-options', __( 'Helpdesk', 'wolf' ), __( 'Helpdesk', 'wolf' ), 'manage_options', 'wolf-theme-support', array( $this, 'support_page' ) );
			}

			add_submenu_page(
				'options.php',
				//'wolf-framework',
				'presets',
				'presets',
				'manage_options',
				'wolf-customizer-presets',
				array( $this, 'customizer_preset_page' )
			);
		}

		/**
		 * This page is not visible. It is used to execute customizer preset functions as no js fallback
		 * @todo remove
		 */
		public function customizer_preset_page() {

			require( WOLF_FRAMEWORK_DIR . '/includes/customizer-presets.php' );
		}

		/**
		 * Add an update or error notice to the dashboard when needed
		 */
		public function display_notice() {

			global $pagenow;

			// Theme update notifications
			if ( $pagenow == 'index.php' ) {
				wolf_theme_update_notification_message();
			}

			if ( WOLF_ERROR_NOTICES ) {

				/* Error notices
			    	--------------------------------------------------------*/

				// No cURL
				$no_cURL = __( 'The <strong>cURL</strong> extension is not installed on your server. This extension is required to display theme update notifications.', 'wolf' );

				if ( ! function_exists( 'curl_init' ) ) {
					wolf_admin_notice( $no_cURL, 'error', true, 'no_cURL' );
				}

				// No GD library
				$no_GD_library = __( 'The <strong>GD library</strong> extension is not installed on your server. This extension is essential to Wordpress to be able to resize your images. Please contact your hosting service for more informations.', 'wolf' );

				if ( ! extension_loaded( 'gd' ) && ! function_exists( 'gd_info' ) ) {
					wolf_admin_notice( $no_GD_library, 'error', true, 'no_GD_library' );
				}
			}

			/* Always display wrong theme installation notice
		    	-------------------------------------------------------------------*/

			/* Incorect Installation */
			$wrong_install = sprintf(
				__( 'It seems that <strong>the theme has been installed incorrectly</strong>. Go <a href="%s" target="_blank">here</a> to find instructions about theme installation.', 'wolf' ), 'http://wolfthemes.com/common-wordpress-theme-issues/'
				);

			$wolf_wp_themes_folder = basename( dirname( dirname( dirname( __FILE__ ) ) ) );

			if ( $wolf_wp_themes_folder != 'themes' ) {
				wolf_admin_notice( $wrong_install , 'error' );
			}

			return false;
		}

		/**
		 * Update Page
		 */
		public function update_page() {
			require( WOLF_FRAMEWORK_DIR . '/pages/update.php' );
		}

		/**
		 * Support Page  :
		 * Redirect to the support forum
		 * http://help.wolfthemes.com/
		 */
		public function support_page() {
			require( WOLF_FRAMEWORK_DIR . '/pages/support.php' );
		}

		/**
		 * Theme Options
		 * Generate Theme Options page with the Wolf_Theme_Options class
		 * The theme options are set in includes/options.php as an array
		 */
		public function options() {

			if ( class_exists( 'Wolf_Theme_Admin_Options' ) ) {
				global $wolf_theme_options;
				$wolf_do_theme_options = new Wolf_Theme_Admin_Options( $wolf_theme_options );
			}
		}

		/**
		 * Theme Default Options
		 */
		public function default_options() {

			// Default theme options are defined in "includes/default-options.php"
			if ( function_exists( 'wolf_theme_default_options_init' ) )
				wolf_theme_default_options_init();

			// Default customizer options are defined in "includes/default-customizer-options.php"
			if ( function_exists( 'wolf_theme_customizer_options_init' ) )
				wolf_theme_customizer_options_init();
		}

	} // end class

} // end class exists check