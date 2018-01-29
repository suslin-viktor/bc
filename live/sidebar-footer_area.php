<?php
/**
 * The Sidebar containing the main widget areas.
 */
if ( is_active_sidebar( 'footer_area' ) ) :
?>
<section id="tertiary" class="sidebar-footer clearfix" role="complementary">
	<div class="sidebar-inner wrap">
		<div class="widget-area">
			<?php dynamic_sidebar( 'footer_area' ); ?>
		</div>
	</div>
</section><!-- .sidebar-footer -->
<?php endif; ?>
