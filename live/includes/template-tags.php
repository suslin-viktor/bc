<?php
/*-----------------------------------------------------------------------------------*/
/*  Template tags that we will use in loop-post.php
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'wolf_post_title' ) ) :
/**
 * Prints HTML post title
 *
 * The post title is rendered differently depending on post formats and is a link if is in the index loop
 *
 *
 * @return void
 */
function wolf_post_title() {

	$exclude_formats = array( 'aside', 'status', 'quote', 'link', 'chat' );
	$format = get_post_format();
	if ( ! in_array( $format, $exclude_formats ) ) :
		if ( is_single() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
	endif;

	if ( $format == 'link' ) :

	$first_url = wolf_get_first_url();
	 ?>
	<h2 class="entry-title">
		<a target="_blank" title="<?php _e( 'View Website', 'wolf' ) ?>" href="<?php echo $first_url; ?>">
			<?php the_title(); ?>
		</a>
	</h2>
	<?php endif;
	
}
endif;

if ( ! function_exists( 'wolf_post_thumbnail' ) ) :
/**
 * Display an optional post thumbnail.
 *
 *
 *
 * @return void
*/
function wolf_post_thumbnail() {
	if ( post_password_required() || ! has_post_thumbnail() ) {
		return;
	}


	$post_id = get_the_ID();
	$img = wolf_get_post_thumbnail_url( 'default' );
	$img_excerpt = get_post( get_post_thumbnail_id() )->post_excerpt;
	$img_alt = esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) );
	$caption = ( $img_excerpt ) ? $img_excerpt : get_the_title();
	$caption = '';

	if ( 'image' == get_post_format() ) :
	
		$full_img = wolf_get_post_thumbnail_url( 'full' );
		echo '<div class="entry-thumbnail">';
		echo "<a title='$caption' class='lightbox zoom' href='$full_img'><img src='$img' alt='$img_alt'></a>";
		echo '</div>';

	else : 

		echo '<div class="entry-thumbnail">';
		if ( ! is_single() ) :
		echo '<a href="' . get_permalink() . '" title="' . esc_attr( sprintf( __( 'Permalink to %s', 'wolf' ), the_title_attribute( 'echo=0' ) ) ) . '" rel="bookmark">';
		endif;
		echo get_the_post_thumbnail( $post_id, 'default' );
		
		if ( ! is_single() ) :
		echo '</a>';
		endif;
		echo '</div>';


	endif;
}
endif;



if ( ! function_exists( 'wolf_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own wolf_entry_meta() to override in a child theme.
 *
 *
 * @return void
 */
function wolf_entry_meta() {
	
	if ( is_sticky() && is_home() && ! is_paged() )
		echo '<span class="featured-post">' . __( 'Sticky', 'wolf' ) . '</span>';


	// if ( ! has_post_format( 'aside' ) && ! has_post_format( 'link' ) && 'post' == get_post_type() ||  'work' == get_post_type() )
	// 	wolf_entry_date();

	if ( ! is_single() && ! has_post_format( 'link' ) && ! has_post_format( 'status' ) ) {
		printf( '<span class="permalink"><a href="%1$s" title="%2$s" rel="bookmark">%3$s</a></span>',
			esc_url( get_permalink() ),
			esc_attr( sprintf( __( 'Permalink to %s', 'wolf' ), the_title_attribute( 'echo=0' ) ) ),
			__( 'Permalink', 'wolf' )
		);
	}

	// Post author
	if ( 'post' == get_post_type() && is_multi_author() ) {
		printf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'wolf' ), get_the_author() ) ),
			get_the_author()
		);
	}
		

	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'wolf' ) );
	if ( $categories_list ) {
		echo '<span class="categories-links">' . $categories_list . '</span>';
	}

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'wolf' ) );
	if ( $tag_list ) {
		echo '<span class="tags-links">' . $tag_list . '</span>';
	}

	
}
endif;

if ( ! function_exists( 'wolf_entry_date' ) ) :
/**
 * Prints HTML with date information for current post.
 *
 * Create your own wolf_entry_date() to override in a child theme.
 *
 *
 * @param boolean $echo Whether to echo the date. Default true.
 * @return string
 */
function wolf_entry_date( $echo = true ) {
	
	$format_prefix = ( has_post_format( 'chat' ) || has_post_format( 'status' ) ) ? _x( '%1$s on %2$s', '1: post format name. 2: date', 'wolf' ): '%2$s';

	$date = sprintf( '<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>',
		esc_url( get_permalink() ),
		esc_attr( sprintf( __( 'Permalink to %s', 'wolf' ), the_title_attribute( 'echo=0' ) ) ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) )
	);

	if ( $echo )
		echo $date;

	return $date;
}
endif;

if( ! function_exists( 'wolf_paging_nav' ) ):
/**
 * Displays navigation to next/previous set of posts when applicable.
 *
 */
function wolf_paging_nav( $loop = null ) {
	
	if ( ! $loop ){
		global $wp_query;
		$max = $wp_query->max_num_pages;
	} else {
		$max = $loop->max_num_pages;
	}

	// Don't print empty markup if there's only one page.
	if ( $max < 2 )
		return;
	
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<div class="nav-links clearfix">

			<?php if ( get_next_posts_link( '', $max ) ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'wolf' ), $max ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link( '', $max ) ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'wolf' ), $max ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if( ! function_exists( 'wolf_post_nav' ) ):
/**
 * Displays navigation to next/previous work post when applicable.
 *
 */
function wolf_post_nav() {
	global $post;

	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
	$next = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous )
		return;
	?>
	<nav class="navigation post-navigation" role="navigation">
		<div class="nav-links clearfix">
			
			<p class="left" style="margin-top:5px; margin-bottom:8px">
				<?php next_post_link('%link',"&lsaquo; %title"); ?>
			</p>
			<p class="right" style="margin-top:5px; margin-bottom:8px">
				<?php previous_post_link('%link',"%title &rsaquo;"); ?>
			</p>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;