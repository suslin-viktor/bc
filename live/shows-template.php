<?php
/*
Template Name: Shows
*/
get_header();
wolf_page_before();
?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php echo do_shortcode( '[wolf_tour_dates]' ); ?>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php
get_sidebar();
wolf_page_after();
get_footer(); 
?>