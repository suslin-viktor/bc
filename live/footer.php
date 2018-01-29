<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 */
?>
		</div><!-- .wrap -->
	</section><!-- section#main -->
	<?php //phpinfo(); ?>
	<?php wolf_bottom_holder(); ?>
	
	<?php wolf_footer_before(); ?>
	
	<footer id="colophon" class="site-footer" role="contentinfo">
		<div id="bottom-bar">
			<div class="container">
				<div class="row">
					<div class="col-sm-12 col-md-9 col-lg-offset-3 col-lg-6">
						<nav id="site-navigation-tertiary" class="navigation tertiary-navigation" role="navigation">
							<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_id' => 'bottom-menu', 'menu_class' => 'nav-menu-tertiary', 'fallback_cb'  => '' ) ); ?>
						</nav><!-- #site-navigation -->
					</div>
					<div class="col-sm-12 col-md-3 col-lg-3">
						<div class="holder-copy-box">
							<?php wolf_site_info(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

	</footer><!-- #colophon -->

	
	
	<?php wolf_footer_after(); ?>
</div><!-- #page .hfeed .site -->
<!--
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
-->

<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.matchHeight.js" type="text/javascript"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/main.js" type="text/javascript"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/ajax-handler.js" type="text/javascript"></script>

<?php wolf_body_end(); ?>

<?php wp_footer(); ?>
</body>
</html>