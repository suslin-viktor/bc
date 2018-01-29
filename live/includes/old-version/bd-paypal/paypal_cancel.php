<?php require_once 'wp.php' ?>
<?php get_header(); ?>
	<div class="wrap" id="main-content">
		<h1><?php _e('Order Cancelled', 'wolf'); ?></h1>
		<p><?php _e('Your order has been cancelled.', 'wolf'); ?></p>
		<p><a href="<?php echo home_url(); ?>/">&larr; <?php _e('back home', 'wolf'); ?></a></p>
	</div>
<?php get_footer(); ?>