<?php

if ( ! class_exists( 'Wolf_Theme_Share' ) ) {

class Wolf_Theme_Share{

	function __construct() {

		if ( ! defined( 'BD_SEO' ) )
			define('BD_SEO', true);

		define('WOLF_SHARE_URL', WOLF_THEME_URL . '/includes/features/' . basename( dirname( __FILE__ ) ) );
		define('WOLF_SHARE_DIR', dirname(__FILE__));

		add_action('wolf_meta_head', array($this, 'meta'));

		add_action('after_setup_theme', array($this, 'options_init') );
		add_action('wp_print_styles', array($this, 'print_styles'));
		add_action('wolf_body_start', array($this, 'output_script'));
		add_shortcode('wolf_share', array($this, 'shortcode'));

		add_action('admin_init', array($this, 'admin_init'));
		add_action('admin_init', array($this, 'save'));
		add_action('admin_menu',  array($this, 'share_plugin_init'));
		add_action('admin_print_styles', array($this, 'print_styles'));
		add_action('admin_head', array($this, 'admin_js'));

	}

	// --------------------------------------------------------------------------

	/**
	* Share buttons css styles
	*/
	function print_styles()
	{
		wp_register_style('wolf-share', WOLF_SHARE_URL . '/css/share.css', array(), '0.1', 'all');
		wp_enqueue_style('wolf-share');
	}

	// --------------------------------------------------------------------------

	/**
	* Set default settings
	*/
	function options_init()
	{
		global $options;

		if ( false ===  get_option('wolf_share_settings')  ) {

			$default = array(
				//'text' => 'Share this post',
				'show_twitter' => 1,
				'show_facebook' => 1,
				'show_google' => 1,
				'show_pinterest' => 1,
				'layout' => 'horizontal',
				'facebook_meta' => 1,
				'google_meta' => 1,
			);

			add_option( 'wolf_share_settings', $default );
		}
	}

	// --------------------------------------------------------------------------

	function admin_js()
	{

		if(isset($_GET['page']) && $_GET['page'] == basename(__FILE__)){

			echo  "<script type='text/javascript'>
		      	 jQuery(document).ready(function($){
				$('#share-image-reset').click(function(){
			 		$('#share-image-set').val('');
			 		$('#share-image-preview').hide();
					return false;  });
				$('#wolf-share-submit').click(function(){
			 		$('#loader').show();
				 });

		      	});</script>";
		}
	}


	// --------------------------------------------------------------------------


	/**
	* Add menu
	*/
	function share_plugin_init()
	{
		// Add Contextual menu
		add_menu_page(__('Share', 'wolf'), __('Share', 'wolf'), 'administrator', basename(__FILE__), array($this, 'share_settings') );

	}

	// --------------------------------------------------------------------------


	/**
	* Add Settings
	*/
	function admin_init()
	{
		register_setting( 'wolf-share', 'wolf_share_settings', array($this, 'settings_validate') );
		add_settings_section( 'wolf-share', '', array($this, 'section_intro'), 'wolf-share' );
		//add_settings_field( 'text', __( 'Text on single post', 'wolf' ), array($this, 'setting_text'), 'wolf-share', 'wolf-share' );
		add_settings_field( 'buttons', __( 'Show', 'wolf' ), array($this, 'setting_buttons'), 'wolf-share', 'wolf-share' );
		//add_settings_field( 'layout', __( 'Layout', 'wolf' ), array($this, 'setting_layout'), 'wolf-share', 'wolf-share' );
		add_settings_field( 'facebook_meta', __( 'Generate Facebook Meta', 'wolf' ), array($this, 'setting_facebook_meta'), 'wolf-share', 'wolf-share' );
		add_settings_field( 'google_meta', __( 'Generate Google+ Meta', 'wolf' ), array($this, 'setting_google_meta'), 'wolf-share', 'wolf-share' );
		add_settings_field( 'image', __( 'Default facebook and google share image', 'wolf' ), array($this, 'setting_image'), 'wolf-share', 'wolf-share' );
		add_settings_field( 'preview', __( 'Preview', 'wolf' ), array($this, 'setting_preview'), 'wolf-share', 'wolf-share' );
		add_settings_field( 'instructions', __( 'Shortcode and Template Tag', 'wolf' ), array($this, 'setting_instructions'), 'wolf-share', 'wolf-share' );
		add_settings_field( 'tip', __( 'Tip', 'wolf' ), array($this, 'setting_tip'), 'wolf-share', 'wolf-share' );
	}

	// --------------------------------------------------------------------------

	function section_intro()
	{
		//global $options;
		//debug(get_option('wolf_share_settings'));
		//debug($_POST);
		?>
		<?php
	}

	// --------------------------------------------------------------------------

	function setting_text()
	{
		echo '<input type="text" name="wolf_share_settings[text]" class="regular-text" value="'. stripslashes($this->get_share_option('text')) .'">';
	}

	// --------------------------------------------------------------------------

	function setting_buttons()
	{
		echo '<input type="hidden" name="wolf_share_settings[show_twitter]" value="0">
		<label><input type="checkbox" name="wolf_share_settings[show_twitter]" value="1"'. (($this->get_share_option('show_twitter') == 1) ? ' checked="checked"' : '') .'>
		Twitter</label><br />
		<input type="hidden" name="wolf_share_settings[show_facebook]" value="0">
		<label><input type="checkbox" name="wolf_share_settings[show_facebook]" value="1"'. (($this->get_share_option('show_facebook') == 1) ? ' checked="checked"' : '') .'>
		Facebook</label><br />
		<input type="hidden" name="wolf_share_settings[show_google]" value="0">
		<label><input type="checkbox" name="wolf_share_settings[show_google]" value="1"'. (($this->get_share_option('show_google') == 1) ? ' checked="checked"' : '') .'>
		Google</label><br />
		<input type="hidden" name="wolf_share_settings[show_pinterest]" value="0">
		<label><input type="checkbox" name="wolf_share_settings[show_pinterest]" value="1"'. (($this->get_share_option('show_pinterest') == 1) ? ' checked="checked"' : '') .'>
		Pinterest</label><br />';
	}

	// --------------------------------------------------------------------------

	function setting_layout()
	{
		?>
		<select name="wolf_share_settings[layout]" id="layout">
			<option value="horizontal" <?php if( $this->get_share_option('layout') == 'horizontal' ) echo 'selected="selected"'; ?>>horizontal</option>
			<option value="vertical" <?php if( $this->get_share_option('layout') == 'vertical' ) echo 'selected="selected"'; ?>>vertical</option>
		</select>
		<?php
	}

	// --------------------------------------------------------------------------


	function setting_facebook_meta()
	{
		echo '<input type="hidden" name="wolf_share_settings[facebook_meta]" value="0" />
		<label><input type="checkbox" name="wolf_share_settings[facebook_meta]" value="1"'. (($this->get_share_option('facebook_meta')) ? ' checked="checked"' : '') .' />
		'.__('By default, this plugin generates facebook meta automatically. If you use a third-party facebook plugin, you can disable this option to avoid conflicts', 'wolf').'.</label><br />';
	}

	// --------------------------------------------------------------------------

	function setting_google_meta()
	{
		echo '<input type="hidden" name="wolf_share_settings[google_meta]" value="0" />
		<label><input type="checkbox" name="wolf_share_settings[google_meta]" value="1"'. (($this->get_share_option('google_meta')) ? ' checked="checked"' : '') .' /></label><br />';
	}

	// --------------------------------------------------------------------------

	function setting_image()
	{
		?>

	 	<input name="share_image" id="share_image" type="file">
	 	<input type="hidden" name="share_image_set" id="share-image-set" value="<?php if ( $this->get_share_option('share_image') != "") { echo "set";   } else { echo ""; } ?>">
	 	<a href="#" id="share-image-reset">reset</a>

	 	<div style="clear:both"></div>
		<?php if ( $this->get_share_option('share_image') != null): ?>
		<div style="float:left; margin: 5px; width:80px; min-height:10px">
			<img id="share-image-preview" src="<?php echo WOLF_SHARE_URL.'/userimage/'.$this->get_share_option('share_image'); ?>?<?php echo time(); ?>" alt="" width="80">
		</div>
		<?php endif; ?>
		<br><label for="share_image"><?php _e('By default, the featured image or the first image of the shared post will be shown. If the post has no image, this image will be used', 'wolf'); ?>.</label>
		<?php

	}

	// --------------------------------------------------------------------------

	function setting_preview()
	{
		echo $this->render('horizontal', true);
		$this->output_script();
	}

	// --------------------------------------------------------------------------

	function setting_instructions()
	{
		echo '<p>'.__('To use BrutalDesign Share in your pages you can use the shortcode', 'wolf').':</p>
		<p><code>[wolf_share layout="horizontal|vertical"]</code></p>';


		echo '<p>'.__('To use BrutalDesign Share manually in your theme template use the following PHP code', 'wolf').':</p>
			<p><code>&lt;?php if( function_exists(\'wolf_share\') ) wolf_share(\'horizontal | vertical\'); ?&gt;</code></p>';
	}

	// --------------------------------------------------------------------------

	function setting_tip()
	{
		echo '<p>'.__('You can check how it looks when you share an URL on your facebook wall by using this tool', 'wolf').':</p>
		<p><a href="https://developers.facebook.com/tools/debug" target="_blank">https://developers.facebook.com/tools/debug</a></p>';

	}

	// --------------------------------------------------------------------------

	function settings_validate($input)
	{
		//$input['text'] = sanitize_text_field( $input['text'] );

		return $input;
	}

	// --------------------------------------------------------------------------


	function save()
	{
		if(isset($_POST['wolf-share-settings-save']) && isset($_GET['page']) && $_GET['page'] == basename(__FILE__) ) :

			if ( !empty($_POST) || wp_verify_nonce($_POST['wolf-share'],basename(__FILE__)) ){

					$newoptions = $_POST['wolf_share_settings'];

					/* Image Upload */
					$img_dir = WOLF_SHARE_DIR.'/userimage';
					if(!file_exists($img_dir)) mkdir($img_dir,0777);

					if(!empty($_FILES['share_image']['name'])){

						$tmp = $_FILES['share_image']['tmp_name'];
						$ext = pathinfo($_FILES['share_image']['name'], PATHINFO_EXTENSION);
						$img_name = mktime().'.'.$ext;
						$img_name = wp_unique_filename($img_dir, $img_name);
						$allowed_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png');

						if(in_array($_FILES['share_image']['type'], $allowed_types)){

							move_uploaded_file($tmp, $img_dir.'/'.$img_name);

							$newoptions['share_image'] = $img_name ;


						}

					}elseif( $_POST['share_image_set'] == null && $this->get_share_option('share_image') != null ){

						if(file_exists($img_dir.'/'.$this->get_share_option('share_image')))
							unlink($img_dir.'/'.$this->get_share_option('share_image'));

					 	unset($newoptions['share_image']);

					}else{

						$newoptions['share_image'] = $this->get_share_option('share_image');

					}

				update_option('wolf_share_settings', $newoptions);


			}else{
				print 'Sorry, your nonce did not verify.';
	  			exit;
	  		}

	  	endif; // end if post

	}

	// --------------------------------------------------------------------------


	function share_settings()
	{
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2><?php _e('Share Buttons Settings', 'wolf'); ?></h2>
			<form action="<?php echo admin_url('admin.php?page='. basename(__FILE__)); ?>" method="post" enctype="multipart/form-data">
				<?php settings_fields( 'wolf-share' ); ?>
				<?php do_settings_sections( 'wolf-share' ); ?>
				<p class="submit"><input style="float:left" id="wolf-share-submit" name="wolf-share-settings-save" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wolf' ); ?>" />
				<img id="loader" style="display:none; margin-top:3px" src="<?php echo admin_url('images/loading.gif'); ?>" alt="loader">
				<div style="float:none; clear:both"></div>
				</p>
			</form>
		</div>
		<?php
	}

	// --------------------------------------------------------------------------


	function get_share_option($value = null)
	{
	            global $options;

	            $wolf_share_settings = get_option('wolf_share_settings');
	            if( isset($wolf_share_settings[$value]) )
	                return $wolf_share_settings[$value];
	            else
	                return null;
	}

	// --------------------------------------------------------------------------


	function is_third_party_seo()
	{
		include_once( ABSPATH .'wp-admin/includes/plugin.php' );

		if( is_plugin_active('headspace2/headspace.php') ) return true;
		if( is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php') ) return true;
		if( is_plugin_active('wordpress-seo/wp-seo.php') ) return true;

		return false;
	}

	// --------------------------------------------------------------------------


	function bd_seo_is_on()
	{
		global $options;
		$theme_options = get_option('bd_theme_options');

		if( defined('BD_SEO') && BD_SEO && !$this->is_third_party_seo() && !isset($theme_options['disable_seo']) ){
			return true;
		}else{
			return false;
		}
	}

	// --------------------------------------------------------------------------

	function do_meta()
	{
		if(!$this->is_third_party_seo() && !$this->bd_seo_is_on()){
			return true;
		}else{
			return false;
		}
	}

	// --------------------------------------------------------------------------

	function sample($text,$nbcar = 200)
	{
		$text= strip_tags($text);
		if(strlen($text)>$nbcar) {

			preg_match('!.{0,'.$nbcar.'}\s!si', $text, $match);
			$str= $match[0].'... ';

		}else {
			$str=$text;
		}

		$str = preg_replace('/\s\s+/', '', $str);
		return $str;
	}

	// --------------------------------------------------------------------------


	function shortcode($atts)
	{
		extract(shortcode_atts(array(
		      'layout' => 'horizontal',
		      ), $atts)
		);
		return $this->render($layout);
	}

	// --------------------------------------------------------------------------


	/**
	* Generate share buttons scripts
	*
	*/
	function output_script() {
		/* Put conditino in variables */
		$facebook = $this->get_share_option('show_facebook') == 1;
		$twitter = $this->get_share_option('show_twitter') == 1;
		$google = $this->get_share_option('show_google') == 1;
		$pinterest = $this->get_share_option('show_pinterest') == 1;

		$output = '';

		if( $facebook )
			$output .= '<div id="fb-root"></div>';

		if( $facebook || $twitter || $google || $pinterest ){
			$output .= '<script type="text/javascript">';

			$output .= '(function(doc, script) {
			        var js,
			        fjs = doc.getElementsByTagName(script)[0],
			        add = function(url, id) {
			            if (doc.getElementById(id)) {return;}
			            js = doc.createElement(script);
			            js.src = url;
			            id && (js.id = id);
			            fjs.parentNode.insertBefore(js, fjs);
			        };';
		}


		if( $facebook )
			// facebook
			$output .= 'add("//connect.facebook.net/en_US/all.js#xfbml=1", "facebook-jssdk");' . "\n";

		if( $twitter )
			// twitter
			$output .= 'add("//platform.twitter.com/widgets.js", "twitter-wjs");' . "\n";

		if( $google )
			// google plus
			$output .= 'add("https://apis.google.com/js/plusone.js");' . "\n";

		if( $pinterest )
			// pinterest
			$output .= 'add("//assets.pinterest.com/js/pinit.js");' . "\n";

		if( $facebook || $twitter || $google || $pinterest )
			$output .= '}(document, "script"));</script>' . "\n";



		// $output .='<script>
		// (function(d, s, id) {
		//       var js, fjs = d.getElementsByTagName(s)[0];
		//       if (d.getElementById(id)) return;
		//       js = d.createElement(s); js.id = id;
		//       js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
		//       fjs.parentNode.insertBefore(js, fjs);
		//     }(document, \'script\', \'facebook-jssdk\'));</script>'. "\n";
		// }

		// if($google){
		// 	$output .= '<script type="text/javascript">
		// 	    (function() {
		// 	    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
		// 	    po.src = \'https://apis.google.com/js/plusone.js\';
		// 	    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
		// 	    })();
		// 	    </script>';
		//   }

		// if($twitter){
		// 	$output .= '<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>';
		// }

		// if($pinterest){
		// 	//$output .= '<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>';
		// }
		echo $output;
	}

	// --------------------------------------------------------------------------


	function get_wp_title()
	{
		ob_start();
		wp_title();
		$wp_title = ob_get_contents();
		ob_end_clean();
		$wp_title = preg_replace("/&#?[a-z0-9]{2,8};/i","",$wp_title);
		$wp_title = preg_replace ("/\s+/", " ", $wp_title);
		return $wp_title;

	}

	// --------------------------------------------------------------------------


	/**
	* Get an image for facebook meta.
	* This function search the post featured image or, if no featured image is set, the first image in the post content
	* If the post don't have any image, the deafult facebook thumbnail set in the theme options will be used
	* $this->get_share_option(bd_fbimage)
	*/
	function get_fb_image()
	{
		global $post, $options;

		/* We define the default image first and see if the post contains an image after */
		$fbimage = WOLF_SHARE_URL.'/share_image.jpg';
		if($this->get_share_option('share_image')!='')
			$fbimage = WOLF_SHARE_URL.'/userimage/'.$this->get_share_option('share_image');

		if(!is_404() && !is_search()){
			if ( $post ){
				if ((function_exists('has_post_thumbnail')) && (has_post_thumbnail())) {
					$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium');
					$fbimage = $src[0];
				}else{

					$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i',
					$post->post_content, $matches);
					if( $matches [1] )
						$fbimage = $matches [1] [0];
				}
			}

		}

		return $fbimage;
	}

	// --------------------------------------------------------------------------

	/**
	* Get the post description
	* Create a sample of the excerpt if it exists, or get the site description
	**/
	function get_decription()
	{
		global $post;
		$excerpt = '';
		if (!is_404() && !is_search() && $post):
			$excerpt = $post->post_excerpt;
			if($excerpt==''){
				$excerpt = strip_tags(preg_replace( '/'.get_shortcode_regex().'/i', '', $post->post_content));
				$excerpt = preg_replace ("/\s+/", " ", $excerpt);
				$excerpt = $this->sample($excerpt);
			}
		endif;
		if($excerpt == null )
			$excerpt = get_bloginfo ( 'description' );

		return $excerpt;
	}

	// --------------------------------------------------------------------------

	/**
	* Generate facebook and googleplus metatags if SEO plugin is off
	* Otherwise, that's the SEO plugin that will do it
	**/
	function meta()
	{
		global $post;

		if (!is_404() && $post):

		if (is_single() || is_page() )
			$fb_type = 'article';
		else
			$fb_type = 'website';

		$fbmeta = '';
		$gpmeta = '';
		$sitename = get_bloginfo( 'name' );

		/*g+*/
		$gpmeta .='<meta itemprop="name" content="'.$sitename.'">' . "\n";

		$gpmeta .= '<meta itemprop="image" content="'.$this->get_fb_image().'">' . "\n";

		/*fb*/

		$fbmeta .='<meta property="og:site_name" content="'.$sitename.'" />' . "\n";

		$fbmeta .='<meta property="og:url" content="'.get_permalink().'" />' . "\n";

		$fbmeta .='<meta property="og:image" content="'.$this->get_fb_image().'" />' . "\n";

		if(isset($fb_type)){
			$fbmeta .='<meta property="og:type" content="'.$fb_type.'" />' . "\n";
		}

		if( $this->bd_seo_is_on() && get_post_meta( $post->ID, 'bd_seo_description', true ) == null ){
			$gpmeta .='<meta itemprop="description" content="'.$this->get_decription().'">' . "\n";
			$fbmeta .='<meta property="og:description" content="'.$this->get_decription().'" />' . "\n";
		}

		if( $this->do_meta()){
			$fbmeta .='<meta property="og:title" content="'.$this->get_wp_title().'" />' . "\n";
			$gpmeta .='<meta itemprop="description" content="'.$this->get_decription().'">' . "\n";
			$fbmeta .='<meta property="og:description" content="'.$this->get_decription().'" />' . "\n";
		}


		if( $this->get_share_option('facebook_meta') == 1 )
			echo $fbmeta;

		if( $this->get_share_option('google_meta') == 1 )
			echo $gpmeta;

		endif;
	}

	// --------------------------------------------------------------------------


	function render($layout = 'horizontal', $is_preview = false)
	{
	          global $post;
	          /* conditions */
	          $facebook = $this->get_share_option('show_facebook') == 1;
	          $twitter = $this->get_share_option('show_twitter') == 1;
	          $google = $this->get_share_option('show_google') == 1;
	          $pinterest = $this->get_share_option('show_pinterest') == 1;



		$output = '';

		$permalink = get_permalink();
		if($is_preview)
			$permalink = 'https://www.facebook.com/wordpresswolf';

		//$layout = $this->get_share_option('layout');

	          if( $this->bd_seo_is_on() && $post &&  get_post_meta( $post->ID, 'bd_seo_description', true ) != null){
	          		$title='data-text="'.get_post_meta( $post->ID, 'bd_seo_description', true ).'"';
	          }else{

	            	$title='data-text="'.get_the_title().' | '.get_bloginfo( 'name' ).'"';
	          }

	          if($is_preview)
	          		$title='data-text="Share this Plugin"';

	          	if(is_single())
	          		$output .= '<p class="wolf-share-text">'.stripslashes($this->get_share_option('text')).'</p>';

	             $output .= '<div class="wolf-share-container '.$layout.'">';

	             if($layout == 'horizontal') {


			if($facebook) {
				//$output .= '<div id="fb-root"></div>';
				$facebook_layout = 'data-width="100" data-height="80" data-layout="button_count"';
				//$permalink = 'http://wolfthemes.com';
				$output .= '<div class="wolf-share-facebook">';
				$output .= '<div class="fb-like" data-href="' . $permalink . '" data-send="false" '.$facebook_layout.' data-show-faces="false" data-font="verdana"></div>';
				$output .= '</div>';
			}

		}

		 if( $layout == 'vertical' ) {

		 	if( $facebook ){
		 		$facebook_layout = 'data-width="54" data-height="54" data-layout="box_count"';
		 		$output .= '<div class="wolf-share-facebook">';
				$output .= '<div class="fb-like" data-href="' . $permalink . '" data-send="false" '.$facebook_layout.' data-show-faces="false" data-font="verdana"></div>';
				$output .= '</div>';
			}
	             }



		if($twitter){
			$twitter_data_link = 'data-url="'.$permalink.'"';

			$twitter_layout = '';
			if($layout == 'vertical')
				$twitter_layout = ' data-count="vertical"';

			$output .= '<div class="wolf-share-twitter">';
			$output .= '<a href="http://twitter.com/share" '.$twitter_data_link.' '.$title.' class="twitter-share-button"'.$twitter_layout.'>Tweet</a>';
			$output .= '</div>';
		}

		if($google){

			if($layout == 'horizontal')
				$google_layout = 'medium';
			else
				$google_layout = 'tall';

			$output .= '<div class="wolf-share-google">';
			$output .= '<div class="g-plusone" data-size="'.$google_layout.'" data-href="'.$permalink.'"></div>';
			$output .= '</div>';
		}

		if($pinterest){

			if( $layout == 'horizontal')
				$pinit_layout = 'beside';
			else
				$pinit_layout = 'above';

			$output .= '<div class="wolf-share-pinterest">';
$output .= '<a href="http://pinterest.com/pin/create/button/
?url='.$permalink.'
&media='.$this->get_fb_image().'
&description='.urlencode($this->get_decription()).'"
data-pin-do="buttonPin"
data-pin-config="' . $pinit_layout . '"
data-pin-height="28">
<img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_28.png" /></a></a>';
			$output .= '</div>';
		}

		$output .= '<div style="clear:both; float:none"></div></div>';

		if( $facebook || $twitter || $google || $pinterest ) //main condition
			return '<div style="margin:0; padding:0; height:0; width:0; clear:both"></div>'.$output;

	} // end function

	// --------------------------------------------------------------------------

	function render_mobile( $text )
	{
		global $post;
		/* conditions */
		$facebook = $this->get_share_option('show_facebook') == 1;
		$twitter = $this->get_share_option('show_twitter') == 1;
		$google = $this->get_share_option('show_google') == 1;
		$pinterest = $this->get_share_option('show_pinterest') == 1;

		$img_path = WOLF_THEME_URL . '/images/share/';
		$desc = $this->get_decription();
		$output = '';

		$permalink = get_permalink();
		$share_title = get_the_title().' | '.get_bloginfo( 'name' );


		$display_text = '';

		$output .= '<div style="clear:both"></div><div class="wolf-share-mobile">' . $display_text . ' ';

		if($facebook){
			$fb_link = 'http://www.facebook.com/sharer.php?u=' . $permalink . '&t='.urlencode($share_title).'';
			$output .= '<a href="' . $fb_link . '">';
			$output .= '<img src="' . $img_path . 'facebook.png" alt="facebook">';
			$output .= '</a>';
		}

		if($twitter){
			$tw_link = 'http://twitter.com/home?status='. urlencode($share_title.' - ').$permalink;
			$output .= '<a href="' . $tw_link . '">';
			$output .= '<img src="' . $img_path . 'twitter.png" alt="twitter">';
			$output .= '</a>';
		}

		if($google){
			$gg_link = 'https://plus.google.com/share?url=' . urlencode($permalink) . '';
			$output .= '<a href="' . $gg_link . '">';
			$output .= '<img src="' . $img_path . 'googleplus.png" alt="google plus">';
			$output .= '</a>';
		}

		if($pinterest){
			$pin_link = 'http://pinterest.com/pin/create/button/?url=' .$permalink. '&media=' .$this->get_fb_image(). '&description='.$desc;
			$output .= '<div class="wolf-pin" ><a count-layout="none" href="' . $pin_link . '">';
			// $output .= '<img src="' . $img_path . 'pinterest.png" alt="pinterest">';
			$output .= '</a></div>';
		}

		$output .= '</div><div style="clear:both"></div>';

		if( $facebook || $twitter || $google || $pinterest )
			return $output;
	}

	// --------------------------------------------------------------------------


} // end class
global $wolf_share;
$wolf_share = new Wolf_Theme_Share;

function wolf_is_share() {
	global $options;
	$o = get_option('wolf_share_settings');

	if( $o ){
		if( isset($o['show_twitter']) || isset($o['show_facebook']) || isset($o['show_google'])  || isset($o['show_pinterest']) ){
			if( $o['show_twitter'] == 1 || $o['show_facebook'] == 1 || $o['show_google'] == 1 || $o['show_pinterest'] == 1 )
				return true;
		}
	}

}

function wolf_share( $layout ){
	global $wolf_share;
	echo $wolf_share->render($layout);
}

function wolf_share_mobile( $text = true ){
	global $wolf_share;
	echo $wolf_share->render_mobile($text);
}


} // end class check
