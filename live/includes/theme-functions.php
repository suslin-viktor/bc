<?php
/**
 * Set your language here
 *
 * Uncomment the line above the function to make the wolf_set_lang function active
 * Replace the languague code by your own
 * The .po and .mo files can be found in the languages/ folder
*/

if ( ! function_exists( 'wolf_set_lang' ) ) :
//add_filter( 'locale', 'wolf_set_lang' );
function wolf_set_lang( $locale ) {
	return 'fr_FR';
}
endif;

/**
 * Enqueue CSS stylsheets
 * JS scripts are separated and can be found in includes/scripts.php 
 */
function wolf_enqueue_style() {
	global $wp_styles;

	$lightbox = wolf_get_theme_option( 'lightbox' );

	if ( $lightbox == 'swipebox' ) {

		wp_enqueue_style( 'swipebox', WOLF_THEME_URL. '/css/lib/swipebox.min.css', array(), '1.2.8' );

	} elseif ( $lightbox == 'fancybox' ) {

		wp_enqueue_style( 'fancybox', WOLF_THEME_URL. '/css/lib/fancybox.css', array(), '2.1.4' );

	}

	wp_enqueue_style( 'flexslider', WOLF_THEME_URL. '/css/lib/flexslider.css', array(), '2.2.0' );

	/* Main Stylesheet */
	wp_enqueue_style( 'live-style', get_stylesheet_uri(), array(), WOLF_THEME_VERSION );
	

}
add_action( 'wp_print_styles', 'wolf_enqueue_style' );


if ( ! function_exists( 'wolf_body_classes' ) ) :
/**
 * Add specific class to the body depending on theme options
 */
function wolf_body_classes( $classes ) {

	global $wp_customize;

	if ( isset( $wp_customize ) ) {
		$classes[] = 'is-customizer';
	}

	$classes[] = 'wolf wolf-woocommerce wolf-woocommerce-buttons';

	/* Page template clean classes */
	if ( is_page_template( 'page-templates/full-width.php' ) )
		$classes[] = 'full-width';

	/* Layout Class (wrapped or full width container) */
	$classes[] = wolf_get_theme_option( 'page_container' );

	$classes[] = wolf_get_theme_option( 'skin' );

	$classes[] = 'sidebar-' . wolf_get_theme_option( 'sidebar_position' );

	if ( wolf_get_theme_option( 'sticky_menu' ) )
		$classes[] = 'sticky-menu';

	if ( is_multi_author() )
		$classes[] = 'is-multi-author';

	return $classes;
}
add_filter( 'body_class', 'wolf_body_classes' );
endif;


if ( ! function_exists( 'wolf_logo' ) ) :
/**
 * Display the Logo
 */
function wolf_logo() {

	$logo = get_stylesheet_directory_uri() . '/images/default_logo.png';

	$theme_slug = WOLF_THE_THEME;
	$uploads = wp_upload_dir();
	$old_uploads_dir = $uploads['basedir'] . '/' . $theme_slug . '/bd-uploads';
	$old_uploads_url = $uploads['baseurl'] . '/' . $theme_slug . '/bd-uploads/';

	if ( wolf_get_theme_option('logo') && is_file( $old_uploads_dir . '/' . wolf_get_theme_option('logo') ) ) 
		$logo = $old_uploads_url . wolf_get_theme_option('logo');
	elseif( wolf_get_theme_option('logo') )
		$logo = wolf_get_theme_option( 'logo' );

	if ( $logo ) {
		
		$output = '<div id="logo-container"><div id="logo">
		<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">
			<img src="' . $logo . '" alt="' . esc_attr( get_bloginfo( 'name') ) . '">
		</a>
		</div></div>';
		echo $output;
	}
}
endif;

if ( ! function_exists( 'wolf_excerpt_length' ) ) :
/**
 * Excerpt length hook 
 * Set the number of character to display in the excerpt
 */
function wolf_excerpt_length($length) {
	return 50; 
}
add_filter( 'excerpt_length', 'wolf_excerpt_length' );
endif;



if ( ! function_exists( 'wolf_more_text' ) ) :
/**
 * Excerpt more
 * Render "Read more" link text differenttly depending on post format
 * the_content( wolf_more_text() )
 */
function wolf_more_text() {
	global $post;

	$format = null;
	$text = __('Read more &rarr;', 'wolf');
	$format = get_post_format();
	
	if ( $format ) {

		if ( $format == 'video' ) {

		 	$text = __( 'More about this video &rarr;', 'wolf' );

		}elseif ( $format == 'gallery' ||  $format == 'image' ) {

			$text = __( 'View more &rarr;', 'wolf' );

		}elseif ( $format == 'audio' ) {

			$text = __( 'More about this song &rarr;', 'wolf' );

		}else{
			$text = __( 'Read more &rarr;', 'wolf' );
		}
	}

	return $text;
       
}
endif;

if ( ! function_exists( 'wolf_output_title' ) ) :
/**
 * Display Page Title
 */
function wolf_output_title() {

	global $wp_query;

	$is_blog = $wp_query && isset( $wp_query->queried_object->ID ) 
		&& $wp_query->queried_object->ID == get_option( 'page_for_posts' );

	if ( 
		(
			! is_page() && ! $is_blog
			|| ( ( is_page() || $is_blog ) && wolf_get_theme_option( 'show_title' ) )
		)

		&& ! is_single()
		&& ! is_singular( 'gallery' ) && ! is_singular( 'video' ) && ! is_singular( 'release' ) && ! is_singular( 'show' ) && ! is_singular( 'product' )

	) {
		echo '<header class="page-header">';
		echo wolf_get_page_title();
		echo '</header>';
	}
		

}
add_action( 'wolf_page_before', 'wolf_output_title' );
endif;



if ( ! function_exists( 'wolf_excerpt_more' ) ) :
/**
 * Excerpt more
 */
function wolf_excerpt_more( $more ) {
	
	return '<p><a rel="bookmark" class="more-link" href="'. get_permalink( get_the_ID() ) . '">' . wolf_more_text() . '</a></p>';
       
}
add_filter( 'excerpt_more', 'wolf_excerpt_more' );
endif;


if( ! function_exists( 'wolf_remove_more_jump_link' ) ) :
/**
 * Avoid page jump when clicking on more link
 */
function wolf_remove_more_jump_link( $link )  {
	$offset = strpos( $link, '#more-' );
	if ( $offset ) { $end = strpos( $link, '"',$offset ); }
	if ( $end ) { $link = substr_replace( $link, '', $offset, $end-$offset ); }
	return $link;
}
add_filter( 'the_content_more_link', 'wolf_remove_more_jump_link' );
endif;


if ( ! function_exists('wolf_is_woocommerce') ) :
/**
 * Check if we are on a woocommerce page
 */
function wolf_is_woocommerce() {

	if ( class_exists( 'Woocommerce' ) ) {

		if ( is_woocommerce() ) {
			return true;
		}

		if ( is_checkout() || is_order_received_page() ) {
			return true;
		}

		if ( is_cart() ) {
			return true;
		}

		if ( is_account_page() ) {
			return true;
		}

	}

}
endif;


if ( ! function_exists( 'wolf_get_woocommerce_shop_page_id' ) ) :
/**
 * Check if we are on a woocommerce page
 */
function wolf_get_woocommerce_shop_page_id() {

	$page_id = null;

	//debug( get_option( 'woocommerce_shop_page_id' ) );

	if ( class_exists( 'Woocommerce' ) ) {

		$page_id = get_option( 'woocommerce_shop_page_id' );
	}

	return $page_id;

}
endif;



if ( ! function_exists( 'wolf_get_page_title' ) ) :
/**
 * Returns page title outside the loop
 */
function wolf_get_page_title() {
	
	global $post, $wp_query;
	$title = null;
	$desc = null;
	$output = null;

	if ( have_posts() ) :
		
		/* Main condition not 404 and not woocommerce page */
		if ( 
			! is_404()
			&& ! wolf_is_woocommerce() 
			&& ! is_singular( 'release' ) 
			&& ! is_singular( 'show' )
			&& ! is_singular( 'gallery' ) 
		) :

			if ( is_category() ) {
				
				$title = single_cat_title( '', false );
				$desc = category_description();
					
			} elseif ( is_tag() ) {
				
				$title = single_tag_title( '', false );
				$desc = category_description();

			} elseif ( is_author() ) {
				
				the_post();
				$title = get_the_author();
				rewind_posts();

			} elseif ( is_day() ) {
				
				get_the_date();

			} elseif ( is_month() ) {
				
				$title = get_the_date( 'F Y' );

			} elseif ( is_year() ) {
				
				$title = get_the_date( 'Y' );

			} elseif ( is_tax() ) {
				$the_tax = get_taxonomy( get_query_var( 'taxonomy' ) );
				if( $the_tax && $wp_query && isset($wp_query->queried_object->name) ) {

					$title = $wp_query->queried_object->name;
					$desc = category_description();
					
				}

			} elseif( is_search() ) {
			
				$title = sprintf( __( 'Search Results for: %s', 'wolf' ), get_search_query());

			} elseif ( is_single() ) {
				
				$format = get_post_format();
				
				if ( $format != 'quote' 
				&& $format != 'aside'
				&& $format != 'status' 
				&& $format != 'link' 
				) {
					$title = get_the_title(); 
				}
				
				
			/* is blog index */
			} elseif (
				$wp_query && isset( $wp_query->queried_object->ID ) 
				&& $wp_query->queried_object->ID == get_option( 'page_for_posts' )
			 ) {
			
				$title = $wp_query->queried_object->post_title;
				$desc = wolf_get_theme_option( 'blog_tagline' ); // blog tagline from theme options

			} elseif ( $wp_query && isset( $wp_query->queried_object->ID )  ) {
			
				$title = $wp_query->queried_object->post_title;
			}
		
		endif; // if not 404
	endif; // end if have posts

	if ( $title ) 
		$output .= "<h1 class='page-title'>$title</h1>";

	if ( $title ) 
		$output .= "<div class='category-description'>$desc</div>";

	return $output;
}
endif;

/*-----------------------------------------------------------------------------------*/
/*	Page Layout
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'wolf_top_holder' ) ) :
/**
 * Output top holder area (mainly the page title container)
 */
function wolf_top_holder() {

	$content = wolf_get_meta_data( 'headline', '_top_holder' );
	
	if ( $content ) :
	?>
	<section id="top-holder" class="clearfix">
		<div class="wrap"><?php echo wolf_format_custom_content_output( stripslashes( $content ) ); ?></div>
	</section>
	<?php
	endif;
	
}
add_action( 'wolf_header_after', 'wolf_top_holder' );
endif;




if ( ! function_exists( 'wolf_bottom_holder' ) ) :
/**
 * Output bottom holder area
 */
function wolf_bottom_holder() {


	$content = wolf_get_meta_data( 'bottomline',  '_bottom_holder' );

	if ( $content ) :
	?>
	<section id="bottom-holder" class="clearfix">
		<div class="wrap"><?php echo wolf_format_custom_content_output( stripslashes( $content ) ); ?></div>
	</section>
	<?php
	endif;
}
// add_action( 'wolf_page_after', 'wolf_bottom_holder' );
endif;

if ( ! function_exists( 'wolf_output_music_network' ) ) :
/**
 * Output music network icons
 */
function wolf_output_music_network() {
	
	if ( function_exists( 'wolf_music_network' ) ) {
		echo '<div class="wolf-music-social-icons-container">';
		wolf_music_network( '32', 'center' );
		echo '</div>';
	}
	
}
add_action( 'wolf_footer_end', 'wolf_output_music_network' );
endif;



if ( ! function_exists( 'wolf_get_meta_data' ) ) :
/**
 * Output a meta
 */
function wolf_get_meta_data( $option_id = null, $meta_id = null ) {

	global $wp_query;
	$post_id = null;
	
	/* By default we pick the bottom message from theme custom options */
	$data = wolf_get_theme_option( $option_id );

	if ( ! $meta_id )
		$meta_id = '_' . $id;

	if ( 
		have_posts() 
		&& $wp_query 
		&& isset( $wp_query->queried_object->ID )
		&& $wp_query->queried_object->ID != 0
	)
		$post_id = $wp_query->queried_object->ID;

	if ( ! is_404() && ! is_search() ) { // if no 404 or search

		$custom_types = array(

			'video' => array(
				'name' => 'video',
				'tax' => array( 'video_type'),
				'template_name' => 'videos'
			),

			'gallery' => array(
				'name' => 'gallery',
				'tax' => array( 'gallery_type' ),
				'template_name' => 'albums'
			),

			'release' => array(
				'name' => 'release',
				'tax' => array( 'label', 'band' ),
				'template_name' => 'discography'
			),

			'show' => array(
				'name' => 'show',
				'tax' => array(),
				'template_name' => ''
			)

		);

		foreach ( $custom_types as $key => $value ) {
			
			$index_page_id = wolf_get_custom_index_page_id( $value['template_name'] );

			foreach ( $value['tax'] as $tax ) {

				/* If we are on a taxonomy page, we display the data from the post type index page if set */
				if (
					$index_page_id 
					&& get_post_meta( $index_page_id , $meta_id, true )
					&& is_tax( $tax ) 
				) {
					$data = get_post_meta( $index_page_id , $meta_id, true );
				}
			}

			/* If we are on a single post_type page, and index page data if set and the single custom post
			 *  does not have a data set, we display the index page data  
			 */
			if (
				$post_id && $index_page_id  && get_post_meta( $index_page_id  , $meta_id, true )
				&& ! get_post_meta( $post_id, $meta_id, true )
				&& is_singular( $value['name'] )
			) {
				$data = get_post_meta( $index_page_id , $meta_id, true );
			}

		}

		/* WooCommerce 
		---------------------------------------*/
		if ( 
			wolf_is_woocommerce() 
			&& is_archive( 'product' ) 
		) {

			$data = get_post_meta( wolf_get_woocommerce_shop_page_id() , $meta_id, true );

		}

	
		/* Blog 
		---------------------------------------*/
		/* If we are on an archive page, we display the blog data if set */
		if ( get_post_type() == 'post' && is_archive() && get_option('page_for_posts') && get_post_meta( get_option('page_for_posts') , $meta_id, true ) ){

			$data = get_post_meta( get_option('page_for_posts') , $meta_id, true );

		}

		/* If we're on a single blog post, a blog index data is set and no single post data found, we display the blog data */
		elseif ( 
			$post_id 
			&& get_post_type() == 'post'
			&& get_option( 'page_for_posts' ) 
			&& is_single() 
			&& ! get_post_meta( $post_id, $meta_id, true )  ) {

			$data = get_post_meta( get_option( 'page_for_posts' ) , $meta_id, true );
		}

		/* If a custom data post meta if set for a post */
		elseif ( $post_id && get_post_meta( $post_id, $meta_id, true ) ){
			$data = get_post_meta( $post_id, $meta_id, true );
		}
	}
	
	if ( $data && ! is_404() && ! is_search() ) {
		// return wolf_format_custom_content_output( stripslashes( $data ) );
		return $data;
	}
	

}
endif;

if ( ! function_exists( 'wolf_get_custom_index_page_id' ) ) :
/**
 * Returns page ID of specific post types index page if exists
 */
function wolf_get_custom_index_page_id( $name = '' ){
	
	$pages = get_pages( array(
		'meta_key' => '_wp_page_template',
		'meta_value' => $name . '-template.php'
	) );
	if ( $name && $pages && isset( $pages[0] ) )
		return $pages[0]->ID;
}
endif;



/**
* Excerpt more
* Render "Read more" button
*/
function wolf_excerpt_more($more) {
	global $post;

	$class = 'more-link';
	
	if(wolf_get_theme_option('read_more_class'))
		$class = wolf_get_theme_option('read_more_class');

	$text = __('Read more', 'wolf').'';

	if(wolf_get_theme_option('read_more_txt')!='')
		$text = wolf_get_theme_option('read_more_txt');

	return '...<p>
	<a href="'. get_permalink($post->ID) . '" class="'.$class.'">'.$text.'</a>
	</p>';
       
}
add_filter('excerpt_more', 'wolf_excerpt_more');



/**
* Avoid page jump when clicking on more link
*/
add_filter('the_content_more_link', 'wolf_remove_more_jump_link');
function wolf_remove_more_jump_link($link)  {
	$offset = strpos($link, '#more-');
	if ($offset) { $end = strpos($link, '"',$offset); }
	if ($end) { $link = substr_replace($link, '', $offset, $end-$offset); }
	return $link;
}


if ( ! function_exists( 'wolf_single_content_filter' ) ) :
/**
 * Excludes featured media from content.
 * The featured media will be displayed at the top in single page
 */
function wolf_single_content_filter( $content ) { 

	$slider = null;

	$pattern = get_shortcode_regex();

	if ( preg_match( "/$pattern/s", get_the_content(), $match ) ) {
		
		if ( $match[0] && isset( $match[2] ) && 'gallery' == $match[2] ) {
			$slider = $match[0];
		}

	}

	if ( 'post' == get_post_type() && has_post_format( 'gallery' ) ) {

		$content = wolf_format_custom_content_output( str_replace( $slider, '', get_the_content() ) );
	
	}

	return $content;

}
//add_filter( 'the_content', 'wolf_single_content_filter' );
endif;



if ( ! function_exists( 'wolf_custom_gallery' ) ) :
/**
 * Custom Wordpress gallery shortcode output
 * Renders WP gallery differently depending on context (masonry gallery, slider, default)
 */
add_filter( 'use_default_gallery_style', '__return_false' );
add_filter( 'post_gallery', 'wolf_custom_gallery', 10, 2 );
function wolf_custom_gallery( $output, $attr) {
	global $post, $wp_locale;
	$post_id = get_the_ID();
	$is_masonry_gallery = get_post_type( $post_id ) == 'gallery';
	$is_slider = get_post_format( $post_id ) == 'gallery';
	$is_default = ! $is_slider && ! $is_masonry_gallery;

	$size = 'thumbnail';

	if ( $is_slider )
		$size = 'default';

	if ( $is_masonry_gallery )
		$size = 'masonry-thumb';

	static $instance = 0;
	$instance++;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		
		if ( ! $attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post_id,
		'itemtag'    => 'li',
		'icontag'    => 'div',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => $size,
		'include'    => '',
		'exclude'    => ''
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}

	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
		$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$rand = rand(1,999);
	$gallery_id = $post_id.$rand;
	$selector = "gallery-$gallery_id";

	$class = "clearfix gallery default-gallery";

	if ( $is_masonry_gallery )
		$class = "clearfix gallery masonry-gallery";

	$open_tag = "<div id='$selector' class='$class'><ul>";

	if ( $is_slider )
		$open_tag = "<div id='$selector' class='clearfix flexslider wolf-gallery-slider'><ul class='slides'>";

	$gallery_style = '';

	if ( $is_default  ){
		$gallery_style = "<style type='text/css'>
	            #{$selector}.default-gallery {
	                margin: auto;
	                margin-bottom:1em;
	            }
	            #{$selector}.default-gallery ul li {
	                float: {$float};
	                margin-top: 10px;
	                text-align: center;
	                width: {$itemwidth}%;           }
	            #{$selector}.default-gallery img {
	                border: 2px solid #cfcfcf;
	            }
	            #{$selector}.default-gallery .gallery-caption {
	                margin-left: 0;
	            }
	        </style>";
	}

	$output = apply_filters('gallery_style', "$gallery_style
	<!-- see gallery_shortcode() in wp-includes/media.php -->
	$open_tag");

	//debug( $size );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		
		$caption = '';
		$link_class = '';
		$data_thumb_attr = '';
		$fallback = '';
		$img = wp_get_attachment_image_src( $id, $size, false, false );
		$full = wp_get_attachment_image_src( $id, 'full', false, false );
		$square = wp_get_attachment_image_src( $id, 'small-square', false, false );

		if ( $captiontag && trim($attachment->post_excerpt) )
			$caption = wptexturize($attachment->post_excerpt);

		if ( $img[0] && $full[0] && $square[0] ){
			
			$src = $img[0];
			$full_size = $full[0];
			
			//if ( $data_thumb[0] && $is_slider ){
			//	$data_thumb_attr = ' data-thumb="'. $data_thumb[0] .'"';
			//}

			if ( ! $is_slider ){
			
				$link_class = ' class="lightbox"';
			
			}else{
				$link_class = '';
			}
				
			
			$img = '<a'. $link_class .' title="'. $caption .'" href="'. $full_size .'" rel="lightbox[gallery-' . $post->ID . ']">
<img src="' . $src . '" alt="'. wptexturize( $attachment->post_title ) .'"></a>';
		}

		$link = isset($attr['link']) && 'post' == $attr['link'] ? wp_get_attachment_link($id, $size, true, false) : $img;
		
		if ( $is_slider ){
			$output .= "<{$itemtag} class='slide'";
			
			//$output .= $data_thumb_attr;
			
			$output .= ">";
			
		}else{
			$output .= "<{$itemtag}>";
		}

		$output .= "$link";
			
		if ( $caption && $is_slider )
			$output .= "<p class='flex-caption'>$caption</p>";
		
		$output .= "</{$itemtag}>";
	}


	$output .= "</ul></div>\n";


	return $output;
}
endif;


/* Ajax */

if ( ! function_exists( 'wolf_get_video_url_from_post_id' ) ) :
/**
 * 
 */
function wolf_get_video_url_from_post_id() {

	extract( $_POST );

	$post_id = $_POST['id'];
	$content_post = get_post($post_id);
	$content = $content_post->post_content;

	$has_video_url = 
	// youtube
	preg_match( '#(?:\www.)?\youtube.com/watch\?v=([A-Za-z0-9\-_]+)#', $content, $match )
	|| preg_match( '#(?:\www.)?\youtu.be/([A-Za-z0-9\-_]+)#', $content, $match )
	
	// vimeo
	|| preg_match( '#vimeo\.com/([0-9]+)#', $content, $match )

	// other
	|| preg_match( '#http://blip.tv/.*#', $content, $match )
	|| preg_match( '#https?://(www\.)?dailymotion\.com/.*#', $content, $match )
	|| preg_match( '#http://dai.ly/.*#', $content, $match )
	|| preg_match( '#https?://(www\.)?hulu\.com/watch/.*#', $content, $match )
	|| preg_match( '#https?://(www\.)?viddler\.com/.*#', $content, $match )
	|| preg_match( '#http://qik.com/.*#', $content, $match )
	|| preg_match( '#http://revision3.com/.*#', $content, $match )
	|| preg_match( '#http://wordpress.tv/.*#', $content, $match )
	|| preg_match( '#https?://(www\.)?funnyordie\.com/videos/.*#', $content, $match )
	|| preg_match( '#https?://(www\.)?flickr\.com/.*#', $content, $match )
	|| preg_match( '#http://flic.kr/.*#', $content, $match );


	$video_url = ( $has_video_url ) ? esc_url( $match[0] ) : null;

	$video_meta = get_post_meta( $post_id, '_format_video_embed', true );
				
	if ( $video_meta  )
		$video_url = wolf_get_iframe_video_url( $video_meta );
	
	echo $video_url;

	exit;

}
add_action('wp_ajax_wolf_get_video_url_from_post_id', 'wolf_get_video_url_from_post_id');
add_action('wp_ajax_nopriv_wolf_get_video_url_from_post_id', 'wolf_get_video_url_from_post_id');
endif;

/**
 * Overwrite plugin settings
 */
//update_option( 'wolf_video_settings', array( 'isotope' => 1, 'col' => 3, 'page_id' => -1 ) );

function wolf_remove_video_options_menu() {
	remove_submenu_page( 'edit.php?post_type=video', 'wolf-video-settings' );
}
//add_action( 'admin_menu', 'wolf_remove_video_options_menu', 999 );

if ( ! function_exists( 'wolf_includes_file' ) ) {
	/**
	 * "Include file" function that checks if the "includes" directory and the required file exists before including it.
	 *
	 * @access public
	 * @param string $filename
	 */
	function wolf_includes_file( $filename = null, $folder = 'includes' ) {

		$inc_dir = '';

		if ( is_dir( WOLF_THEME_DIR . '/' . $folder ) ) {
			$inc_dir = WOLF_THEME_DIR . '/' . $folder;
		}
			
		if ( file_exists( $inc_dir . '/' . $filename ) ) {
			return include( $inc_dir . '/' . $filename );
		}
	}
}
