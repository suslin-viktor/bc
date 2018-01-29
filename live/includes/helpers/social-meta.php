<?php
if ( ! class_exists( 'Wolf_Social_Meta' ) ) :
class Wolf_Social_Meta {

	function __construct() {

		add_action( 'wolf_meta_head', array( $this, 'meta' ) );

	}

	// --------------------------------------------------------------------------

	function get_share_img( $post_id ) {
		global $post;

		/* We define the default image first and see if the post contains an image after */
		$share_image = wolf_get_theme_option( 'share_img' ) ? wolf_get_theme_option( 'share_img' ) : wolf_get_theme_uri( '/images/share.jpg' );

		if ( has_post_thumbnail( $post_id ) ) {
			$share_image = wolf_get_post_thumbnail_url( 'medium', $post_id );
		}

		return $share_image;
	}

	// --------------------------------------------------------------------------

	function get_description( $post ) {
		$excerpt = '';

		$excerpt = $post->post_excerpt;
		if ( $excerpt == '' ) {
			$excerpt = strip_tags( preg_replace( '/'.get_shortcode_regex().'/i', '', $post->post_content ) );
			$excerpt = preg_replace( "/\s+/", ' ', $excerpt );
			$excerpt = $this->sample($excerpt);
		}

		return $excerpt;
	}

	// --------------------------------------------------------------------------

	function get_wp_title() {
		ob_start();
		wp_title();
		$wp_title = ob_get_contents();
		ob_end_clean();
		$wp_title = preg_replace("/&#?[a-z0-9]{2,8};/i","",$wp_title);
		$wp_title = preg_replace ("/\s+/", " ", $wp_title);
		return $wp_title;

	}

	// --------------------------------------------------------------------------

	function meta() {
		global $post, $wp_query;
		$site_name = get_bloginfo( 'name' );

		if ( ! is_404() && ! is_search() && $post ) {
			$post_id = $post->ID;

			if ( $wp_query && isset( $wp_query->queried_object->ID ) )
				$post_id = $wp_query->queried_object->ID;
		?>


<!-- google meta -->
<?php if ( $this->get_description( $post ) ) : ?>
<meta name="description" content="<?php echo $this->get_description( $post ); ?>" />
<?php endif; ?>

<!-- facebook meta -->
<meta property="og:site_name" content="<?php echo $site_name; ?>" />
<meta property="og:title" content="<?php echo $this->get_wp_title(); ?>" />
<meta property="og:url" content="<?php echo get_permalink( $post_id ); ?>" />
<?php if ( $this->get_share_img( $post_id ) ) : ?>
<meta property="og:image" content="<?php echo $this->get_share_img( $post_id ); ?>" />
<?php endif; ?>
<?php if ( $this->get_description( $post ) ) : ?>
<meta property="og:description" content="<?php echo $this->get_description( $post ); ?>" />
<?php endif; ?>

<!-- google plus meta -->
<meta itemprop="name" content="<?php echo $site_name; ?>" />
<?php if ( $this->get_share_img( $post_id ) ) : ?>
<meta itemprop="image" content="<?php echo $this->get_share_img( $post_id ); ?>" />
<?php endif; ?>
<?php if ( $this->get_description( $post ) ) : ?>
<meta itemprop="description" content="<?php echo $this->get_description( $post ); ?>" />
<?php endif; ?>
		<?php
		}
	}

	// --------------------------------------------------------------------------

	function sample( $text, $nbcar = 140, $after = '...' ) {
		$text = strip_tags( $text );   
		
		if ( strlen( $text ) > $nbcar ) {
			
			preg_match( '!.{0,'.$nbcar.'}\s!si', $text, $match );
			if (isset($match[0])){
				$str = trim( $match[0] ) . $after;
			} else {
				$str = $text;
			}
				 

		} else {
			$str = $text;  
		}
		
		$str = preg_replace( '/\s\s+/', '', $str );
		$str = preg_replace(  '|\[(.+?)\](.+?\[/\\1\])?|s', '', $str );
		$str = preg_replace( '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $str );
		return $str;
	}

}
endif;

if ( wolf_get_theme_option( 'social_meta' ) && class_exists( 'Wolf_Social_Meta' ) ) {
	new Wolf_Social_Meta();
}