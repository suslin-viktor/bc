<?php
/*-----------------------------------------------------------------------------------*/
/*  SEO
/*-----------------------------------------------------------------------------------*/
/** 
* SEO :
* Force to disable or enable SEO feature
* Ususally, we can disable the SEO feature in the theme options
*/
define('BD_SEO', true);

/**
* Are there any third party SEO plugins active ?
* 
* @return bool True is other plugin is detected
*/
function bd_is_third_party_seo(){
    include_once( ABSPATH .'wp-admin/includes/plugin.php' );

    if( is_plugin_active('headspace2/headspace.php') ) return true;
    if( is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php') ) return true;
    if( is_plugin_active('wordpress-seo/wp-seo.php') ) return true;

    return false;
}

/**
* Do we have to generate SEO meta with the BrutalDesign Framework ?
*/
function bd_do_seo(){
            global $options;
            $theme_options = get_option('bd_theme_options');
           if(!bd_is_third_party_seo() && BD_SEO == true && !isset($theme_options['disable_seo']) ){
                return true;
            }else{
                return false;
           }


}

/**
* Do we have to generate facebook meta (open graph) ?
*/
function bd_do_facebook_meta(){
           global $options;
                
          $wolf_share_settings = get_option('wolf_share_settings');
            if( !empty($wolf_share_settings) && !empty($wolf_share_settings['facebook_meta']) && $wolf_share_settings['facebook_meta'] == 1 && bd_do_seo() ){
                 return true;
             }else{
                return false;
            }
}


/**
 * Edit the Title
 */
function bd_metabox_seo_title($title) {
    global $post;

    if( $post && bd_do_seo() ) {
        if( is_home() || is_archive() || is_search() ) { 
            $postid = get_option('page_for_posts'); 
        } else {
            $postid = $post->ID;
        }
        
        if( $seo_title = get_post_meta( $postid, 'bd_seo_title', true ) ) {
            return $seo_title;
        }
    }
    return $title;
}
add_filter('wp_title', 'bd_metabox_seo_title');


/**
* A little trick using ob_ php function to get the current WP title
*/
function bd_get_wp_title(){

	ob_start();
	wp_title();
	$wp_title = ob_get_contents();
	ob_end_clean();
	$wp_title = preg_replace("/&#?[a-z0-9]{2,8};/i","",$wp_title);
	$wp_title = preg_replace ("/\s+/", " ", $wp_title);
	return $wp_title;

}

/**
* Generate Title for facebook
*/
function bd_fb_seo_title(){
	global $post;

          if(!is_404() && $post){
            	if( $seo_title = get_post_meta( $post->ID, 'bd_seo_title', true) )
            		$title = $seo_title;
            	else
            		$title = bd_get_wp_title();

            	$meta ='<meta property="og:title" content="'.$title.'" />' . "\n"; 

           }

	if( !empty($title) && bd_do_facebook_meta() )
		echo $meta;
}
add_action('bd_meta_head', 'bd_fb_seo_title');


/**
 * Add  Description Meta
 */
function bd_metabox_seo_description() {
    global $post;
    
    if( $post && bd_do_seo() ) {
        if( is_home() || is_archive() || is_search() ) { 
            $postid = get_option('page_for_posts'); 
        } else {
            $postid = $post->ID;
        }
        
        if( $seo_description = get_post_meta( $postid, 'bd_seo_description', true ) ){
            echo '<meta name="description" content="'. esc_html(strip_tags($seo_description)) .'" />' . "\n";
            echo '<meta itemprop="description" content="'. esc_html(strip_tags($seo_description)) .'">' . "\n";
            if( bd_do_facebook_meta() )
                echo '<meta property="og:description" content="'. esc_html(strip_tags($seo_description)) .'">' . "\n";
        }
    }
}
add_action('bd_meta_head', 'bd_metabox_seo_description');


/**
 * Add Keywords Meta
 */
function bd_metabox_seo_keywords() {
    global $post;
    
    if( $post && bd_do_seo() ) {
        if( is_home() || is_archive() || is_search() ) { 
            $postid = get_option('page_for_posts'); 
        } else {
            $postid = $post->ID;
        }
        
        if( $seo_keywords = get_post_meta( $postid, 'bd_seo_keywords', true ) ){
            echo '<meta name="keywords" content="'. esc_html(strip_tags($seo_keywords)) .'" />' . "\n";
        }
    }
}
add_action('bd_meta_head', 'bd_metabox_seo_keywords');


/**
 * Add Robots Meta
 */
function bd_metabox_seo_robots() {
    global $post;
    
    if( $post && bd_do_seo() && get_option('blog_public') == 1 ){
        if( is_home() || is_archive() || is_search() ) { 
            $postid = get_option('page_for_posts'); 
        } else {
            $postid = $post->ID;
        }
        
        $seo_index = get_post_meta( $postid, 'bd_seo_index', true );
        $seo_follow = get_post_meta( $postid, 'bd_seo_follow', true );
        if( !$seo_index ) $seo_index = 'index';
        if( !$seo_follow ) $seo_follow = 'follow';
        
        if( !($seo_index == 'index' && $seo_follow == 'follow') )
            echo '<meta name="robots" content="'. $seo_index .','. $seo_follow .'" />' . "\n";
    }
}
add_action('bd_meta_head', 'bd_metabox_seo_robots');
?>