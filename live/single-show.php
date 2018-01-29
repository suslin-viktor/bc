<?php
/**
 * The Template for displaying all single show posts.
 *
 */
get_header();
wolf_page_before();
?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
	
	<?php
	if ( have_posts()): while ( have_posts() ) : the_post();

		$post_id = $post->ID;
		/**
		 * Show meta
		 */
		$artist = get_post_meta( $post_id, '_wolf_show_artist', true );
		$raw_date = get_post_meta( $post_id, '_wolf_show_date', true );
		$is_past_show = function_exists( 'wolf_is_past_show' ) && wolf_is_past_show( $raw_date ) ? true : false;
		$date = function_exists( 'wolf_get_show_date' ) ?  wolf_get_show_date( get_post_meta( $post_id, '_wolf_show_date', true ), false ) : null;
		$time = get_post_meta( $post_id, '_wolf_show_time', true );
		
		$city = get_post_meta( $post_id, '_wolf_show_city', true );
		$country = get_post_meta( $post_id, '_wolf_show_country_short', true );
		$state = get_post_meta( $post_id, '_wolf_show_state', true );

		$place = $city;

		if ( $country && ! $state ) {
			$place = $city . ', ' . $country;
		}

		if ( ! $country && $state ) {
			$place = $city . ', ' . $state;
		}

		if ( $country && $state ) {
			$place = $city . ', ' . $state . ' (' . $country . ')';
		}

		$venue = get_post_meta( $post_id, '_wolf_show_venue', true );
		$address = get_post_meta( $post_id, '_wolf_show_address', true );
		$city = get_post_meta( $post_id, '_wolf_show_city', true );
		$zip = get_post_meta( $post_id, '_wolf_show_zip', true );
		
		$phone = get_post_meta( $post_id, '_wolf_show_phone', true );
		$email = get_post_meta( $post_id, '_wolf_show_email', true );
		$website = get_post_meta( $post_id, '_wolf_show_website', true );

		$map = get_post_meta( $post_id, '_wolf_show_map', true );
		$facebook_page = get_post_meta( $post_id, '_wolf_show_fb', true );
		$ticket = get_post_meta( $post_id, '_wolf_show_ticket', true );
		$price = get_post_meta( $post_id, '_wolf_show_price', true );
		$cancelled = get_post_meta( $post_id, '_wolf_show_cancel', true );
		$soldout = get_post_meta( $post_id, '_wolf_show_soldout', true );
		$free = get_post_meta( $post_id, '_wolf_show_free', true);

		$ticket_text = $price ? sprintf( __( 'Buy ticket - %s', 'wolf' ), $price ) : __( 'Buy ticket', 'wolf' );
		?>
		<article <?php post_class( array( 'wolf-show' ) ); ?>  id="post-<?php the_ID(); ?>">
	
			<header class="entry-header">
				
				<h1 class="entry-title">
					<?php the_title(); ?>	
				</h1>
				
			</header>

			<div class="entry-content">
				<div id="wolf-single-show">
					<div id="wolf-show-meta">
						<?php if ( has_post_thumbnail() ): 
						$img = wolf_get_show_thumbnail_url( 'full' );
						?>
							
							<div class="entry-thumbnail">
								<a class="wolf-show-flyer-single" href="<?php echo $img ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
							</div>
					
						<?php endif; ?>

						<?php if ( $artist ) : ?>
							<h3><?php echo $artist; ?></h3>
						<?php endif ; ?>

						<span class="wolf-single-show-date"><?php echo $date; ?></span>

						<span class="wolf-show-place"><?php echo $place; ?></span>

						<?php if ( $free && ! $cancelled && ! $soldout ) : ?>
						<span class="wolf-show-label free"><?php _e( 'Free', 'wolf' ); ?></span>
						<?php endif; ?>

						<?php if ( $soldout && ! $cancelled ) : ?>
						<span class="wolf-show-label sold-out"><?php _e( 'Sold Out !', 'wolf' ); ?></span>
						<?php endif; ?>

						<?php if ( $cancelled ) : ?>
						<span class="wolf-show-label cancelled"><?php _e( 'Cancelled', 'wolf' ); ?></span>
						<?php endif; ?>
						
						<div class="wolf-show-actions">
							<?php
							if ( $facebook_page ) : 
							echo '<a class="wolf-show-facebook-button" title="' . __( 'View the facebook event page', 'wolf' ) . '" target="_blank" href="' . $facebook_page . '">';
							echo '<span class="wolf-show-facebook">facebook</span>'; 
							echo '</a>';
							endif; 
							?>
							<?php
							if ( ! $cancelled && ! $soldout && ! $free && $ticket && ! $is_past_show ) {
						
								echo '<a target="_blank" href="' . esc_url( $ticket ) . '" class="wolf-show-ticket-button">' . $ticket_text . '</a>';
							}
							?>
						</div>

						<?php wolf_share( 'vertical' ); ?>
						<?php wolf_share_mobile(); ?>
						
					</div>
					<div id="wolf-show-content">

						<div id="wolf-show-details">
							<h3><?php _e( 'Details', 'wolf' ); ?></h3>
							
							<?php if ( $time ) : ?>
							<?php printf( __( '<strong>Time</strong> : %s', 'wolf' ), $time ); ?>
							<?php endif ; ?>
													
							<?php if ( $venue ) : ?>
							<br><?php printf( __( '<strong>Venue</strong> : %s', 'wolf' ), $venue ); ?>
							<?php endif ; ?>
							
							<?php if ( $address ) : ?>
							<br><?php printf( __( '<strong>Address</strong> : %s', 'wolf' ), $address ); ?>
							<?php endif ; ?>
							
							<?php if ( $state ) : ?>
							<br><?php printf( __( '<strong>State</strong> : %s', 'wolf' ), $state ); ?>
							<?php endif ; ?>
							
							<?php if ( $zip ) : ?>
							<br><?php printf( __( '<strong>Zip</strong> : %s', 'wolf' ), $zip ); ?>
							<?php endif ; ?>
							
							<?php if ( $phone ) : ?>
							<br><?php printf( __( '<strong>Phone</strong> : %s', 'wolf' ), $phone ); ?>
							<?php endif ; ?>
							
							<?php if ( $email ) : ?>
							<br><?php printf( __( '<strong>Contact Email</strong> : %s', 'wolf' ), '<a href="mailto:' . $email . '" target="_blank">' . $email . '</a>' ); ?>
							<?php endif ; ?>
							
							<?php if ( $website ) : ?>
							<br><?php printf( __( '<strong>Contact Website</strong> : %s', 'wolf' ), '<a href="' . $website . '" target="_blank">' . $website . '</a>' ); ?>
							<?php endif ; ?>
													
							
						</div>
						<?php
						// google map
						if ( $map ) : ?>
						<p><iframe width="100%" height="230" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $map; ?>&amp;output=embed"></iframe></p>
						<?php endif; ?>
						<?php the_content(); ?>
					</div>
				</div>
			
			</div>
			<footer class="entry-meta">
				<?php edit_post_link( __( 'Edit', 'wolf' ), '<span class="edit-link">', '</span>' ); ?>
			</footer>
		</article>

	<?php endwhile; // end of the loop. 
	endif; ?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php
wolf_page_after(); // after page hook
get_footer(); 
?>