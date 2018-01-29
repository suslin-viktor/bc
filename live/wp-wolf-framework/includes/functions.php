<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'wolf_get_theme_slug' ) ) {
	/**
	 * Get the theme slug
	 *
	 * @access public
	 * @return string
	 */
	function wolf_get_theme_slug() {

		$slug = get_template();

		return sanitize_title( $slug );

	}
}

if ( ! function_exists( 'wolf_add_version_meta' ) ) {
	/**
	 * Add metatags with Theme and Framework Versions
	 * Usefull for support
	 *
	 * @return string
	 */
	function wolf_add_version_meta() {

		echo '<meta name="generator" content="' . WOLF_THEME_NAME . ' ' . WOLF_THEME_VERSION .'" />' . "\n";
		echo '<meta name="generator" content="Wolf Framework ' .WOLF_FRAMEWORK_VERSION . '" />' . "\n";

	}
	add_action( 'wolf_meta_head', 'wolf_add_version_meta' );
}

if ( ! function_exists( 'wolf_get_post_thumbnail_url' ) ) {
	/**
	 * Get any thumbnail URL
	 * @param string $format
	 * @param int $post_id
	 * @return string
	 */
	function wolf_get_post_thumbnail_url( $format = 'medium', $post_id = null ) {
		global $post;

		if ( is_object( $post ) && isset( $post->ID ) && null == $post_id )
			$ID = $post->ID;
		else
			$ID = $post_id;

		if ( $ID && has_post_thumbnail( $ID ) ) {

			$attachment_id = get_post_thumbnail_id( $ID );
			if ( $attachment_id ) {
				$img_src = wp_get_attachment_image_src( $attachment_id, $format ); 
				
				if ( $img_src && isset( $img_src[0] ) )
					return $img_src[0];
			}
		}
	}
}

if ( ! function_exists( 'wolf_get_theme_uri' ) ) {
	/**
	 * Check if a file exists in a child theme
	 * else returns the URL of the parent theme file
	 * Mainly uses for images
	 * @param string $file
	 * @return string
	 */
	function wolf_get_theme_uri( $file = null ) {

		if ( is_file( get_stylesheet_directory() . $file ) ) {

			return get_stylesheet_directory_uri() . $file;

		} else {

			return get_template_directory_uri() . $file;
		}
	}
}

if ( ! function_exists( 'wolf_get_theme_option' ) ) {
	/**
	 * Get theme option from "wolf_theme_options_template" array
	 *
	 * @param string $o
	 * @param string $default
	 * @return string
	 */
	function wolf_get_theme_option( $o, $default = null ) {
		
		global $options;

		$wolf_framework_options = get_option( 'wolf_theme_options_' . wolf_get_theme_slug() );

		if ( isset( $wolf_framework_options[ $o ] ) ) {
			
			$option = $wolf_framework_options[ $o ];

			if ( function_exists( 'icl_t' ) ) {

				$option = icl_t( wolf_get_theme_slug(), $o, $option ); // WPML
			}

			return $option;

		} elseif ( $default ) {

			return $default;
		}
	}
}

if ( ! function_exists( 'wolf_sample' ) ) {
	/**
	* Create a formated sample of any text
	* @param string $text
	* @param int $nbcar
	* @param string $after
	* @return string
	*/
	function wolf_sample( $text, $nbcar = 140, $after = '...' ) {
		$text = strip_tags( $text );   
		
		if ( strlen( $text ) > $nbcar ) {
			
			preg_match( '!.{0,'.$nbcar.'}\s!si', $text, $match );
			if ( isset( $match[0] ) ) {
				$str = trim( $match[0] ) . $after;
			} else {
				$str = $text;
			}
		} else {
			$str = $text;  
		}
		
		$str = preg_replace( '/\s\s+/', '', $str );
		$str = preg_replace(  '|\[(.+?)\](.+?\[/\\1\])?|s', '', $str );

		return $str;
	}
}

if ( ! function_exists( 'wolf_pagination' ) ) {
	/**
	 * Display WP pagination
	 *
	 * @param object $loop
	 * @return string
	 */
	function wolf_pagination( $loop = null ) {

		if ( ! $loop ) {
			global $wp_query;
			$max = $wp_query->max_num_pages;
		} else {
			$max = $loop->max_num_pages;
		}
		
		$big  = 999999999; // need an unlikely integer
		$args = array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?paged=%#%',
			'prev_text' 	=> '&larr;',
			'next_text' 	=> '&rarr;',
			'type'		=> 'list',
			'current' => max( 1, get_query_var( 'paged' ) ),
			'total' => $max,
		);

		echo '<div class="pagination">';
		echo paginate_links( $args ) . '<div style="clear:both"></div>';
		echo '</div>';
	}
}

if ( ! function_exists( 'wolf_compact_css' ) ) {
	/**
	 * Remove spaces in inline CSS
	 *
	 * @param string $css
	 * @return string
	 */
	function wolf_compact_css( $css ) {

		return preg_replace( '/\s+/', ' ', $css );
	}
}

if ( ! function_exists( 'wolf_hex_to_rgb' ) ) {
	/**
	 * Convert hex color to rgb
	 *
	 * @param string $hex
	 * @return string
	 */
	function wolf_hex_to_rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex,0,1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex,1,1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex,2,1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = array( $r, $g, $b );
		return implode( ',', $rgb ); // returns the rgb values separated by commas
		//return $rgb; // returns an array with the rgb values
	}
}

if ( ! function_exists( 'wolf_color_brightness' ) ) {
	/**
	 * Brightness color function simiar to sass lighten and darken
	 *
	 * @param string $hex
	 * @return string
	 */
	function wolf_color_brightness( $hex, $percent ) {

		$steps = ( ceil( ( $percent * 200 ) / 100 ) ) * 2;

		// Steps should be between -255 and 255. Negative = darker, positive = lighter
		$steps = max( -255, min( 255, $steps ) );

		// Format the hex color string
		$hex = str_replace( '#', '', $hex );
		if ( strlen( $hex ) == 3 ) {
			$hex = str_repeat( substr( $hex,0,1 ), 2 ).str_repeat( substr( $hex,1,1 ), 2 ).str_repeat( substr( $hex, 2, 1 ), 2 );
		}

		// Get decimal values
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		// Adjust number of steps and keep it inside 0 to 255
		$r = max( 0, min( 255, $r + $steps ) );
		$g = max( 0, min( 255, $g + $steps ) );  
		$b = max( 0, min( 255, $b + $steps ) );

		$r_hex = str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
		$g_hex = str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
		$b_hex = str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );

		return '#' . $r_hex . $g_hex . $b_hex;
	}
}

if ( ! function_exists( 'wolf_is_ie8' ) ) {
	/**
	 * Check if IE8
	 *
	 * @return bool
	 */
	function wolf_is_ie8() {
		
		global $is_IE;
		if ( preg_match( '#MSIE 8#', $_SERVER['HTTP_USER_AGENT'], $browser_version ) ) {
			if ( $browser_version[0] )
				return true; // make the computer explode
		}
	}
}