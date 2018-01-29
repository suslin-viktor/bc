<?php
/**
 * Live Scripts
 *
 * @package WordPress
 * @subpackage Live
 * @since Live 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'wolf_enqueue_scripts' ) ) {
	/**
	 * Register theme scripts for Flycase
	 *
	 * We will use the wp_enqueue_scripts function in framework/wolf-core.php to enqueue scripts
	 *
	 * @package WordPress
	 * @subpackage Flycase
	 * @since Flycase 1.0.0
	 */
	function wolf_enqueue_scripts() {

		/* Register theme scripts ------------------------------------------------------*/
		wp_register_script( 'parallax', WOLF_THEME_URL . '/js/lib/jquery.parallax.min.js', 'jquery', '1.1.3', true );
		wp_register_script( 'flexslider', WOLF_THEME_URL.'/js/lib/jquery.flexslider.min.js', 'jquery', '2.2.2', true );
		wp_register_script( 'swipebox', WOLF_THEME_URL . '/js/lib/jquery.swipebox.min.js', 'jquery', '1.2.9', true );
		wp_register_script( 'fancybox', WOLF_THEME_URL.'/js/lib/jquery.fancybox.pack.js', 'jquery', '2.1.4', true );
		wp_register_script( 'fancybox-media', WOLF_THEME_URL.'/js/lib/jquery.fancybox-media.js', 'jquery', '1.0.0', true );
		wp_register_script( 'isotope', WOLF_THEME_URL.'/js/lib/jquery.isotope.min.js', 'jquery', '1.5.19', true );

		wp_register_script( 'gallery', WOLF_THEME_URL . '/js/jquery.gallery.js', 'jquery', WOLF_THEME_VERSION, true );
		wp_register_script( 'store', WOLF_THEME_URL . '/js/jquery.store.js', 'jquery', WOLF_THEME_VERSION, true );

		wp_register_script( 'live', WOLF_THEME_URL.'/js/jquery.functions.js', 'jquery', WOLF_THEME_VERSION, true );
		wp_localize_script( 'live', 'WolfThemeParams', array( 
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'lightbox' => wolf_get_theme_option( 'lightbox' ),
				'videoLightbox' => wolf_get_theme_option( 'video_lightbox' ),
			)
		);

		wp_register_script( 'contact', WOLF_THEME_URL.'/js/jquery.contact.js', 'jquery', WOLF_THEME_VERSION, true );

		/* Enqueue theme scripts ------------------------------------------------------*/
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'parallax' );
		wp_enqueue_script( 'flexslider' );


		/* Check lightbox option */
		if ( wolf_get_theme_option( 'lightbox' ) == 'swipebox' ) {

			wp_enqueue_script( 'swipebox' );

		} elseif ( wolf_get_theme_option( 'lightbox' ) == 'fancybox' ) {
			
			wp_enqueue_script( 'fancybox' );
			wp_enqueue_script( 'fancybox-media');

		}

		if( is_page_template( 'contact-template.php' ) )
			wp_enqueue_script( 'contact' );

		if( is_singular( 'gallery' ) ) {
			wp_enqueue_script( 'isotope' );
			wp_enqueue_script( 'gallery' );
		}
			

		if( is_page_template( 'store-template.php' ) ) {
			wp_enqueue_script( 'isotope' );
			wp_enqueue_script( 'store');
		}

		wp_enqueue_script( 'live' );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) 
			wp_enqueue_script( 'comment-reply' ); // loads the javascript required for threaded comments
	}
	add_action( 'wp_enqueue_scripts', 'wolf_enqueue_scripts' );
} // end function check
	