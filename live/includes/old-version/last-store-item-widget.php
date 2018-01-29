<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: Last Store Item Widget
	Plugin URI: http://themes.brutaldesign.com
	Description: Display your last store item
	Version: 1.0
	Author: BrutalDesign
	Author URI: http://themes.brutaldesign.com

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/*  Create the widget
/*-----------------------------------------------------------------------------------*/
add_action('widgets_init', 'bd_store_item_init');

function bd_store_item_init(){

	register_widget('bd_store_item_widget');
	
}

/*-----------------------------------------------------------------------------------*/
/*  Widget Class
/*-----------------------------------------------------------------------------------*/
class bd_store_item_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*  Widget Setup
	/*-----------------------------------------------------------------------------------*/
	function bd_store_item_widget(){

		// Widget settings
		$ops = array('classname' => 'widget_store_item', 'description' => __('Display your last store item', 'wolf'));

		// Create the widget
		$this->WP_Widget('widget_store_item', 'Last Store Item', $ops);
		
	}

	/*-----------------------------------------------------------------------------------*/
	/*  Display Widget
	/*-----------------------------------------------------------------------------------*/
	function widget($args, $instance){
		
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if (!empty($title)) echo $before_title . $title . $after_title;
		echo '<p>';
		echo $instance['desc'];
		echo '</p>';
		echo bd_store_item($instance['count']);
		echo $after_widget;
	
	}

	/*-----------------------------------------------------------------------------------*/
	/*  Update Widget
	/*-----------------------------------------------------------------------------------*/
	function update($new_instance, $old_instance){
		$instance = $old_instance;

		$instance['title'] = $new_instance['title'];
		$instance['desc'] = $new_instance['desc'];
		$instance['count'] = $new_instance['count'];

		return $instance;
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Displays the widget settings controls on the widget panel
	/*-----------------------------------------------------------------------------------*/
	function form($instance){

			// Set up some default widget settings
			$defaults = array(
				'title' => 'From the Store', 
				'count' => 1, 
				'desc' => '');
			$instance = wp_parse_args((array) $instance, $defaults);

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wolf' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('desc'); ?>"><?php _e('Optional Text', 'wolf'); ?>:</label>
			<textarea class="widefat"  id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc'); ?>" ><?php echo $instance['desc']; ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Count', 'wolf' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo $instance['count']; ?>">		</p>
		</p>
		<?php
	}

	

}

/*-----------------------------------------------------------------------------------*/
/*	Video Function
/*-----------------------------------------------------------------------------------*/
function bd_store_item($count = 1){
	ob_start();
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
	$loop = new WP_Query("post_type=item&posts_per_page=$count");
	if($loop->have_posts()): ?>
	<div>
	<?php while ( $loop->have_posts() ) : $loop->the_post(); 
		$display_price = '';
		$item_name = get_post_meta(get_the_ID(), 'bd_paypal_item_name', true);
		$price = get_post_meta(get_the_ID(), 'bd_paypal_price', true);
		if($price){
			if( $currency_code == 'USD' )
				$display_price = $currency.$price;
			else
				$display_price = $price.$currency;
		}
		
		$sold_out = get_post_meta(get_the_ID(), 'bd_item_soldout', true);

		if($sold_out)
			$display_price = __('Sold Out', 'wolf');
	?>
		<?php if(has_post_thumbnail()): ?>
		<a title="<?php _e('Buy Now', 'wolf'); ?>" href="<?php the_permalink(); ?>">
			<img style="margin-top:-10px;" src="<?php echo wolf_get_post_thumbnail_url('store-thumb'); ?>" alt="<?php echo sanitize_title(get_the_title()); ?>"></a>
		<div style="height:10px"></div>

		<?php endif; ?>
		<?php 
		if( $item_name ) 
			echo '<strong>'.$item_name.'</strong>'; 

		if( $sold_out && $item_name ||  !$sold_out && $price && $item_name) 
			echo ' &mdash; ';

		echo $display_price;
	             ?>
		<br><a style="margin-top:8px" href="<?php the_permalink(); ?>" class="buy-button"><span class="buy-cd"></span><?php _e('Buy Now', 'wolf'); ?></a>
		<?php if($count > 1): ?>
		<div style="height:20px"></div>
		<?php endif; ?>
	<?php endwhile; ?>
	</div>
	<?php endif;
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}        
?>