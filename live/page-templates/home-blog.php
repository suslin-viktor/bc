<?php
/*
Template Name: Home - Blog
*/
get_header(); 

wolf_page_before();
?>

	<div id="main-content" class="wrap">
		<div id="primary" class="site-content">
			<div id="content" role="main">
				<?php
				$loop = new WP_Query("post_type=post&paged=$paged");
				if($loop->have_posts()): 
					while ($loop->have_posts()) : $loop->the_post();
					global $more;
					$more = 0;
					get_template_part('partials/loop', 'post');

					endwhile;
					?>
					<a href="<?php echo wolf_get_blog_url(); ?>" class="more-link">
						<?php _e('Read more news', 'wolf'); ?>
					</a>
					<?php
				else: 
					get_template_part('no-results', 'index'); 
	                                     		

	                                    	endif; ?>
	                                     
			</div><!-- #content -->
		</div><!-- #primary .site-content -->
		<?php get_sidebar(); ?>
	</div><!-- .wrap #main-content -->
<?php 
wolf_page_after();
get_footer(); 
?>