<?php
/**
 * The Sidebar containing the main widget areas.
 */
?>
<aside id="secondary" class="widget-area" role="complementary">
	<div class="sidebar-inner">
		<?php 
		if ( wolf_is_woocommerce() ) {

			dynamic_sidebar( 'woocommerce' ); 

		} elseif ( function_exists( 'wolf_sidebar' ) ) {
			
			wolf_sidebar();

		} else {

			dynamic_sidebar( 'main' ); 
		}
				
		?>
	</div>
</aside><!-- #secondary .sidebar-container -->