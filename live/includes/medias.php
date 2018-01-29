<?php
if ( ! function_exists( 'wolf_post_class' ) ) :
/**
 * Add custom classes to each post
 */
function wolf_post_class( $classes ) {

	$format = null;

	$is_embed_video = false;
	$content = get_the_content();
	$has_post_thumbnail = has_post_thumbnail();
	$format = get_post_format();
	
	if ( $has_post_thumbnail )
			$classes[] = 'has-thumbnail';
	
	if ( ! $has_post_thumbnail )
		$classes[] = 'no-thumbnail';

	$pattern = get_shortcode_regex();
	
	if ( preg_match( "/$pattern/s", $content, $match ) ) {
		if ( 'video' == $match[2] ) {
			$classes[] = 'is-embed-video';
			$is_embed_video = true;
		}
	}

	if ( preg_match( "/$pattern/s", $content, $match ) ) {
		if ( 'audio' == $match[2] ) {
			$classes[] = 'is-embed-audio';
		}
	}

	if ( preg_match( wolf_get_regex( 'twitter' ), $content ) ) {
		$classes[] = 'has-tweet';
	}
		
	if ( $format ) {
			
		if ( wolf_get_first_soundcloud_url() && $format == 'audio' )
			$classes[] = 'is-soundcloud';

		if ( wolf_get_first_video_url() || $is_embed_video && $format == 'video' )
			$classes[] = 'is-video';

		if ( ( $format == 'aside' || $format == 'status' ) && preg_match( wolf_get_regex( 'twitter' ), $content ) ) {
			$classes[] = 'is-tweet';
		}
				
		if( $format == 'image' && preg_match( wolf_get_regex( 'instagram' ), $content ) )
			$classes[] = 'is-instagram';
	
	}
	
	return $classes;
}
add_filter( 'post_class', 'wolf_post_class' );
endif;


if ( ! function_exists( 'wolf_featured_quote' ) ) :
/**
 * Returns the first quote in post
 */
function wolf_featured_quote() {
	
	global $post;
	
	$quote = null;
	$has_quote = preg_match( '#<blockquote[^>]*>([^<]+|<(?!/?blockquote)[^>]*>|(?R))+</blockquote>#', $post->post_content, $match );
	
	if ( $has_quote ) {

		if ( ! is_single() ) {

			$hash_link = '<a class="hash-link" href="' . get_permalink() . '">#</a>';

			if ( preg_match( '#<cite[^>]*>([^<]+|<(?!/?cite)[^>]*>|(?R))+</cite>#', $match[0], $cite_match ) ) {
				
				$quote = str_replace( '</cite>', $hash_link . '</cite>', $match[0] );
			
			} else {
				
				$quote = str_replace( '</blockquote>', $hash_link . '</blockquote>', $match[0] );
			}
			
			

		} else {
			$quote = $match[0];
		}
	}

	return $quote;

}
endif;


// --------------------------------------------------------------------------

if ( ! function_exists( 'wolf_featured_instagram' ) ) :
/**
 * Returns the first embed instagram in post
 */
function wolf_featured_instagram( $embed = true ) {
	
	
	$has_instagram = preg_match( wolf_get_regex( 'instagram' ), get_the_content(), $match );
	
	$instagram = null;

	if ( $has_instagram ) {

		if ( $embed ) {

			$instagram = wp_oembed_get( $match[0] );

		} else {
			$instagram = $match[0];
		}
	}

	return $instagram;

}
endif;

// --------------------------------------------------------------------------

if ( ! function_exists( 'wolf_featured_tweet' ) ) :
/**
 * Returns the first tweet in post
 */
function wolf_featured_tweet( $embed = true ) {
	
	$has_tweet = preg_match( wolf_get_regex( 'twitter' ), get_the_content(), $match );
	
	$tweet = null;

	if ( $has_tweet ) {

		if ( $embed ) {

			$tweet = wp_oembed_get( $match[0] );

		} else {
			$tweet = $match[0];
		}
	}

	return $tweet;

}
endif;

// --------------------------------------------------------------------------


if ( ! function_exists( 'wolf_featured_gallery' ) ) :
/**
 * Returns the first gallery from post content. 
 * Changes image size depending on context
 */
function wolf_featured_gallery( $do_shortcode = true, $size = 'image-thumb' ) {
	
	$pattern = get_shortcode_regex();
	
	$shortcodes = array(
		'gallery',
		'rev_slider'
	);
	
	if ( preg_match( "/$pattern/s", get_the_content(), $match ) ) {
		
		if ( in_array( $match[2], $shortcodes ) ) {

		//if ( 'gallery' == $match[2] ) {
			
			if ( 'gallery' == $match[2] && ! strpos( $match[3], 'size' ) ) {
				$match[3] .= ' size="' . $size . '"';
			}
			
			if ( $do_shortcode ) {
				return do_shortcode_tag( $match );
			} else {
				return $match;
			}
		
		}
	}
}
endif;

// --------------------------------------------------------------------------

if ( ! function_exists( 'wolf_featured_audio' ) ) :
/**
 * Returns the first audio in post
 * Looks for shortcode and soundcloud embed URL
 */
function wolf_featured_audio( $embed = true ) {
	
	$audio = null;

	$pattern = get_shortcode_regex();
	$first_url = wolf_get_first_url();
	
	$shortcodes = array(
		'audio',
		'playlist',
		'wolf_jplayer_playlist',
		'soundcloud'
	);

	if ( preg_match( "/$pattern/s", get_the_content(), $match ) ) {

		if ( in_array( $match[2], $shortcodes ) ) {

			if ( $embed ) {
				$audio = do_shortcode_tag( $match );
			} else {
				$audio = $match;
			}
		} elseif ( preg_match( wolf_get_regex( 'soundcloud' ), $first_url ) ) {

			if ( $embed ) {
				$audio = wp_oembed_get( $first_url );
			} else {
				$audio = $first_url;
			}

		}
			
	} elseif ( preg_match( wolf_get_regex( 'soundcloud' ), $first_url ) ) {

		if ( $embed ) {
			$audio = wp_oembed_get( $first_url );
		} else {
			$audio = $first_url;
		}

	}

	return $audio;

}
endif;

// --------------------------------------------------------------------------

if ( ! function_exists( 'wolf_featured_video' ) ) :
/**
 * Returns the first video in post
 * Looks for shortcode and embed URL
 */
function wolf_featured_video( $embed = true ) {
	
	$video = null;

	$pattern = get_shortcode_regex();
	$shortcodes = array(
		'video',
		'playlist',
		'wpvideo'
	);

	if ( preg_match( "/$pattern/s", get_the_content(), $match ) ) {
		
		if ( in_array( $match[2], $shortcodes ) ) {

			if ( $embed ) {
				$video = do_shortcode_tag( $match );
			} else {
				$video = $match;
			}
		
		} else {

			$first_video_url = wolf_get_first_video_url();



			if ( $first_video_url ) {

				if ( $embed ) {
					
					$video = wp_oembed_get( $first_video_url );
					
				} else {
					$video = $first_video_url;
				}
			
			}
		}
	
	} else {

		$first_video_url = wolf_get_first_video_url();

		if ( $first_video_url ) {

			if ( $embed ) {
				
				$video = wp_oembed_get( $first_video_url );
				
			} else {
				$video = $first_video_url;
			}
		
		}
	}

	return $video;

}
endif;


// --------------------------------------------------------------------------

if ( ! function_exists( 'wolf_post_media' ) ) :
/**
 * Returns featured media
 */
function wolf_post_media( $embed = true ) {

	$media = null;
	$post_id = get_the_ID();
	$format = get_post_format() ? get_post_format() : 'standard';
	$content = get_the_content();
	$has_thumbnail = has_post_thumbnail();

	$audio = wolf_featured_audio( false );
	$video = wolf_featured_video( false );
	$gallery = wolf_featured_gallery( false );
	$tweet = wolf_featured_tweet( false );
	$link = wolf_get_first_url();

	$is_standard = $format == 'standard' && $has_thumbnail;
	$is_image = $format == 'image' && $has_thumbnail;
	$is_instagram = $format == 'image' && preg_match( wolf_get_regex( 'instagram' ), $content );
	
	$is_audio = $audio && $format == 'audio';
	$is_video = $video && $format == 'video' || get_post_type( $post_id ) == 'video' ? true : false;
	$is_quote = $format == 'quote';
	$is_gallery = $gallery && $format == 'gallery';
	$is_link = $format == 'link' && $link;
	//$is_status =  $format == 'status' || $format == 'aside';

	if ( $is_instagram ) {

		$media = wolf_featured_instagram( $embed );

	} elseif ( $is_video ) {

		$media = wolf_featured_video( $embed );


	} elseif ( $is_quote ) {

		$media = wolf_featured_quote();

	} elseif ( $is_link ) {

		if ( is_single() ) {
			$media = '<h1 class="entry-title"><a href="' . $link . '">' . get_the_title() . '</a></h1>';
		} else {
			$media = '<h2 class="entry-title"><a href="' . $link . '">' . get_the_title() . '</a></h2>';
			$media .= '<p>' . $link . ' <a href="' . get_permalink() . '" title="' . esc_attr( sprintf( __( 'Permalink to %s', 'wolf' ), the_title_attribute( 'echo=0' ) ) ) . '" rel="bookmark">#</a></p>';
		}
		
		

	} elseif ( $is_gallery ) {

		$media = wolf_featured_gallery( $embed, 'slide' );

	} elseif ( $is_audio ) {

		if ( $embed ) {
			// if ( $has_thumbnail ) {
			// 	$media .= '<div class="entry-thumbnail">';
			// 	$media .= '<a href="' . get_permalink() . '" title="' . esc_attr( sprintf( __( 'Permalink to %s', 'wolf' ), the_title_attribute( 'echo=0' ) ) ) . '" rel="bookmark">';
			// 	$media .= get_the_post_thumbnail( $post_id, 'image-thumb' );
			// 	$media .= '</a>';
			// 	$media .= '</div>';
			// }
			$media .= wolf_featured_audio( $embed );
		} else {
			$media = wolf_featured_audio( $embed );
		}


	} elseif ( $is_image ) {

		$img_excerpt = get_post( get_post_thumbnail_id() )->post_excerpt;
		$img_alt = esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) );

		$caption = ( $img_excerpt ) ? $img_excerpt : get_the_title();
		$caption = '';
		if ( ! is_single() )
			$img = wolf_get_post_thumbnail_url( 'image-thumb' );
		else
			$img = wolf_get_post_thumbnail_url( 'extra-large' );

		$full_img = wolf_get_post_thumbnail_url( 'full' );

		$media = '<div class="entry-thumbnail">';
		$media .= "<a title='$caption' class='lightbox zoom' href='$full_img'><img src='$img' alt='$img_alt'></a>";
		$media .= '</div>';
	
	} elseif ( $is_standard ) {
		$media = '<div class="entry-thumbnail">';
		if ( ! is_single() ) :
		$media .= '<a href="' . get_permalink() . '" title="' . esc_attr( sprintf( __( 'Permalink to %s', 'wolf' ), the_title_attribute( 'echo=0' ) ) ) . '" rel="bookmark">';
		endif;
		$media .= get_the_post_thumbnail( $post_id, 'image-thumb' );
		
		if ( ! is_single() ) :
		$media .= '</a>';
		endif;
		$media .= '</div>';
	
	}

	return $media;
}
endif;

// --------------------------------------------------------------------------


if ( ! function_exists( 'wolf_get_first_link_url' ) ) :
/**
 * Return the URL for the first link in the post content or the permalink if no
 * URL is found.
 * Keep this function just in case  'cause we mostly use the function below
 */
function wolf_get_first_link_url() {
	$has_url = preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $match );
	$link = ( $has_url ) ? $match[1] : apply_filters( 'the_permalink', get_permalink() );

	return esc_url_raw( $link );
}
endif;

// --------------------------------------------------------------------------

if ( ! function_exists( 'wolf_get_first_url' ) ) :
/**
 * Return the first URL in the post if an URL is found
 */
function wolf_get_first_url() {
	
	$has_url = preg_match( '`\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))`', get_the_content(), $match );
	$link = ( $has_url ) ? $match[0] : get_permalink();

	return esc_url_raw( $link );
}
endif;

// --------------------------------------------------------------------------


if ( ! function_exists('wolf_get_first_soundcloud_url') ) :
/**
 * Returns the first soundcloud URL
 */
function wolf_get_first_soundcloud_url() {
	
	$has_soundlcloud_url = preg_match( wolf_get_regex( 'soundcloud' ), get_the_content(), $match );
	$link = ( $has_soundlcloud_url ) ? $match[0] : null;

	return esc_url( $link );

}
endif;

// --------------------------------------------------------------------------

if ( ! function_exists( 'wolf_get_first_video_url' ) ) :
/**
 * Return the first video URL in the post if a video URL is found
 */
function wolf_get_first_video_url( $post_id = null ) {

	$content = get_the_content();

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

	return $video_url;
}

endif;

// --------------------------------------------------------------------------

if ( ! function_exists( 'wolf_get_regex' ) ) :
/**
 * Get most usefull regex
 */
function wolf_get_regex( $type = null ) {

	$regex = array(

		'instagram' => '#http://instagr(\.am|am\.com)/p/.*#',
		'twitter' => '#https?://(www\.)?twitter\.com/.+?/status(es)?/.*#',
		'soundcloud' => '#https?://(www\.)?soundcloud\.com/.*#',

	);

	if ( array_key_exists( $type, $regex ) ) {

		return $regex[$type];

	}

}
endif;


// --------------------------------------------------------------------------

if ( ! function_exists( 'wolf_override_shortcode_video_dimensions' ) ) :
/**
 * Overrrides video shortcode dimension to always make it full width
 */
function wolf_override_shortcode_video_dimensions( $output, $pairs, $atts ) {
	
	$output['width'] = '800';
	$out['height'] = '450';
	return $output;

}
add_filter( 'shortcode_atts_video', 'wolf_override_shortcode_video_dimensions', 10, 3 );
endif;


// --------------------------------------------------------------------------

if( ! function_exists('wolf_format_custom_content_output') ) :
/**
 * Format output for other content area that can't be handle by the_content() filter
 * Such as additional content area from the theme options
 */
function wolf_format_custom_content_output( $content ) {

	$array = array(
		'<p>[' => '[',
		']</p>' => ']',
		']<br />' => ']'
	);
	$content = strtr( $content, $array );

	return apply_filters( 'the_content', $content );
}
endif;


/*-----------------------------------------------------------------------------------*/
/*	Format Single Post Content
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'wolf_content' ) ) :
/**
 * Excludes featured media from content.
 * The featured media will be displayed at the top in single page
 */
function wolf_content() {

	global $post;

	if ( ! is_single() && $post->post_excerpt || is_search() ) {
		
		return get_the_excerpt(); 
	
	} else {

		$media = wolf_post_media( false );
		$post_types = array( 'post', 'work' );

		$content = get_the_content( wolf_more_text() );
		$array = array(
			'<p>[' => '[',
			']</p>' => ']',
			']<br />' => ']'
		);
		$content = strtr( $content, $array );
		
		if ( in_array( get_post_type(), $post_types ) && $media ) {

			$new_content = str_replace( $media, '', $content );

		} else {
			$new_content = $content;
		}

		return apply_filters( 'the_content', $new_content );

	}

}
//add_filter( 'the_content', 'wolf_content' );
endif;
