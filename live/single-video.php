<?php
/**
 * The Template for displaying all single videos.
 *
 */
get_header(); 
wolf_page_before();
?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php
			if(have_posts()):
				while(have_posts()): the_post(); ?>
				
				<article <?php post_class(); ?>  id="post-<?php the_ID(); ?>">
	
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header><!-- .entry-header -->
					
					<div class="entry-content">
						<?php the_content(); ?>
						<?php edit_post_link( __( 'Edit', 'wolf' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-content -->
					
				</article>

					
				<?php
				/**
				* Displaying the comments
				*/
				comments_template(); 

				
				endwhile;
			
			endif; ?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php 
get_sidebar();
wolf_page_after(); // after page hook
get_footer(); 
?>