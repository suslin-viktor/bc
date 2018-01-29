<?php require_once 'wp.php' ?>
<?php get_header(); ?>
	<div class="wrap" id="main-content">
		<h1><?php _e('Thank you!', 'wolf'); ?></h1>
		<p><?php _e('Your order has been processed successfully.', 'wolf'); ?></p>
		<?php
			global $options;

			$bd_paypal_options = get_option('bd_paypal_settings');

			if( $bd_paypal_options && isset($bd_paypal_options['confirm']) && $bd_paypal_options['confirm'] == 'true' ){
				?>
				<p><?php _e('You will recieve a confirmation email.', 'wolf'); ?></p>
				<?php
			}

		?>
		<p><a href="<?php echo home_url(); ?>/">&larr; <?php _e('back home', 'wolf'); ?></a></p>
	</div>
<?php get_footer(); ?>