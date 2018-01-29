<?php
function wolf_flexslider_script()
{
	global $options; 

	$o = get_option( 'wolf_flexslider_settings' );

	$id = 'flexslider';

	$output ='<!-- Flexslider -->
		<script type="text/javascript">
			jQuery(window).load(function(){
				var firstImg = jQuery(\'.slides li\').first().find(\'img\').css(\'opacity\', 1);
				jQuery("#'.$id.'").flexslider({
					animation: "'.$o['effect'].'",
					controlNav: '.$o['control'].',
					directionNav: '.$o['direction'].',
					pauseOnHover: '.$o['pausehover'].', 
					slideshow: '.$o['autoplay'].',
					slideshowSpeed: '.$o['pausetime'].',
					animationSpeed: '.$o['duration'].', 
					touch: true
				});
				
			});</script>
		<!-- Flexslider -->';
	echo $output;

}
//add_action('wp_footer', 'wolf_flexslider_script' );

function wolf_flexslider_show()
{
	global $wpdb, $options; //database init

	if(!defined('ICL_LANGUAGE_CODE'))
		define('ICL_LANGUAGE_CODE', 'en');

	/* Table var */
	$sliders_tbl = $wpdb->prefix.'wolf_flexslider';
	/* Req */
    	$slides = $wpdb->get_results("SELECT * FROM $sliders_tbl WHERE language_code='".ICL_LANGUAGE_CODE."' ORDER BY position");

    	$o = get_option('wolf_flexslider_settings');

	$id = 'flexslider';
	
	wolf_flexslider_script();
	$html ='<div class="flexslider flexslider-'.$o['effect'].'" id="'.$id.'"><ul class="slides">';
	if ($slides){
		foreach ( $slides as $slide ) {
	
			$html .='<li>';
			if($slide->link){
				$html .='<a href="'.esc_url($slide->link).'">';
			}
			
			$html .='<img src="'.WOLF_FLEXSLIDER_FILES_URL.'slides/'.$slide->img.'" alt="slider">';
			
			if($slide->link){
				$html .= '</a>';
			}
			if($slide->caption){
				$html .= '<p class="flex-caption">'.stripslashes($slide->caption).'</p>';
			}
			$html .='</li>';
		}
	}
	$html .='</ul></div>';
	echo $html;
}
?>