<?php
/**
 * The Template for displaying all single gallery posts.
 *
 */
get_header();
wolf_post_before();
?>
	<?php /* The loop */ ?>
	<?php while ( have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php the_content(); ?>
		<?php //wolf_post_nav(); ?>
	</article><!-- article.post -->
	<?php endwhile; ?>
<?php 
wolf_post_after();
get_footer(); 
?>