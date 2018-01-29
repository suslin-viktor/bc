<?php
/**
 * The Template for displaying all single posts.
 *
 */
get_header(); 
wolf_page_before();
?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php
			if(have_posts()):
				while(have_posts()): the_post();
				
					get_template_part('partials/loop', 'post');

					
					/**
					* Displaying the comments
					*/
					comments_template(); 

				
				endwhile;
		
			
			else: //no posts found
				get_template_part('partials/content', 'none');
			
			endif; ?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php 
get_sidebar();
wolf_page_after(); // after page hook
get_footer(); 
?>