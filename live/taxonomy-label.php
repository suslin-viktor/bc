<?php
get_header();
wolf_page_before();
?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			
			 /* The loop */ ?>
			<?php 
			if ( have_posts() ) :
				
				while ( have_posts() ) : the_post(); 
				
				global $more;
				$more = 0;

				$post_id = $post->ID;

				/* Metaboxes and Taxonomy */
				
				// label
				$band = '';

				if ( strip_tags( get_the_term_list( $post_id, 'band', '', ', ', '' ) ) != '' ) {
					
					$band =  '<strong>' . __( 'Band ', 'wolf') . '</strong> : ' . strip_tags( get_the_term_list( $post_id, 'band', '', ', ', '' ) ) . '<br>';

				}


				if ( wolf_get_release_option( 'use_band_tax' ) ) {
					$band = get_the_term_list( $post_id, 'band', '<strong>' . __( 'Band ', 'wolf') . '</strong> : ', ', ', '<br>' );
				}
					

				// label
				$label = '';

				if ( strip_tags( get_the_term_list( $post_id, 'label', '', ', ', '' ) ) != '' ) {
					
					$label =  '<strong>' . __( 'Label ', 'wolf') . '</strong> : ' . strip_tags( get_the_term_list( $post_id, 'label', '', ', ', '' ) ) . '<br>';

				}


				if ( wolf_get_release_option( 'use_label_tax' ) ) {
					$label = get_the_term_list( $post_id, 'label', '<strong>' . __( 'Label ', 'wolf') . '</strong> : ', ', ', '<br>' );
				}

				$release_title = get_post_meta( $post_id, '_wolf_release_title', true );
				$release_date = get_post_meta( $post_id, '_wolf_release_date', true );
				$release_label = get_post_meta( $post_id, '_wolf_release_label', true );
				$release_catalog = get_post_meta( $post_id, '_wolf_release_catalog_number', true );
				$release_type = get_post_meta( $post_id, '_wolf_release_type', true );

				$display_date = '';
				if ( $release_date ) {
					list( $month, $day, $year ) = explode( "-", $release_date );
					$sql_date = $year . '-' . $month . '-' . $day . ' 00:00:00';
					$display_date = mysql2date( get_option( 'date_format' ), $sql_date );
				}

				$thumbnail_size = get_post_meta( $post_id, '_wolf_release_type', true ) == 'DVD' || get_post_meta( $post_id, '_wolf_release_type', true ) == 'K7' ? 'DVD' : 'CD';
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( array( 'wolf-release', 'clearfix' ) ); ?>>
					
					<div class="entry-thumbnail">
						<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'wolf' ), the_title_attribute( 'echo=0' ) ) ); ?>">
							<?php the_post_thumbnail( $thumbnail_size ); ?>
						</a>
						<?php endif; ?>
						<?php edit_post_link( __( 'Edit', 'wolf' ), '<span class="edit-link">', '</span>' ); ?>
					</div>
					
					<div class="entry-content">
						<h2 class="entry-title">
							<a class="entry-link"  href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'wolf' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
						</h2>
						<div class="wolf-release-meta">
							<?php echo $band; ?>
							
							<?php // Title
							if ( $release_title ) : ?>
							<strong><?php _e( 'Title', 'wolf' ); ?></strong> : <?php echo $release_title; ?><br>
							<?php endif; ?>
							
							<?php // Date
							if ( $display_date ) : ?>
							<strong><?php _e( 'Release Date', 'wolf' ); ?></strong> : <?php echo $display_date; ?><br>
							<?php endif; ?>
							
							<?php // Label
							echo $label; ?>
							
							<?php // Catalog number
							if ( $release_catalog ) : ?>
							<strong><?php _e( 'Catalog ref.', 'wolf' ); ?></strong> : <?php echo $release_catalog; ?><br>
							<?php endif; ?>

							<?php // Type
							if ( $release_type && wolf_get_release_option( 'display_format' ) ) : ?>
							<strong><?php _e( 'Format', 'wolf' ); ?></strong> : <?php echo $release_type; ?><br>
							<?php endif; ?>
						</div>
						<a href="<?php the_permalink(); ?>" class="more-link"><?php _e('View details', 'wolf'); ?></a>

					</div><!-- .entry-content -->
					
				</article><!-- .wolf-release -->
				<hr>
			<?php endwhile; ?>
			
			<?php wolf_release_page_nav(); ?>
			
			<?php else : ?>

			<p class="no-release"><?php _e( 'No release yet', 'wolf' ); ?></p>

			<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
<?php
get_sidebar();
wolf_page_after();
get_footer(); 
?>