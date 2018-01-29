<?php
/*-----------------------------------------------------------------------------------*/
/*  Create the widget
/*-----------------------------------------------------------------------------------*/
add_action('widgets_init', 'bd_paypal_checkout_init');

function bd_paypal_checkout_init(){

	register_widget('bd_paypal_checkout_widget');
	
}

/*-----------------------------------------------------------------------------------*/
/*  Widget Class
/*-----------------------------------------------------------------------------------*/
class bd_paypal_checkout_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*  Widget Setup
	/*-----------------------------------------------------------------------------------*/
	function bd_paypal_checkout_widget(){

		// Widget settings
		$ops = array('classname' => 'widget_checkout', 'description' => __('Display a paypal checkout button', 'wolf'));

		// Create the widget
		$this->WP_Widget('widget_checkout', 'Paypal checkout', $ops);
		
	}

	/*-----------------------------------------------------------------------------------*/
	/*  Display Widget
	/*-----------------------------------------------------------------------------------*/
	function widget($args, $instance){
		
		extract($args);
		echo $before_widget;
		bd_checkout($instance['text']);
		echo $after_widget;
	
	}

	/*-----------------------------------------------------------------------------------*/
	/*  Update Widget
	/*-----------------------------------------------------------------------------------*/
	function update($new_instance, $old_instance){
		$instance = $old_instance;

		$instance['text'] = $new_instance['text'];

		return $instance;
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Displays the widget settings controls on the widget panel
	/*-----------------------------------------------------------------------------------*/
	function form($instance){

			// Set up some default widget settings
			$defaults = array(
				'text' => 'Checkout', 
			);
			$instance = wp_parse_args((array) $instance, $defaults);

		?>
		<p>
			<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text (e;g : Checkout)', 'wolf' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" value="<?php echo $instance['text']; ?>">
		</p>
		<?php
	}

	

}
?>