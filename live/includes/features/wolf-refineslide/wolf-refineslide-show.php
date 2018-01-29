<?php
class Wolf_RefineSlide_Show{

	function __construct(){
		add_action('wp_print_styles', array( $this, 'print_styles') );
		add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts') );
	}

	// --------------------------------------------------------------------------

	function print_styles(){

		wp_register_style('refineslide', WOLF_REFINESLIDE_URL.'css/refineslide.css', array(), '0.1', 'all');
		wp_register_style('refineslide-theme-dark', WOLF_REFINESLIDE_URL.'css/refineslide-theme-dark.css', array(), '0.1', 'all');
		
		if( is_front_page() ){
			if( wolf_get_theme_option('header') == 'slider' ){
				wp_enqueue_style('refineslide');
				wp_enqueue_style('refineslide-theme-dark');
			}
		}
	}

	// --------------------------------------------------------------------------

	function enqueue_scripts(){

		wp_register_script('refineslide', WOLF_REFINESLIDE_URL.'js/jquery.refineslide.min.js', 'jquery', '0.3', true);
		
		if( is_front_page() ){
			if( wolf_get_theme_option('header') == 'slider' ){
				wp_enqueue_script('refineslide');
			}
		}
			
	}

	// --------------------------------------------------------------------------

	function show()
	{
		global $wpdb, $options;

		if(!defined('ICL_LANGUAGE_CODE'))
			define('ICL_LANGUAGE_CODE', 'en');

		$sliders_tbl = $wpdb->prefix.'wolf_refineslide';
	    	$slides = $wpdb->get_results("SELECT * FROM $sliders_tbl WHERE language_code='".ICL_LANGUAGE_CODE."' ORDER BY position");
	    	$o = get_option('wolf_refineslide_settings');
	    	$nav =$o['navigation'];
	    	if($nav == 'thumbs') {

	    		$nav = 'useThumbs : true,     // Bool (default true): Navigation type thumbnails
useArrows : false,';

	    	} elseif ( $nav == 'arrows' ) {
	    		$nav = 'useThumbs : false,     // Bool (default true): Navigation type thumbnails
useArrows : true,';
	    	} else {
	    		$nav = 'useThumbs : false,     // Bool (default true): Navigation type thumbnails
useArrows : false,';
	    	}
	    		



		?>
		<script>
			jQuery(window).load(function(){
				jQuery('.rs-wrap').css({'background' : 'none'});
			});

		   jQuery(function ($) {
		        $('.rs-slider').refineSlide({
		        	maxWidth              : 1140,
		        	transition            : "<?php echo $o['effect']; ?>", 
			fallback3d            : 'sliceV', 
			<?php echo $nav; ?> // String (default 'thumbs'): Navigation type ('thumbs', 'arrows', null)
			thumbMargin      : 3,        // Int (default 3): Percentage width of thumb margin
			autoPlay              : <?php echo $o["autoplay"]; ?>,
			delay                 : <?php echo $o["delay"]; ?>, 
			transitionDuration    : <?php echo $o["transition_duration"]; ?>, 
			startSlide            : 0, 
			keyNav                : true, 
			captionWidth          : 50, 
arrowTemplate         : '<div class="rs-arrows"><a href="#" class="rs-prev"></a><a href="#" class="rs-next"></a></div>', // String: The markup used for arrow controls (if arrows are used). Must use classes '.rs-next' & '.rs-prev'


		        });
		    });
		</script>
		<?php
		$html = '<ul class="rs-slider">';
		
		if ($slides){
			foreach ($slides as $slide) {
				
		
		    $html .='<li>';

	   		if($slide->link){
				$html .='<a href="'.esc_url($slide->link).'">';
			}

		    	$html .='<img src="'. WOLF_REFINESLIDE_FILES_URL.'slides/'.$slide->img.'" alt="">';

		    	if($slide->link){
				$html .= '</a>';
			}
			if($slide->caption){
				$caption_position = 'rs-' . $slide->caption_position;

				$html .= '<div class="rs-caption '.$caption_position.'">
		                            <p>'.stripslashes($slide->caption).'</p>
		                        </div>';
	                          }

		    $html .='</li>';
		

			}
		}
		$html .='</ul>';

		echo $html;
	}

} // end class

global $wolf_show_refineslide;
$wolf_show_refineslide = new Wolf_RefineSlide_Show;

function wolf_refineslide(){

	global $wolf_show_refineslide;
	$wolf_show_refineslide->show();

}
?>