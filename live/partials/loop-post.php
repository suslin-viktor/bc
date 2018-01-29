<?php
/**
 * This is the loop for the posts
 * We display each post differently depending on the post format
 */
 ?>
<article <?php post_class(); ?>   id="post-<?php the_ID(); ?>">	
	<div class="post-entry">
		<div class="right-post-column">
			<?php
			$comments_link = get_comments_link();
			if( is_single() )
				$comments_link = '#comments';
			?>
			<a title="<?php printf(__('Comment on %s', 'bd'), get_the_title()); ?>" class="comment-bubble-link scroll" href="<?php echo $comments_link; ?>"><?php echo get_comments_number(); ?></a>
			<?php
			if ( function_exists( 'wolf_share' ) ) {
		
				if ( is_single () ) : ?>
					<?php wolf_share( 'vertical' ); ?>
				<?php
				else :
					wolf_share( 'horizontal' );
				endif;
			}
			/*  Edit link
			/*---------------------------*/
			edit_post_link( __( 'Edit', 'wolf' ), '<span class="edit-link">', '</span>' ); 
			?>
		</div>
		<div class="left-post-column">
			<header class="entry-header">
				<?php wolf_post_title(); ?>
				<div class="entry-meta">
					<?php wolf_entry_date(); ?>
					<?php if ( comments_open() ) : ?>
					<span class="comments-link mobile-comment-count">
						<?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a comment', 'wolf' ) . '</span>', __( 'One comment so far', 'wolf' ), __( 'View all % comments', 'wolf' ) ); ?>
					</span><!-- .comments-link -->
					<?php endif; // comments_open() ?>
				</div>
			</header>
			
			<?php if ( ! get_post_format() || 'image' == get_post_format() || 'audio' == get_post_format() ) : ?>
				<?php wolf_post_thumbnail(); ?>
			<?php endif ?>
		
			<div class="entry-content">
				<?php the_content( wolf_more_text() ); ?>
				<?php wolf_share_mobile(); ?>
			</div>
		
			<footer class="entry-meta">
				<?php wolf_entry_meta(); ?>
			</footer>
		</div>
		
		
	</div>

	<?php if ( is_single() ) : ?>
	<hr>
	<?php wolf_post_nav(); ?>
	<?php endif ?>

</article>
<hr>