<?php
/*
Template Name: Store
*/

get_header(); 
wolf_page_before(); 

$bd_paypal_option = get_option('bd_paypal_settings');
$currency_code = $bd_paypal_option['currency'];
$currency_symbol = array(
	'USD' => '$',
	'EUR' => '€',
	'GBP'        => '£'

);

if( isset( $currency_symbol[$currency_code] ) )
	$currency = $currency_symbol[$currency_code];
else
	$currency = $currency_code;
?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
		<?php if( get_post_meta(get_the_ID(), 'bd_page_title', true) ): ?>
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header><!-- .entry-header -->
		<?php endif; ?>
		<?php $loop = new WP_Query("post_type=item&posts_per_page=-1");
		if($loop->have_posts()): 
			$args = array(
				'taxonomy'     => 'item-type',
				'orderby'      => 'slug',
				'show_count'   => 0,
				'pad_counts'   => 0,
				'hierarchical' => 0,
				'title_li'     => ''
			); 
		 ?>
		<div id="content" role="main">
			<div id="filter-container">
				<ul id="filter">
					<li><a href="#" data-filter="item" class="active hover"><?php _e('All', 'wolf'); ?></a></li>
					<?php $cat = get_categories( $args ); ?>
					
					<?php foreach($cat as $c): ?>
						<?php if($c->count != 0): ?>
						<li><a href="#" data-filter="<?php echo $c->slug; ?>" class="hover"><?php echo $c->name; ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
					
				</ul>
				<div class="clear"></div>
			</div><!-- #filter-container -->
		<div id="store-grid">
		<?php 
		while ($loop->have_posts()) : $loop->the_post();

			/* Categories
			-----------------------------*/
			$term_list = '';
			$term_name ='';
			if(get_the_terms($post->ID, 'item-type')){
				foreach(get_the_terms($post->ID, 'item-type') as $term){
					$term_list .= $term->slug.' ';
					$term_name .= $term->name.', ';
				}
			}
			$term_name = substr($term_name, 0, -2);


			/* Item meta
			-----------------------------*/
			$display_price = '';
			$item_name = get_post_meta(get_the_ID(), 'bd_paypal_item_name', true);
			$price = get_post_meta(get_the_ID(), 'bd_paypal_price', true);
			
			if($price){
				if( $currency_code == 'USD' || $currency_code == 'GBP' )
					$display_price = $currency.$price;
				else
					$display_price = $price.$currency;
			}
			
			$sold_out = get_post_meta(get_the_ID(), 'bd_item_soldout', true);

			if($sold_out)
				$display_price = __('Sold Out', 'wolf');
		?>
		             <?php if(has_post_thumbnail()): ?>
			<div <?php post_class(array('store-item-container', $term_list)); ?> id="post-<?php the_ID(); ?>">
				<div class="store-item">
					<a class="entry-link shadow" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
						<?php the_post_thumbnail('store-thumb'); ?>
					</a>
					<div class="store-item-meta">
						<?php 
						if( $item_name ) 
							echo '<strong>'.$item_name.'</strong>'; 
						
						if( $sold_out && $item_name ||  !$sold_out && $price && $item_name) 
							echo ' &mdash; ';
						
						echo $display_price;
										             ?>
				            </div>
					
				</div>
			</div>
			<?php 
			endif;
		
			endwhile; ?> 
			<div class="clear"></div>
			</div><!-- #store-grid -->
		</div><!-- #content -->
		
		                           
		<?php else: // if no post?>
		<div style="padding:0 0 150px">
			<article id="post-0" class="post no-results not-found">
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Nothing Found', 'wolf' ); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<p><?php _e( 'No item for sale yet.', 'wolf' ); ?></p>
				</div><!-- .entry-content -->
			</article><!-- #post-0 .post .no-results .not-found -->

		</div>
		<?php endif; ?>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php
get_sidebar();
wolf_page_after();
get_footer(); ?>