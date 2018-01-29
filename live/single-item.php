<?php
get_header();
wolf_page_before();
?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
				<?php wolf_post_nav(); ?>
				<?php if(have_posts()): while ( have_posts() ) : the_post(); ?>
					<?php
					$paypal_button = false;
					$price = get_post_meta(get_the_ID(), 'bd_paypal_price', true);
					$sold_out = get_post_meta(get_the_ID(), 'bd_item_soldout', true);
					$item_name = get_post_meta(get_the_ID(), 'bd_paypal_item_name', true);

					$itunes = get_post_meta(get_the_ID(), 'bd_item_itunes', true);
					$amazon = get_post_meta(get_the_ID(), 'bd_item_amazon', true);
					$buy_cd = get_post_meta(get_the_ID(), 'bd_item_buy_cd', true);
					if(get_post_meta(get_the_ID(), 'bd_item_buy_cd_text', true))
						$buy_cd_text = get_post_meta(get_the_ID(), 'bd_item_buy_cd_text', true);
					else
						$buy_cd_text = __('Buy Now', 'wolf');
					?>
					<article <?php post_class(); ?>  id="post-<?php the_ID(); ?>">
				
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
						</header><!-- .entry-header -->
						
						<div class="entry-content">
							<div class="item-meta">
								<?php if(has_post_thumbnail()): ?>
								<p>
									<a style="style:width:100%; display:block;" href="<?php echo wolf_get_post_thumbnail_url('full-size'); ?>" class="zoom lightbox">
										<img width="100%" src="<?php echo wolf_get_post_thumbnail_url('store-thumb'); ?>" alt="<?php echo sanitize_title(get_the_title()); ?>"></a>
								</p>
								<?php endif; ?>
								<?php if( !$sold_out ): ?>
								<?php if($itunes): ?><a target="_blank" href="<?php echo esc_url($itunes); ?>" class="buy-button"><span class="buy-itunes"></span><?php _e('Buy on iTunes', 'wolf'); ?></a><?php endif ; ?>
								<?php if($amazon): ?><a target="_blank" href="<?php echo esc_url($amazon); ?>" class="buy-button"><span class="buy-amazon"></span><?php _e('Buy on Amazon', 'wolf'); ?></a><?php endif ; ?>
								<?php endif; ?>
							</div>
							<div class="item-content">
								<?php the_content(); ?>
								<div class="item-buy">
								<?php if( !$sold_out ): ?>
								<?php if($buy_cd): ?><a href="<?php echo esc_url($buy_cd); ?>" class="buy-button"><span class="buy-cd"></span><?php echo $buy_cd_text; ?></a><?php endif ; ?>
								<?php
									
								/* Paypal button options
								-------------------------------*/
								$type = get_post_meta(get_the_ID(), 'bd_paypal_type', true);
								$shipping = get_post_meta(get_the_ID(), 'bd_paypal_shipping', true);
								$tax = get_post_meta(get_the_ID(), 'bd_paypal_tax', true);
								$paypal_text = get_post_meta(get_the_ID(), 'bd_paypal_text', true);
								$opt1_name = get_post_meta(get_the_ID(), 'bd_paypal_opt1_name', true);
								$opt1 = get_post_meta(get_the_ID(), 'bd_paypal_opt1', true);
								$opt2_name = get_post_meta(get_the_ID(), 'bd_paypal_opt2_name', true);
								$opt2 = get_post_meta(get_the_ID(), 'bd_paypal_opt2', true);
									

								if(!$sold_out && $paypal_text && !$buy_cd)
									bd_paypal($paypal_text, $item_name, $price, $type, $shipping, $tax, $opt1_name, $opt1, $opt2_name, $opt2);


								?>
								<?php else: ?>
								<?php _e('Sold Out', 'wolf') ; ?>
								<?php endif; ?>
								</div>
							</div>
							<div class="clear"></div>
							<?php edit_post_link( __( 'Edit', 'wolf' ), '<span class="edit-link">', '</span>' ); ?>
						</div><!-- .entry-content -->
						
					</article>
		
				<?php endwhile; // end of the loop. 
				endif; ?>
		                                        
		</div><!-- #content -->
	</div><!-- #primary -->
<?php
dynamic_sidebar('item');
wolf_page_after();
get_footer(); ?>