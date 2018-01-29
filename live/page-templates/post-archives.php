<?php
/*
 * Template Name:  Archives
 */
get_header();
wolf_page_before(); 
?>
	<div id="content" class="site-content" role="main">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php the_content(); ?>
			<?php endwhile; endif; wp_reset_query(); ?>
			<div class="entry-content">
				<section class="archives-list clearfix">
					<div class="archives-row">
						<h4><?php _e( 'Last 30 posts', 'wolf' ); ?></h4>
						<ul>
							<?php wp_get_archives( 'type=postbypost&limit=30' ); ?>
						</ul>
					</div>
					<div class="archives-row">
						<h4><?php _e( 'Archives by Month', 'wolf' ); ?></h4>  
						<ul>  
							<?php wp_get_archives(); ?>  
						</ul>
					</div>
					<div class="archives-row">
						<h4><?php _e( 'Archives by Subject', 'wolf' ); ?></h4>  
						<ul>
							<?php wp_list_categories('title_li='); ?>  
						</ul>
					</div>
				</section>
				<?php 
				$tags = get_tags( array(
						'orderby' => 'name'
					)
				);
				?>
				<?php if ( $tags != array() ) : ?>
				<hr>
				<section class="tag-list">
					<h4>Tags</h4>
					<?php
					
					$html = '<ul class="post_tags">';
					foreach ( $tags as $tag ) {
						$tag_link = get_tag_link( $tag->term_id );
								
						$html .= "<li><a href='{$tag_link}' title='{$tag->name} Tag' class='{$tag->slug}'>";
						$html .= "{$tag->name}</a></li>";
					}
					$html .= '</ul>';
					echo $html;
					?>
				</section>
				<?php endif; ?>
				
			</div><!-- .entry-content -->

			<footer class="entry-meta">
					<?php edit_post_link( __( 'Edit', 'wolf' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- .entry-meta -->
		</article><!-- #post -->

	</div><!-- #content -->
<?php 
wolf_page_after(); 
get_footer(); 
?>