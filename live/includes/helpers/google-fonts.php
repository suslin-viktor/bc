<?php
global $wolf_google_fonts;
$wolf_google_fonts = array();
// =============================================

$wolf_google_fonts[] = 'PT+Serif:400,700,400italic,700italic';
$wolf_google_fonts[] = 'Source+Sans+Pro:400,700,900';
$wolf_google_fonts[] = 'Lato:400,700';
$wolf_google_fonts[] = 'Montserrat:400,700';

/**
* You can add your own google font here
*/

// $wolf_google_fonts[] = 'AnyGoogleFont:400,700';

/* The options */
if ( wolf_get_theme_option( 'heading_google_font_code' ) )
	$wolf_google_fonts[] = wolf_get_theme_option( 'heading_google_font_code' );

if ( wolf_get_theme_option( 'menu_google_font_code' ) )
	$wolf_google_fonts[] = wolf_get_theme_option( 'menu_google_font_code' );

if ( wolf_get_theme_option( 'body_google_font_code' ) )
	$wolf_google_fonts[] = wolf_get_theme_option( 'body_google_font_code' );


// =============================================

if ( ! function_exists( 'wolf_google_fonts' ) ) {
	/**
	 * Loads our special font CSS file.
	 *
	 * To disable in a child theme, use wp_dequeue_style()
	 * function mytheme_dequeue_fonts() {
	 *     wp_dequeue_style( 'wolf-fonts' );
	 * }
	 * add_action( 'wp_enqueue_scripts', 'mytheme_dequeue_fonts', 11 );
	 *
	 */
	function wolf_google_fonts() {

		global $wolf_google_fonts;

		if( $wolf_google_fonts && is_array( $wolf_google_fonts ) && $wolf_google_fonts != array() ){

			$protocol = is_ssl() ? 'https' : 'http';
			$query_args = array(
				'family' => implode( '|', $wolf_google_fonts ),
				'subset' => 'latin,latin-ext',
			);

			wp_enqueue_style( 'wolf-fonts', esc_url( add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ) ), array(), null );
		}
	}
	add_action( 'wp_enqueue_scripts', 'wolf_google_fonts' );
}
