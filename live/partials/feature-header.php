<?php 
/**
 * The is where we display the content of the feature header on the home page
 * Slider, Video, etc... 
 *
 */

$wolf_feature = wolf_get_theme_option('header'); ?>

<section id="hello">
	<?php 

	/* If the Slider is choosen
	-------------------------------------------*/
	if( $wolf_feature == 'slider' ): 
		
		?>
		<div class="wrap" style="padding-bottom:30px">
			<?php if(function_exists('wolf_refineslide')) wolf_refineslide(); ?>
		</div>
		<?php 

	/* If the embed object is choosen
	-------------------------------------------*/
	elseif($wolf_feature == 'embed'): ?>
	
	<div class="wrap">
		<div id="embed-container">  
			<?php echo wolf_format_custom_content_output( stripslashes(wolf_get_theme_option('embed_header')) ); ?>
		</div>
	</div>
			
	<?php 

	/* If the Static Image is choosen
	-------------------------------------------*/
	elseif($wolf_feature == 'static'):
	
		if(wolf_get_theme_option('static_header')): ?>
		<div id="fixheader-container">
			<img src="<?php echo wolf_get_theme_option('static_header'); ?>" alt="<?php bloginfo('name'); ?>">
		</div>
		<?php endif;
	
	endif; ?>
</section><!-- #feature-container -->
