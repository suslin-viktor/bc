<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Wolf_Framework' ) ) {
	/**
	 * Main Framework Class
	 *
	 * @since 1.4.2
	 * @package WolfFramework
	 * @author WolfThemes
	 */
	class Wolf_Framework {

		/**
		 * @var string
		 */
		public $version = '1.4.2.5';

		/**
		 * Default theme settings
		 *
		 * @var array
		 */
		public $options = array(
			'menus' => array( 'primary' => 'Main Menu' ),
			'post-formats' => array( 'gallery', 'image', 'aside', 'video', 'audio', 'link', 'quote', 'status', 'chat' ),
			'images' => array(),
		);

		/**
		 * Wolf_Framework Constructor.
		 */
		public function __construct( $options = array() ) {

			$this->options = $options + $this->options;

			// Define constants
			$this->define_constants();

			// Auto-load front end classes on demand
			if ( function_exists( '__autoload' ) ) {
				spl_autoload_register( '__autoload' );
			}

			spl_autoload_register( array( $this, 'autoload' ) );

			$this->init();

			/**
			 * Add theme support
			 */
			add_action( 'after_setup_theme', array( $this, 'after_setup' ) );
		}

		/**
		 * Define Constants
		 */
		private function define_constants() {

			// require config file
			require( 'wp-wolf-config.php' );

			// Display the theme update notice
			if ( ! defined( 'WOLF_UPDATE_NOTICE' ) ) {
				define( 'WOLF_UPDATE_NOTICE', true );
			}

			// Display error notices ( no GD library, no cURL )
			if ( ! defined( 'WOLF_ERROR_NOTICES' ) ) {
				define( 'WOLF_ERROR_NOTICES', true );
			}

			// Display the link to the support forum
			if ( ! defined( 'WOLF_SUPPORT_PAGE' ) ) {
				define( 'WOLF_SUPPORT_PAGE', true );
			}

			// Enable the customizer
			if ( ! defined( 'WOLF_ENABLE_CUSTOMIZER' ) ) {
				define( 'WOLF_ENABLE_CUSTOMIZER', true );
			}

			// Framework version
			define( 'WOLF_FRAMEWORK_VERSION', $this->version );

			// Theme Directory and URL
			define( 'WOLF_THEME_DIR', get_template_directory() );
			define( 'WOLF_THEME_URL', get_template_directory_uri() );

			$framework_path = str_replace( DIRECTORY_SEPARATOR, '/', dirname( __FILE__ ) );
			$template_path  = str_replace( DIRECTORY_SEPARATOR, '/', get_template_directory() );
			$template_url   = get_template_directory_uri();

			// Framework directory and URL
			define( 'WOLF_FRAMEWORK_DIR', dirname( __FILE__ ) );
			define( 'WOLF_FRAMEWORK_URL', str_replace( $template_path, $template_url, $framework_path ) );

			// Get the Theme Version and Theme Name for the style.css file
			$theme_data = wp_get_theme( sanitize_title( get_template() ) );

			// Define theme version to display update notification
			define( 'WOLF_THEME_VERSION', $theme_data->Version );

			// Define theme name
			define( 'WOLF_THEME_NAME', $theme_data->Name );

			// Define theme slug
			define( 'WOLF_THEME_SLUG', $theme_data->template );

			/**
			 * Set global cache duration ( 6 hours )
			 * We will use Wordpress transient keys to cache our data (mainly the theme changelog)
			 */
			define( 'WOLF_CACHE_DURATION', 60 * 60 * 6 );
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

			if ( strpos( $class, 'wolf_theme_' ) !== false ) {
				$file = str_replace( 'wolf-theme-', '', $file );
				$path = WOLF_FRAMEWORK_DIR . '/classes/';
			}

			if ( $path && is_readable( $path . $file ) ) {
				include_once( $path . $file );
				return;
			}
		}

		/**
		 * Init function
		 */
		public function init() {

			$this->includes();
			$this->register_menus();
			$this->admin();
		}

		/**
		 * Includes framework filters, functions, specific front end options & template-tags
		 */
		public function includes() {

			// Core functions
			require_once( WOLF_FRAMEWORK_DIR . '/includes/filters.php' );
			require_once( WOLF_FRAMEWORK_DIR . '/includes/functions.php' );
			require_once( WOLF_FRAMEWORK_DIR . '/includes/hooks.php' );

			// Includes files from theme includes dir
			$inc_dir = WOLF_THEME_DIR . '/includes';
			if ( is_dir( $inc_dir ) ) {
				foreach ( glob( $inc_dir . '/*.php' ) as $filename ) {
					include_once( $filename );
				}
			}

			// Includes files from theme includes/helpers dir
			$helpers_dir = WOLF_THEME_DIR . '/includes/helpers';
			if ( is_dir( $helpers_dir ) ) {
				foreach ( glob( $helpers_dir . '/*.php' ) as $filename ) {
					include_once( $filename );
				}
			}
		}

		/**
		 * First basic wordpress components
		 */
		public function after_setup() {

			$this->language();
			$this->supports();
			$this->images();
			$this->post_formats();
		}

		/**
		 * Default WP supports
		 * Post thumbnails, RSS and editor style file if we have one
		 */
		public function supports() {

			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'editor-style' );

			if ( is_file( WOLF_THEME_DIR . '/css/editor-style.css' ) ) {
				add_editor_style( 'css/editor-style.css' );
			}
		}

		/**
		 * Register menus
		 */
		public function register_menus() {
			if ( function_exists( 'register_nav_menus' ) ) {
				register_nav_menus( $this->options['menus'] );
			}
		}

		/**
		 * Set the different thumbnail sizes needed in the design
		 * (set in functions.php)
		 */
		public function images() {
			global $content_width;
			set_post_thumbnail_size( $content_width, $content_width / 2 ); // default Post Thumbnail dimensions

			if ( $this->options['images'] != array() ) {
				if ( function_exists( 'add_image_size' ) ) {
					foreach ( $this->options['images'] as $k => $v ) {
						add_image_size( $k, $v[0], $v[1], $v[2] );
					}
				}
			}
		}

		/**
		 * Post Formats :
		 * Add Post format Support
		 */
		public function post_formats() {
			if ( $this->options['post-formats'] != array() ) {
				add_theme_support( 'post-formats', $this->options['post-formats'] );
			}
		}

		/**
		 * Language Support :
		 * Check .po and .mo files in "languages" folder
		 */
		public function language() {
			load_theme_textdomain( 'wolf', WOLF_THEME_DIR . '/languages' );

			$locale = get_locale();
			$locale_file = WOLF_THEME_DIR . "/languages/$locale.php";

			if ( is_readable( $locale_file ) ){
				require( $locale_file );
			}
		}

		/**
		 * Admin
		 * See "wolf-admin.php"
		 *
		 * @access public
		 */
		public function admin() {
			if ( is_admin() ) {
				require( WOLF_FRAMEWORK_DIR . '/wp-wolf-admin.php' );
				$wolf_admin_theme = new Wolf_Framework_Admin;
			}
		}

	} // end class

} // end class exists check