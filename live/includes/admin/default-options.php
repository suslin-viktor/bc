<?php
/**
 * Set default theme options
 */
function wolf_theme_default_options_init() {
	
	global $options;
	
	$very_old_options = get_option( 'bd_theme_options' );
	$old_theme_options = get_option( 'wolf_theme_options' );
	$theme_options = get_option( 'wolf_theme_options_' . wolf_get_theme_slug() );

	$header_bg = wolf_get_theme_uri( '/images/presets/header_bg.jpg' );

	$default_options = array(

		'body_font' => "'Helvetica Neue', Helvetica, Arial, sans-serif",
		'lightbox' => 'fancybox',
		'video_lightbox' => 'true',
		   
		'menu_font' => "'BebasRegular'",
		'title_font' => "'OstrichSansMedium'",
		'sidebar_position' => 'right',

		'skin' => 'dark',
		'sticky_menu' => 'true',

		'header' => 'embed',
		'embed_header' =>'[wolf_col_6 class="first"][wolf_jplayer_playlist id="1"][/wolf_col_6]
[wolf_col_6 class="last"]
http://vimeo.com/33316741
[/wolf_col_6]',

		'header_color' => '#0d0d0d',
		'header_img' => $header_bg,
		'header_repeat' => 'no-repeat',
		'header_position' => 'center bottom',
		'header_attachment' => 'fixed',
		'header_size' => 'cover',
		'header_parallax' => true,

		'footer_text' => 'Wordpress theme by <a href="http://themes.brutaldesign.com">Brutal Design</a>',

	);

	if ( $very_old_options && ! $old_theme_options && ! $theme_options ) {
		
		$very_old_options['lightbox'] = 'fancybox';
		$very_old_options['video_lightbox'] = 'true';
		add_option( '_wolf_theme_default_pages', true );
		add_option( '_live_updated', '1.6.5' );
		add_option( 'wolf_theme_options_' . wolf_get_theme_slug(), $very_old_theme_options );
		delete_option( 'bd_theme_options' );
		delete_user_meta( get_current_user_id(), 'tgmpa_dismissed_notice' );

	} elseif ( ! $theme_options && $old_theme_options ) {

		$old_theme_options['video_lightbox'] = 'true';
		add_option( 'wolf_theme_options_' . wolf_get_theme_slug(), $old_theme_options );
		delete_option( 'wolf_theme_options' );

	} elseif ( ! $theme_options ) {

		add_option( 'wolf_theme_options_' . wolf_get_theme_slug(), $default_options );
	}

	// woo thumbnails
	$catalog = array(
		'width' 	=> '400',	// px
		'height'	=> '400',	// px
		'crop'	=> 1 		// true
	);
 
	$single = array(
		'width' 	=> '600',	// px
		'height'	=> '600',	// px
		'crop'	=> 1 		// true
	);
 
	$thumbnail = array(
		'width' 	=> '120',	// px
		'height'	=> '120',	// px
		'crop'	=> 0 		// false
	);
 
	// Image sizes
	update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
	update_option( 'shop_single_image_size', $single ); 		// Single product image
	update_option( 'shop_thumbnail_image_size', $thumbnail );
}
?>