<?php

if ( ! function_exists( 'wolf_get_iframe_video_url' ) ) :
/**
 * Get video URL from deprecated post meta if a video post type hasn't be updated
 */
function wolf_get_iframe_video_url( $iframe ) {

	if ( preg_match( '/src="([^"]*)"/i', $iframe, $match ) ) {
		$url = $match[1];
	}

	$video_url = $url;

	/* Is Vimeo
	-----------------------------------------------------*/
	$is_vimeo = preg_match('#vimeo#', $url);
	if($is_vimeo){
		$video_url = str_replace( array('video/', 'player.'), '', $url);
	}

	/* Is Youtube
	-----------------------------------------------------*/
	$is_youtub = preg_match('#youtub#', $url);
	if($is_youtub){
		$video_url = str_replace( array('embed/'), 'watch?v=', $url);
	}

	return esc_url( $video_url );

}
endif;


// delete_option( '_has_restored' );
// delete_option( '_w_to_woocommerce' );

if ( isset( $_GET['remove_old_store'] ) && $_GET['remove_old_store'] == 'true' ) {
	add_option( '_w_to_woocommerce', true );
	wp_redirect( admin_url() );
	exit();
}


include( 'old-version/upgrader.php' );

function restore_live_content() {


	$r = new Wolf_Live_Upgrader;
	?>
	<div class="wrap" style="padding-left:10px">
		<?php printf( __( '<strong>Your content has been upgraded successfully to suit to the new theme structure.</strong><br>
			Please install the required plugins if you didn\'t already did it.<br>
			If you encounter issues with your image sizes, please install <a href="%s">Regenerate Thumbnails</a> plugin and run the plugin in Tools -> Regenerate thumbnails to re-crop all your image automatically.', 'wolf' ),
			esc_url( 'http://wordpress.org/plugins/regenerate-thumbnails/' )
			); ?>
	</div>
	<?php

}

function wolf_bg_fallback( $bg_id ) {

	$theme_slug = WOLF_THE_THEME;
	$uploads = wp_upload_dir();
	$old_uploads_dir = $uploads['basedir'] . '/' . $theme_slug . '/bd-uploads';
	$old_uploads_url = $uploads['baseurl'] . '/' . $theme_slug . '/bd-uploads/';

	if (
		$bg_id
		&& is_dir( $old_uploads_dir )
		&& is_file( $old_uploads_dir . '/' . str_replace( 'http://', '', $bg_id ) )
	) {
		return $old_uploads_url . str_replace( 'http://', '', $bg_id );
	}

}

function restore_page() {
	add_submenu_page(
	    'options.php',
	    //'wolf-framework',
	    'restore',
	    'restore',
	    'edit_themes',
	    'restore_content',
	    'restore_live_content'
	);

}
add_action( 'admin_menu', 'restore_page', 8 );

function wolf_is_old_version() {

	return get_option( '_live_updated' ) && ! get_option( '_has_restored' );
}


function wolf_do_you_want_to_update_theme_core_from_1_6_5() {

	if ( wolf_is_old_version() ) {
		$update_this = sprintf( __( '<strong>Live! Theme Data Update Required</strong> &mdash; We need to update your install. <a href="%1$s" target="_blank">More Infos</a><br>
			It may take a couple of minutes. Please, don\'t refresh or close this page during the update.<br><br>
			<a href="%2$s" class="button-primary">Run the Updater</a>',
			'wolf' ),
			esc_url( 'http://help.wolfthemes.com/update-to-live-2-0/' ),
			esc_url( admin_url( 'admin.php?page=restore_content&restore=true' ) )
		);
		wolf_admin_notice( $update_this, 'updated' );
	}

	return false;
}
add_action( 'admin_notices', 'wolf_do_you_want_to_update_theme_core_from_1_6_5' );



function wolf_do_you_want_to_update_to_woocommerce() {

	if ( get_option( '_has_restored' ) && ! get_option( '_w_to_woocommerce' ) ) {
		$update_this = sprintf(__( '<strong>Would you like to remove the old store feature to use Woocommerce instead?</strong><br>
			Click on the button below if you agree or click on "Hide permanently" on the right to dimiss this message.<br><br>
			<a href="%s" class="button-primary">Remove In-buit Store</a>',
			'wolf' ),
			esc_url( admin_url( 'admin.php?remove_old_store=true' ) )

		);
		wolf_admin_notice( $update_this, 'updated', true, '_w_to_woocommerce' );
	}

	return false;
}
add_action( 'admin_notices', 'wolf_do_you_want_to_update_to_woocommerce' );



add_shortcode( 'bd_jplayer_playlist', 'bd_jplayer_playlist_deprecated' );
function bd_jplayer_playlist_deprecated(  $atts  ) {

	if ( is_user_logged_in() && ! wolf_get_theme_option( 'old_player' ) )

		return '<span class="deprecated">' . sprintf( __( 'The in-buit player is deprecated. Please Install <a href="%s">WolfjPlayer plugin</a>', 'wolf' ), 'http://wolfthemes.com/plugin/wolf-jplayer/' ) . '</span>';

}


function wolf_deprecated_post_types() {

	if ( wolf_is_old_version() ) {

		$labels = array(
		            'name' => __( 'Items', 'wolf' ),
		            'singular_name' => __( 'Item', 'wolf' ),
		            'add_new' => __( 'Add New', 'wolf' ),
		            'add_new_item' => __( 'Add New Item', 'wolf' ),
		            'all_items'  =>  __( 'All Items', 'wolf' ),
		            'edit_item' => __( 'Edit Item', 'wolf' ),
		            'new_item' => __( 'New Item', 'wolf' ),
		            'view_item' => __( 'View Item', 'wolf' ),
		            'search_items' => __( 'Search Items', 'wolf' ),
		            'not_found' => __( 'No Items found', 'wolf' ),
		            'not_found_in_trash' => __( 'No Items found in Trash', 'wolf' ),
		            'parent_item_colon' => '',
		            'menu_name' => __( 'Store', 'wolf' ),
		        );

		$args = array(

		    'labels' => $labels,
		    'public' => true,
		    'publicly_queryable' => true,
		    'show_ui' => true,
		    'show_in_menu' => true,
		    'query_var' => false,
		    'rewrite' => array( 'slug' => 'item' ),
		    'capability_type' => 'post',
		    'has_archive' => false,
		    'hierarchical' => false,
		    'menu_position' => 5,
		    'taxonomies' => array(),
		    'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
		    'exclude_from_search' => false,

		    // 'description' => __( 'My Wolf Plugin', 'wolf' ),
		    // 'menu_icon' => $icon_url
		);
		register_post_type( 'item', $args );



		$labels = array(
			'name' => __( 'Item Categories', 'wolf' ),
			'singular_name' => __( 'Item Type', 'wolf' ),
			'search_items' => __( 'Search Item Categories', 'wolf' ),
			'popular_items' => __( 'Popular Item Categories', 'wolf' ),
			'all_items' => __( 'All Item Categories', 'wolf' ),
			'parent_item' => __( 'Parent Item Category', 'wolf' ),
			'parent_item_colon' => __( 'Parent Item Category:', 'wolf' ),
			'edit_item' => __( 'Edit Item Category', 'wolf' ),
			'update_item' => __( 'Update Item Category', 'wolf' ),
			'add_new_item' => __( 'Add New Item Category', 'wolf' ),
			'new_item_name' => __( 'New Item Category', 'wolf' ),
			'separate_items_with_commas' => __( 'Separate Item categories with commas', 'wolf' ),
			'add_or_remove_items' => __( 'Add or remove Item categories', 'wolf' ),
			'choose_from_most_used' => __( 'Choose from the most used Item categories', 'wolf' ),
			'menu_name' => __( 'Categories', 'wolf' ),
		        );

		$args = array(

			'labels' => $labels,
			'hierarchical' => true,
			'public' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'type', 'with_front' => false),
		);

		register_taxonomy( 'item-type', array( 'item' ), $args );


		if ( ! class_exists( 'Wolf_Tour_Dates' ) ) {

		    $labels = array(
			'name' => __( 'Shows', 'wolf' ),
			'singular_name' => __( 'Show', 'wolf' ),
			'add_new' => __( 'Add New', 'wolf' ),
			'add_new_item' => __( 'Add New Show', 'wolf' ),
			'all_items'  =>  __( 'All Shows', 'wolf' ),
			'edit_item' => __( 'Edit Show', 'wolf' ),
			'new_item' => __( 'New Show', 'wolf' ),
			'view_item' => __( 'View Show', 'wolf' ),
			'search_items' => __( 'Search Shows', 'wolf' ),
			'not_found' => __( 'No Shows found', 'wolf' ),
			'not_found_in_trash' => __( 'No Shows found in Trash', 'wolf' ),
			'parent_item_colon' => '',
			'menu_name' => __( 'Tour Dates', 'wolf' ),
		        );

		$args = array(

			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => false,
			'rewrite' => array( 'slug' => 'show' ),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => 5,
			'taxonomies' => array(),
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'exclude_from_search' => false,

		    // 'description' => __( 'My Wolf Plugin', 'wolf' ),
		    // 'menu_icon' => $icon_url
		);
		register_post_type( 'show', $args );


		} // end class check


		if ( ! class_exists( 'Wolf_Discography' ) ) {

		    $labels = array(
			'name' => __( 'Releases', 'wolf' ),
			'singular_name' => __( 'Release', 'wolf' ),
			'add_new' => __( 'Add New', 'wolf' ),
			'add_new_item' => __( 'Add New Release', 'wolf' ),
			'all_items'  =>  __( 'All Releases', 'wolf' ),
			'edit_item' => __( 'Edit Release', 'wolf' ),
			'new_item' => __( 'New Release', 'wolf' ),
			'view_item' => __( 'View Release', 'wolf' ),
			'search_items' => __( 'Search Releases', 'wolf' ),
			'not_found' => __( 'No Releases found', 'wolf' ),
			'not_found_in_trash' => __( 'No Releases found in Trash', 'wolf' ),
			'parent_item_colon' => '',
			'menu_name' => __( 'Discography', 'wolf' ),
		        );

		$args = array(

			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => false,
			'rewrite' => array( 'slug' => 'release' ),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => 5,
			'taxonomies' => array(),
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'exclude_from_search' => false,

		);
		register_post_type( 'release', $args );

		} // end class check

		if ( ! class_exists( 'Wolf_Albums' ) ) {

		    $labels = array(
			'name' => __( 'Albums', 'wolf' ),
			'singular_name' => __( 'Album', 'wolf' ),
			'add_new' => __( 'Add New', 'wolf' ),
			'add_new_item' => __( 'Add New Album', 'wolf' ),
			'all_items'  =>  __( 'All Albums', 'wolf' ),
			'edit_item' => __( 'Edit Album', 'wolf' ),
			'new_item' => __( 'New Album', 'wolf' ),
			'view_item' => __( 'View Album', 'wolf' ),
			'search_items' => __( 'Search Albums', 'wolf' ),
			'not_found' => __( 'No Albums found', 'wolf' ),
			'not_found_in_trash' => __( 'No Albums found in Trash', 'wolf' ),
			'parent_item_colon' => '',
			'menu_name' => __( 'Albums', 'wolf' ),
		        );

		$args = array(

			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => false,
			'rewrite' => array( 'slug' => 'gallery' ),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => 5,
			'taxonomies' => array(),
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'exclude_from_search' => false,

		);
		register_post_type( 'gallery', $args );

		$labels = array(
			'name' => __( 'Gallery Categories', 'wolf' ),
			'singular_name' => __( 'Gallery Type', 'wolf' ),
			'search_items' => __( 'Search Gallery Categories', 'wolf' ),
			'popular_items' => __( 'Popular Gallery Categories', 'wolf' ),
			'all_items' => __( 'All Gallery Categories', 'wolf' ),
			'parent_item' => __( 'Parent Gallery Category', 'wolf' ),
			'parent_item_colon' => __( 'Parent Gallery Category:', 'wolf' ),
			'edit_item' => __( 'Edit Gallery Category', 'wolf' ),
			'update_item' => __( 'Update Gallery Category', 'wolf' ),
			'add_new_item' => __( 'Add New Gallery Category', 'wolf' ),
			'new_item_name' => __( 'New Gallery Category', 'wolf' ),
			'separate_items_with_commas' => __( 'Separate Gallery categories with commas', 'wolf' ),
			'add_or_remove_items' => __( 'Add or remove Gallery categories', 'wolf' ),
			'choose_from_most_used' => __( 'Choose from the most used Gallery categories', 'wolf' ),
			'menu_name' => __( 'Categories', 'wolf' ),
		        );

		$args = array(

			'labels' => $labels,
			'hierarchical' => true,
			'public' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'gallery-category', 'with_front' => false),
		);

		register_taxonomy( 'gallery-category', array( 'gallery' ), $args );

		} // end class check

		if ( ! class_exists( 'Wolf_Videos' ) ) {

		    $labels = array(
			'name' => __( 'Videos', 'wolf' ),
			'singular_name' => __( 'Video', 'wolf' ),
			'add_new' => __( 'Add New', 'wolf' ),
			'add_new_item' => __( 'Add New Video', 'wolf' ),
			'all_items'  =>  __( 'All Videos', 'wolf' ),
			'edit_item' => __( 'Edit Video', 'wolf' ),
			'new_item' => __( 'New Video', 'wolf' ),
			'view_item' => __( 'View Video', 'wolf' ),
			'search_items' => __( 'Search Videos', 'wolf' ),
			'not_found' => __( 'No Videos found', 'wolf' ),
			'not_found_in_trash' => __( 'No Videos found in Trash', 'wolf' ),
			'parent_item_colon' => '',
			'menu_name' => __( 'Videos', 'wolf' ),
		        );

		$args = array(

			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => false,
			'rewrite' => array( 'slug' => 'video' ),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => 5,
			'taxonomies' => array(),
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'exclude_from_search' => false,

		);
		register_post_type( 'video', $args );

		} // end class check





		if ( ! class_exists( 'Wolf_Discography' ) ) {

		    $labels = array(
			'name' => __( 'Releases', 'wolf' ),
			'singular_name' => __( 'Release', 'wolf' ),
			'add_new' => __( 'Add New', 'wolf' ),
			'add_new_item' => __( 'Add New Release', 'wolf' ),
			'all_items'  =>  __( 'All Releases', 'wolf' ),
			'edit_item' => __( 'Edit Release', 'wolf' ),
			'new_item' => __( 'New Release', 'wolf' ),
			'view_item' => __( 'View Release', 'wolf' ),
			'search_items' => __( 'Search Releases', 'wolf' ),
			'not_found' => __( 'No Releases found', 'wolf' ),
			'not_found_in_trash' => __( 'No Releases found in Trash', 'wolf' ),
			'parent_item_colon' => '',
			'menu_name' => __( 'Discography', 'wolf' ),
		        );

		$args = array(

			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => false,
			'rewrite' => array( 'slug' => 'release' ),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => 5,
			'taxonomies' => array(),
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'exclude_from_search' => false,

		);
		register_post_type( 'release', $args );

		} // end class check

	}

} // end post types function
add_action( 'init', 'wolf_deprecated_post_types' );