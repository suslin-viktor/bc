<?php
/**
 * Basic Theme Functions
 *
 */


/**
 *  Allow Shortcodes in Text Widget
 */
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );


if ( ! function_exists( 'wolf_gravatar' ) ) :
/**
 * Custom Default Avatar
 */
function wolf_gravatar( $avatar_defaults ) {

	if ( wolf_get_theme_option( 'custom_avatar' ) ) {
		$custom_avatar = wolf_get_theme_option( 'custom_avatar' );
		$avatar_defaults[$custom_avatar] = __( 'Custom avatar', 'wolf' );
	}

	return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'wolf_gravatar' );
endif;



if ( ! function_exists( 'wolf_favicons' ) ) :
/**
 * Add favicons (images/favicons)
 */
function wolf_favicons() {
	?>
	<!-- Favicons -->
	<link rel="shortcut icon" href="<?php echo wolf_get_theme_uri( '/images/favicons/favicon.ico' ); ?>">
	<link rel="apple-touch-icon" href="<?php echo wolf_get_theme_uri( '/images/favicons/touch-icon-57x57.png' ); ?>">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo wolf_get_theme_uri( '/images/favicons/touch-icon-72x72.png' ); ?>">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo wolf_get_theme_uri( '/images/favicons/touch-icon-114x114.png' ); ?>">
	<?php
}
add_action( 'wolf_meta_head', 'wolf_favicons' );
endif;


if ( ! function_exists( 'wolf_custom_login_logo' ) ) :
/**
 * Custom Login Logo Option
 */
function wolf_custom_login_logo() { 
	
	$login_logo = wolf_get_theme_option( 'login_logo' );

	if ( $login_logo ) 
		echo '<style  type="text/css"> h1 a { background-image:url(' . $login_logo .' )  !important; } </style>';
}  
add_action( 'login_head',  'wolf_custom_login_logo' );
endif;


if ( ! function_exists( 'wolf_credits' ) ) :
/**
 * Copyright/site info text
 */
function wolf_credits() {

	$default = __( 'Powered by Wordpress', 'wolf' );

	$footer_text = wolf_get_theme_option( 'footer_text', $default );

	if ( $footer_text ) {

		echo '<div class="site-infos">';
		echo stripslashes( $footer_text );
		echo '</div>';
	}
		
}
add_action( 'wolf_site_info', 'wolf_credits' );
endif;


if ( ! function_exists( 'wolf_tracking_code' ) ) :
/**
 * Output Analitycs code in the page footer
 */
function wolf_tracking_code() {

	$tracking_code = wolf_get_theme_option( 'tracking_code');

	if ( $tracking_code && ! is_user_logged_in() ) {
		echo stripslashes( $tracking_code );
	}
		
}
add_action( 'wolf_body_end', 'wolf_tracking_code' );
endif;


if ( ! function_exists( 'wolf_comment' ) ) :
/*-----------------------------------------------------------------------------------*/
/*	Basic Comments function
/*-----------------------------------------------------------------------------------*/
function wolf_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<p><?php _e( 'Pingback:', 'wolf' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'wolf' ), '<span class="ping-meta"><span class="edit-link">', '</span></span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
	?>
	<li id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
			<div class="comment-author vcard">
				<?php echo get_avatar( $comment, 74 ); ?>
			</div><!-- .comment-author -->

			<header class="comment-meta">
				<cite class="fn"><?php comment_author_link(); ?></cite>
				<?php
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						sprintf( _x( '%1$s at %2$s', '1: date, 2: time', 'wolf' ), get_comment_date(), get_comment_time() )
					);
					edit_comment_link( __( 'Edit', 'wolf' ), '<span class="edit-link">', '<span>' );
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'wolf' ); ?></p>
			<?php endif; ?>

			<div class="comment-content">
				<?php comment_text(); ?>
			</div><!-- .comment-content -->

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'wolf' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // End comment_type check.
}
endif; // ends check for wolf_comment()