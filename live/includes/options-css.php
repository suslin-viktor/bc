<?php
if ( ! function_exists( 'Wolf_Custom_Styles' ) ) {
/*-----------------------------------------------------------------------------------*/
/*  Print custom CSS set up in the theme options
/*-----------------------------------------------------------------------------------*/
class Wolf_Custom_Styles{

	function hex_to_rgb($hex) {
		$hex = str_replace("#", "", $hex);

		if (strlen($hex) == 3) {
		$r = hexdec(substr($hex,0,1).substr($hex,0,1));
		$g = hexdec(substr($hex,1,1).substr($hex,1,1));
		$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return implode(",", $rgb); // returns the rgb values separated by commas
		//return $rgb; // returns an array with the rgb values
	}

	// --------------------------------------------------------------------------

	function brightness($hex, $percent) {

		$steps = (ceil(($percent*200) / 100))*2;

		// Steps should be between -255 and 255. Negative = darker, positive = lighter
		$steps = max(-255, min(255, $steps));

		// Format the hex color string
		$hex = str_replace('#', '', $hex);
		if (strlen($hex) == 3) {
		$hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
		}

		// Get decimal values
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));

		// Adjust number of steps and keep it inside 0 to 255
		$r = max(0,min(255,$r + $steps));
		$g = max(0,min(255,$g + $steps));
		$b = max(0,min(255,$b + $steps));

		$r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
		$g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
		$b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

		return '#'.$r_hex.$g_hex.$b_hex;
	}

	/**
	* Return font css import
	*/
	function import_fonts(){
	$css = '';
	$heading = str_replace("'", "", stripslashes(wolf_get_theme_option('title_font')) );
	$menu = str_replace("'", "", stripslashes(wolf_get_theme_option('menu_font')) );
	$body = str_replace("'", "", stripslashes(wolf_get_theme_option('body_font')) );


	$font = array(
		"CrimsonItalic" => 'Crimson-Italic-webfont',
		"ProclamateLight" => 'proclamate_light-webfont',
		"ProclamateHeavy" => 'proclamate_heavy-webfont',
		"CamboRegular" => 'Cambo-Regular-webfont',
		"UbuntuCondensedRegular" => 'unbuntucondensed-webfont',
		"LeagueGothicRegular" => 'League_Gothic-webfont',
		"LeanderRegular" => 'Leander-webfont',
		"Lobster13Regular" => "Lobster_1.3-webfont",
		"GermaniaOneRegular" => "germaniaone-webfont",
		"BebasRegular" => "BebasNeue-webfont",
		"BlackoutMidnight" => "Blackout-Midnight-webfont",
		"BoycottRegular" => "BOYCOTT_-webfont",
		"OstrichSansMedium" => "ostrich-regular-webfont",
		"AftaserifRegular" => "AftaSerifThin-Regular-webfont",
		"AurulentSansRegular" => "AurulentSans-Regular-webfont",
		"DroidSerifRegular" => "DroidSerif-Regular-webfont",
		"HillHouseMedium" => "Hill_House-webfont",
		"MisoRegular" => "miso-regular-webfont",
		"NobileRegular" => "nobile-webfont"
	);

	$font_list = array_keys($font);

	$is_heading_google_font = wolf_get_theme_option( 'heading_google_font_code' );
	$is_menu_google_font = wolf_get_theme_option( 'menu_google_font_code' );
	$is_body_google_font = wolf_get_theme_option( 'body_google_font_code' );

	if( in_array($heading, $font_list ) && ! $is_heading_google_font ) {

	$css .= "@font-face {
			font-family: '".$heading."';
			src: url('".WOLF_THEME_URL."/css/fonts/".$font[$heading].".eot');
			src: url('".WOLF_THEME_URL."/css/fonts/".$font[$heading].".eot?#iefix') format('embedded-opentype'),
			url('".WOLF_THEME_URL."/css/fonts/".$font[$heading].".woff') format('woff'),
			url('".WOLF_THEME_URL."/css/fonts/".$font[$heading].".ttf') format('truetype'),
			url('".WOLF_THEME_URL."/css/fonts/".$font[$heading].".svg#".$heading."') format('svg');
			font-weight: normal;
			font-style: normal;
		}" . "\n";
	}

	if( (in_array($menu, $font_list )) && ( $menu !=$heading || $is_heading_google_font )  && ! $is_menu_google_font ){

		$css .= "@font-face {
			font-family: '".$menu."';
			src: url('".WOLF_THEME_URL."/css/fonts/".$font[$menu].".eot');
			src: url('".WOLF_THEME_URL."/css/fonts/".$font[$menu].".eot?#iefix') format('embedded-opentype'),
			url('".WOLF_THEME_URL."/css/fonts/".$font[$menu].".woff') format('woff'),
			url('".WOLF_THEME_URL."/css/fonts/".$font[$menu].".ttf') format('truetype'),
			url('".WOLF_THEME_URL."/css/fonts/".$font[$menu].".svg#".$menu."') format('svg');
			font-weight: normal;
			font-style: normal;
		}". "\n";


	}


  		return $css;
	// end function
	}


	function get_bg_options($id = null, $selector = null) {

		$css = '';

		$img = wolf_get_theme_option($id.'_img');
		$color = wolf_get_theme_option($id.'_color');
		$repeat = wolf_get_theme_option($id.'_repeat');
		$position = wolf_get_theme_option($id.'_position');
		$attachment = wolf_get_theme_option($id.'_attachment');
		$size = wolf_get_theme_option($id.'_size');
		$parallax = wolf_get_theme_option( $id . '_parallax' );


		/************************************/
		if ( wolf_bg_fallback( $img ) )
			$img = wolf_bg_fallback( $img );
		/************************************/


		if ( $color && ! $img )
			$css .= "$selector {background-image:none!important;}";

		if ( $img )
			$img = 'url("'. $img .'")';

		if ( $color || $img ) {

			if ( ! $img ) {
				$css .= "$selector {background-color:$color;}";
			}


			if ( $img )  {

				if ( $parallax ) {

					$css .= "$selector {background : $color $img $repeat fixed}";
					$css .= "$selector {background-position : 50% 0}";

				} else {
					$css .= "$selector {background : $color $img $position $repeat $attachment}";
				}

				if ( $size == 'cover' ) {

					$css .= "$selector {
						-webkit-background-size: 100%;
						-o-background-size: 100%;
						-moz-background-size: 100%;
						background-size: 100%;
						-webkit-background-size: cover;
						-o-background-size: cover;
						background-size: cover;
					}";
				}

				if ( $size == 'resize' ) {

					$css .= "$selector {
						-webkit-background-size: 100%;
						-o-background-size: 100%;
						-moz-background-size: 100%;
						background-size: 100%;
					}";
				}

			}
		}

		return $css;

	}


	function css_options(){
		$css = '';

		/*-----------------------------------------------------------------------------------*/
		/*  Backgrounds
		/*-----------------------------------------------------------------------------------*/

		$body = 'body, body.dark, body.light, body.grunge';

		$bgs = array(
			'body_bg' => $body,
			'header' => '.dark header#masthead, .light header#masthead, .grunge header#masthead',
			'wrapper' => '.dark.wrapped #page, .light.wrapped #page, .grunge.wrapped #page',
			'headline' => '.dark #top-holder, .light #top-holder, .grunge #top-holder',
			'bottomline' => '.dark #bottom-holder, .light #bottom-holder, .grunge #bottom-holder',
			'footer_socials' => '.wolf-music-social-icons-container',
			'footer' => '.dark footer#colophon, .light footer#colophon, .grunge footer#colophon'
		);

		foreach($bgs as $k => $v){
			$css .= $this->get_bg_options($k, $v);
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Body
		/*-----------------------------------------------------------------------------------*/

		if( wolf_get_theme_option('wrapper_color') || wolf_get_theme_option('wrapper_img')){

			$css .= '.dark.wrapped #page, .grunge.wrapped #page, .light.wrapped #page {
				box-shadow:none;
				-moz-box-shadow:none;
				  -webkit-box-shadow: none;
				  -o-box-shadow:none;

			}';

		}

		/*-----------------------------------------------------------------------------------*/
		/*  Headline & bottomline
		/*-----------------------------------------------------------------------------------*/

		if( wolf_get_theme_option('headline_color') || wolf_get_theme_option('headline_img')){

			$css .= 'body #top-holder, body.dark #top-holder, body.light #top-holder, body.grunge #top-holder {border:none!important }';

		}

		if( wolf_get_theme_option('bottomline_color') || wolf_get_theme_option('bottomline_img')){

			$css .= 'body #bottom-holder, body.dark #bottom-holder, body.light #bottom-holder, body.grunge #bottom-holder {border:none!important }';

		}


		if(wolf_get_theme_option('header') == 'none'){
			$css .= '#hello {display:none}';
		}



		/*-----------------------------------------------------------------------------------*/
		/*  Font colors
		/*-----------------------------------------------------------------------------------*/

		if(wolf_get_theme_option('custom_color')){

			$accent = wolf_get_theme_option( 'custom_color' );
		        /* Main custom color */
		       $css .="a, a:visited,  table a
		       	{color:$accent }

			 .main-navigation .nav-menu li a:hover,
			 .light .main-navigation .nav-menu li a:hover,  .site-navigation .current_page_item a,
			 .entry-meta a:hover,
			body.light .site-navigation a:hover,  body.light .site-navigation .current_page_item a,
			#primary-menu li ul li a:hover,
			 .post .entry-link:hover, .release .entry-link:hover, table a,
		       	span.comment-author a.url:hover,
		       	table a:hover,
		       	span.themecolor,
		       	.wolf-show-entry-link:hover,
			.wolf-show-flyer:hover,
			.entry-title a:hover,
		       	.album-thumb p {color:$accent!important}

		       	.mobile-navigation li a:visited{ color:white!important;}
			.mobile-navigation li a:hover{ color:$accent!important;}

			::-moz-selection,  span.dropcap-bg{ background: $accent ;}
			::selection { background:$accent ;}
			a:link { -webkit-tap-highlight-color:$accent  ; }

			a.comment-bubble-link:hover,
			a.more-link:hover, a.buy-button:hover, input[type=submit]:hover,
			a.more-link:focus, a.buy-button:focus, input[type=submit]:focus,
			a.theme-button:hover,
			a.theme-button:focus,
			.button:hover,
			.button:focus,
			.wolf-show-actions a:hover,
			.wolf-show-actions a:focus
			{background-color:$accent!important}

			ul.page-numbers li .page-numbers.current
			{background-color: $accent!important;}
			ul.page-numbers li .page-numbers.current{border-color:$accent}";

			/* Button gradient */
$accent_rgba = $this->hex_to_rgb($accent);
$css .= "
.wolf-woocommerce-buttons.woocommerce .button.alt,
.wolf-woocommerce-buttons.woocommerce #content input.button.alt,
.wolf-woocommerce-buttons.woocommerce #respond input#submit.alt,
.wolf-woocommerce-buttons.woocommerce-page .button.alt,
.wolf-woocommerce-buttons.woocommerce-page #content input.button.alt,
.wolf-woocommerce-buttons.woocommerce-page #respond input#submit.alt,
.wolf-widget-area .wolf_widget_col_12.widget_mailchimpsf_widget #mc_signup .mc_signup_submit input[type=\"submit\"]
{
border:1px solid " . $this->brightness($accent, -10) . "!important;
text-shadow:0px 0px 5px " . $this->brightness($accent, -10) . "!important;
background: $accent;
background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, $accent), color-stop(100%, " . $this->brightness( $accent, -10 ) . ")!important);
background-image: -webkit-linear-gradient($accent, " . $this->brightness( $accent, -10 ) . ")!important;
background-image: -moz-linear-gradient($accent, " . $this->brightness( $accent, -10 ) . ")!important;
background-image: -o-linear-gradient($accent, " . $this->brightness( $accent, -10 ) . ")!important;
background-image: linear-gradient($accent, " . $this->brightness( $accent, -10 ) . ")!important;
}

.wolf-woocommerce-buttons.woocommerce .button.alt:hover,
.wolf-woocommerce-buttons.woocommerce #content input.button.alt:hover,
.wolf-woocommerce-buttons.woocommerce #respond input#submit.alt:hover,
.wolf-woocommerce-buttons.woocommerce-page .button.alt:hover,
.wolf-woocommerce-buttons.woocommerce-page #content input.button.alt:hover,
.wolf-woocommerce-buttons.woocommerce-page #respond input#submit.alt:hover,
.wolf-woocommerce-buttons.woocommerce .button.alt:focus,
.wolf-woocommerce-buttons.woocommerce #content input.button.alt:focus,
.wolf-woocommerce-buttons.woocommerce #respond input#submit.alt:focus, .wolf-woocommerce-buttons.woocommerce-page .button.alt:focus,
.wolf-woocommerce-buttons.woocommerce-page #content input.button.alt:focus,
.wolf-woocommerce-buttons.woocommerce-page #respond input#submit.alt:focus,
.wolf-widget-area .wolf_widget_col_12.widget_mailchimpsf_widget #mc_signup .mc_signup_submit input[type=\"submit\"]:hover,
.wolf-widget-area .wolf_widget_col_12.widget_mailchimpsf_widget #mc_signup .mc_signup_submit input[type=\"submit\"]:focus{
	background: " . $this->brightness( $accent, 5 ) . "!important;
}

.wolf-woocommerce-buttons.woocommerce .button.alt:active,
.wolf-woocommerce-buttons.woocommerce #content input.button.alt:active,
.wolf-woocommerce-buttons.woocommerce #respond input#submit.alt:active,
.wolf-woocommerce-buttons.woocommerce-page .button.alt:active,
.wolf-woocommerce-buttons.woocommerce-page #content input.button.alt:active,
.wolf-woocommerce-buttons.woocommerce-page #respond input#submit.alt:active,
.wolf-widget-area .wolf_widget_col_12.widget_mailchimpsf_widget #mc_signup .mc_signup_submit input[type=\"submit\"]:active{
	background: $accent;
}


.wolf-woocommerce-buttons.woocommerce .widget_price_filter .ui-slider .ui-slider-range,
.wolf-woocommerce-buttons.woocommerce-page .widget_price_filter .ui-slider .ui-slider-range{
	background: $accent;
}

.wolf-woocommerce-buttons.woocommerce .widget_price_filter .ui-slider .ui-slider-handle,
.wolf-woocommerce-buttons.woocommerce-page .widget_price_filter .ui-slider .ui-slider-handle{
	background: " . $this->brightness( $accent, -10 ) . ";
}

.wolf-woocommerce.woocommerce div.product .woocommerce-tabs .panel,
.wolf-woocommerce.woocommerce #content div.product .woocommerce-tabs .panel,
.wolf-woocommerce.woocommerce-page div.product .woocommerce-tabs .panel,
.wolf-woocommerce.woocommerce-page #content div.product .woocommerce-tabs .panel{
	background-color:$accent!important;
}

.wolf-woocommerce.woocommerce div.product .woocommerce-tabs ul.tabs li.active,
.wolf-woocommerce.woocommerce #content div.product .woocommerce-tabs ul.tabs li.active,
.wolf-woocommerce.woocommerce-page div.product .woocommerce-tabs ul.tabs li.active,
.wolf-woocommerce.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li.active{
	background: $accent;
}

";
		}



		/*-----------------------------------------------------------------------------------*/
		/*  Fonts
		/*-----------------------------------------------------------------------------------*/

		$is_heading_google_font = wolf_get_theme_option( 'heading_google_font_code' );
		$is_menu_google_font = wolf_get_theme_option( 'menu_google_font_code' );
		$is_body_google_font = wolf_get_theme_option( 'body_google_font_code' );

		/* Body font
		-----------------------------*/
		if(wolf_get_theme_option('body_font') && ! $is_body_google_font ){
			$css .=   "body {font-family: ".stripslashes(wolf_get_theme_option('body_font'))."}";

		}

		/* Heading font
		-----------------------------*/

		if(wolf_get_theme_option('title_font') && ! $is_heading_google_font){
		           $css .= "h1, h2, h3, h4, h5, h6, span.comment-author, span.dropcap,
		           span.dropcap-bg {font-family:".stripslashes(wolf_get_theme_option('title_font'))."}". "\n";

		            if( stripslashes(wolf_get_theme_option('title_font')) == "'OstrichSansMedium'" ){
			             $css .= "
			             h1, h2, h3, h4, h5, h6{ line-height:1.2em; margin-bottom: 0.5em }
				h1 { font-size:430%; }
				h2, .post h1.entry-title { font-size:350%; }
				h3 { font-size:300%; }h4 { font-size:260% } h5 { font-size:210% }
				h6 { font-size:180% }". "\n";
		            }

		 }
		if(wolf_get_theme_option('title_font_transform') && ! $is_heading_google_font){
           			$css .="h1, h2, h3, h4, h5, h6 {   text-transform: ".wolf_get_theme_option('title_font_transform')."  }";
		}

		 if(wolf_get_theme_option('menu_font') && ! $is_heading_google_font){
		          $css .=".site-navigation a, #mobile-menu-dropdown li { font-weight:normal; font-family:".stripslashes(wolf_get_theme_option('menu_font')).";}";
		            if( stripslashes(wolf_get_theme_option('menu_font'))=="'Helvetica Neue', Helvetica, Arial, sans-serif"  ){
			            	$css .=".site-navigation a, #mobile-menu-dropdown li {font-weight:bold; font-size:14px!important;}";
		            }
		 }
		 if(wolf_get_theme_option('menu_font_transform') && ! $is_heading_google_font){
		            $css .=".site-navigation a, #mobile-menu-dropdown li {   text-transform:  ".wolf_get_theme_option('menu_font_transform')." ;  }";
		 }


		/*Header*/

		if(wolf_get_theme_option('static_header_padding')){
		        $css .="#fixheader-container
		        { padding: ".(wolf_get_theme_option('static_header_padding')+5)."px 0 ".wolf_get_theme_option('static_header_padding')."px }";
		}



		if(wolf_get_theme_option('sidebar_position') == 'left'){
		        $css .="#primary {float:right; margin-right: 0;} #secondary {float:left; margin-right: 3.8%;}";
		}


		/**
		* Flex slider container height to avoid jumping on load
		*
		*/
		$wolf_flexslider_settings = get_option('wolf_flexslider_settings');

		if( isset($wolf_flexslider_settings['height']) )
			$css .='#head-slider-container {height:'. $wolf_flexslider_settings['height'] .'px!important}';

		/*-----------------------------------------------------------------------------------*/
		/*  Heading Font
		/*-----------------------------------------------------------------------------------*/

		$heading_font = wolf_get_theme_option( 'heading_google_font_name' );

		if ( $heading_font ){
			$css .= "h1, h2, h3, h4, h5, h6{font-family:'$heading_font'}";
		}

		$heading_font_weight = wolf_get_theme_option( 'heading_google_font_weight' );

		if ( $heading_font_weight ){
			$css .= "h1, h2, h3, h4, h5, h6{font-weight:$heading_font_weight}";
		}

		$heading_font_transform = wolf_get_theme_option( 'heading_google_font_transform' );

		if ( $heading_font_transform == 'uppercase' ){
			$css .= "h1, h2, h3, h4, h5, h6{text-transform:uppercase}";
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Menu Font
		/*-----------------------------------------------------------------------------------*/

		$menu_font = wolf_get_theme_option( 'menu_google_font_name' );

		if( $menu_font ){
			$css .= ".site-navigation a, #mobile-menu-dropdown li{ font-family:'$menu_font'}";
		}

		$menu_font_weight = wolf_get_theme_option( 'menu_google_font_weight' );

		if( $menu_font_weight ){
			$css .= ".site-navigation a, #mobile-menu-dropdown li{font-weight:$menu_font_weight}";
		}

		$menu_font_transform = wolf_get_theme_option( 'menu_google_font_transform' );

		if ( $menu_font_transform == 'uppercase' ){
			$css .= ".site-navigation a, #mobile-menu-dropdown li{text-transform:uppercase}";
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Body Font
		/*-----------------------------------------------------------------------------------*/

		$body_font = wolf_get_theme_option( 'body_google_font_name' );

		if( $body_font ){
			$css .= "body{font-family:'$body_font'}";
		}

		/*
		Custom CSS
		*/

		 if(wolf_get_theme_option('custom_css')){
		 	$css .= stripslashes(wolf_get_theme_option('custom_css'));
		 }

		return $css;
	}



	/**
	 * Render Custom Background Style
	 */
	function render_background( $meta_id = '_post_bg', $selector = 'body' ) {

		global $post, $wp_query;
		$css = '';

		if ( ! is_404() && ! is_tax() && ! is_search() && $post ) {

			$post_id = $post->ID;
			$is_post_index = false;
			$is_page_for_posts = get_option('page_for_posts');

			if ( $wp_query && isset ( $wp_query->queried_object_id ) ) {
				if ( $is_page_for_posts && $is_page_for_posts ==  $wp_query->queried_object_id ) {
					$post_id =  $wp_query->queried_object_id;
					$is_post_index = true;
				}
			}

			if ( is_single() || is_page() || $is_post_index || is_archive() ) {

				if ( is_archive() && $is_page_for_posts )
					$post_id = get_option('page_for_posts');

				$url = null;
				$img = get_post_meta( $post_id, $meta_id . '_img', true );
				$color = get_post_meta( $post_id, $meta_id . '_color', true );
				$repeat = get_post_meta( $post_id, $meta_id . '_repeat', true );
				$position = get_post_meta( $post_id, $meta_id . '_position', true );
				$attachment = get_post_meta( $post_id, $meta_id . '_attachment', true );
				$size = get_post_meta( $post_id, $meta_id . '_size', true );
				$parallax = get_post_meta( $post_id, $meta_id . '_parallax', true );

				/************************************/
				if ( wolf_bg_fallback( $img ) )

					$img = wolf_bg_fallback( $img );

				/************************************/

				if ( $img )
					$url = 'url("'. $img .'")';

				if ( $color || $img ) {

					if ( $parallax ) {

						$css .= "$selector {background : $color $url $repeat fixed}";
						$css .= "$selector {background-position : 50% 0}";

					} else {
						$css .= "$selector {background : $color $url $position $repeat $attachment}";
					}

					if ($size == 'cover') {

							$css .= "$selector {
								-webkit-background-size: cover;
								-o-background-size: cover;
								-moz-background-size: cover;
								background-size: cover;
							}";
						}

					if ( $size == 'resize' ) {

						$css .= "$selector {
							-webkit-background-size: 100%;
							-o-background-size: 100%;
							-moz-background-size: 100%;
							background-size: 100%;
						}";
					}

				}

			}
			return $css;
		}
	} // end render background & custom CSS


	function render_custom_css() {
		global $post, $wp_query;
		$css = '';

		if ( ! is_404() && ! is_tax() && ! is_search() && $post ) {

			$post_id = $post->ID;
			$is_post_index = false;
			$is_page_for_posts = get_option('page_for_posts');

			if ( $wp_query && isset ( $wp_query->queried_object_id ) ) {
				if ( $is_page_for_posts && $is_page_for_posts ==  $wp_query->queried_object_id ) {
					$post_id =  $wp_query->queried_object_id;
					$is_post_index = true;
				}
			}

			if ( is_single() || is_page() || $is_post_index || is_archive() ) {

				if ( is_archive() && $is_page_for_posts )
					$post_id = get_option('page_for_posts');

				$custom_css = get_post_meta( $post_id, '_custom_css', true );

				if ( $custom_css ) {
					$css .= $custom_css ;
				}


			}
			return $css;
		}
	}



	function render_background_prallax( $meta_id = '_post_bg' ) {

		global $post, $wp_query;
		$js = '';

		if ( ! is_404() && ! is_tax() && ! is_search() && $post ) {

			$post_id = $post->ID;
			$is_post_index = false;
			$is_page_for_posts = get_option('page_for_posts');

			if ( $wp_query && isset ( $wp_query->queried_object_id ) ) {
				if ( $is_page_for_posts && $is_page_for_posts ==  $wp_query->queried_object_id ) {
					$post_id =  $wp_query->queried_object_id;
					$is_post_index = true;
				}
			}

			if ( is_single() || is_page() || $is_post_index || is_archive() ) {

				if ( is_archive() && $is_page_for_posts )
					$post_id = get_option('page_for_posts');

				$parallax = get_post_meta( $post_id, $meta_id . '_parallax', true );

				if ( $parallax ) {
					$selector = '#masthead';
					$js .= '$( "' . $selector . '" ).addClass( "section-parallax" );';
				}

			}

			if ( $js )
				return '<script type="text/javascript">jQuery(document).ready(function($) {' . $js . '});</script>'."\n";
		}
	}


} // end class

global $wolf_custom_css;
$wolf_custom_css = new Wolf_Custom_Styles;

} // end class check


/**
 * Output the custom CSS
 */
function wolf_output_css() {
	global $wolf_custom_css;
	$output ="\n";
	$output .= '/* Custom CSS */' . "\n";
	$output .= wolf_compact_css( $wolf_custom_css->import_fonts() );
	$output .= wolf_compact_css( $wolf_custom_css->css_options() );
	$output .="\n";
	$output .= '/* Custom Background */' . "\n";
	$output .= wolf_compact_css( $wolf_custom_css->render_background( '_header_bg', '.dark header#masthead, .light header#masthead, .grunge header#masthead' ) );
	$output .= wolf_compact_css( $wolf_custom_css->render_custom_css() );
	$output .= "\n\n";

	return '<style type="text/css">'.$output.'</style>'."\n";
}

/**
 * Parallax
 */
function wolf_output_parallax_js() {

	global $wolf_custom_css;
	$output = '';
	$output .= "\n" . '<!-- Single Post Header Background Parallax -->' . "\n";

	$output .= $wolf_custom_css->render_background_prallax( '_header_bg', '#masthead' );
	$output .= "\n\n";

	return $output;
}

function wolf_output_options_parallax_js() {

	$js = '';
	$output = '';

	$backgrounds = array(
		'header' => '#masthead',
		//'headline' => '#top-holder',
		//'bottom' => '#bottom-holder',
		// 'footer' => '#colophon',
	);

	foreach ( $backgrounds as $id => $selector ) {

		if ( wolf_get_theme_option( $id . '_parallax' ) && wolf_get_theme_option( $id . '_img' ) && is_front_page() )
			$output .= '$( "' . $selector . '" ).addClass( "section-parallax" );';
	}

	$js .= $output;

	if ( $output )
		return '<script type="text/javascript">jQuery(document).ready(function($) {' . $js . '});</script>'."\n";
}

/**
 * Output the custom CSS
 */
function wolf_render_custom_css() {
    	echo wolf_output_css();
    	echo wolf_output_options_parallax_js();
    	echo wolf_output_parallax_js();
}
add_action( 'wp_head', 'wolf_render_custom_css' );
