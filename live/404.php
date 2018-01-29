<?php 
/**
 * The template for displaying 404 pages (Not Found).
 * 
 */
get_header(); ?>

	<div id="main-content" class="wrap">
		<article id="post-0" class="post error404 not-found center">
			<h1><span class="themecolor">404</span> <br>
				<?php _e('Page not found !', 'wolf'); ?>
			</h1>
			<p><?php 

			if ( wolf_get_theme_option('404')) { 
				echo stripslashes(wolf_get_theme_option('404')); 
			}else{ 
				_e("You've tried to reach a page that doesn't exist or has moved.", 'wolf'); 
			} 
			?></p>
			<p><a href="<?php echo home_url(); ?>/">&larr; <?php _e('back home', 'wolf'); ?></a></p>
		</article>
	</div><!-- .wrap #main-content -->
<?php get_footer(); ?>