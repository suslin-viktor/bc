<?php
global $current_user;
get_currentuserinfo();
$home_content = '<h2>How to start</h2>';
$home_content .= '<ul>';
$home_content .= '<li>You can start by <a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">creating your menus</a> in the admin panel.';
$home_content .= '</li>';

$home_content .= '<li>Upload your logo, backgrounds, choose your colors and layout</li>';
$home_content .= '<li>Set your home page header in the theme <a href="' . esc_url( admin_url( 'admin.php?page=wolf-framework' ) ) . '">custom options</a> panel</li>';
$home_content .= '<li>Edit this page!</li>';
$home_content .= '<li>If you need more details, check the <strong>Documentation</strong> included in the theme package (“Documentation” folder)</li>';
$home_content .= '</ul>';


$home_content .= '<hr>';
$home_content .= '<h2>Tips</h2>';
$home_content .= '<h3>Main  Menu</h3>';
$home_content .= '<p>To add a home icon to your menu, add the "home-menu-item" class to your "Home" menu item</p>';
$home_content .= '<p><img src="' . WOLF_THEME_URL . '/includes/installation/img/home-menu-item.jpg' . '" alt="home-menu-item"></p>';


if ( class_exists( 'Wolf_Twitter' ) ) :
$home_content .= '<hr>';
$home_content .= '<h2>Last Tweet</h2>';
$home_content .= '<p>To display your last tweet use the following shortcode</p>';
$home_content .= '<code>[[wolf_tweet username="your_twitter_id"]]</code>';
endif;

if ( class_exists( 'Wolf_Albums' ) ) :
$home_content .= '<hr>';
$home_content .= '<h2>Last Albums</h2>';
$home_content .= '<p>To display your last albums use the following shortcode</p>';
$home_content .= '<code>[[wolf_last_albums]]</code>';
endif;

if ( class_exists( 'Wolf_Videos' ) ) :
$home_content .= '<hr>';
$home_content .= '<h2>Last Videos</h2>';
$home_content .= '<p>To display your last videos use the following shortcode</p>';
$home_content .= '<code>[[wolf_last_videos]]</code>';
endif;


if ( class_exists( 'Wolf_Shortcodes' ) ) :
$home_content .= '<hr>';
$home_content .= '<h2>Shortcodes</h2>';
$home_content .= '<p>You will find the shortcode generator button in your text editor toolbar</p>';
$home_content .= '<p><img src="' . WOLF_THEME_URL . '/includes/installation/img/shortcodes-button.jpg' . '" alt="shortcodes"></p>';
endif;

if ( class_exists( 'Wolf_Sidebars' ) ) :
$home_content .= '<hr>';
$home_content .= '<h3>Widget Area</h3>';
$home_content .= '<p>To create a Widget Area, go in the Wolf Sidebars panel and create a sidebar using the "widget area" type.</p>';
$home_content .= '<p><img src="' . WOLF_THEME_URL . '/includes/installation/img/widget-area.jpg' . '" alt="widget-area"></p>';
$home_content .= '<p>You will be able to add your widget area in your page by using the shortcode generator button in any text editor.</p>';
$home_content .= '<p><img src="' . WOLF_THEME_URL . '/includes/installation/img/widget-area-button.jpg' . '" alt="widget-area-button"></p>';
endif;

/*----------------------*/

$top_holder = '<h2 style="text-align:center;">Welcome ' . $current_user->user_login . '!</h2>';
$top_holder .= '<p style="text-align:center;">';
$top_holder .= 'This your top area.
You can display any content in here. You can set a default content in the <a href="' . esc_url( admin_url( 'admin.php?page=wolf-framework' ) ) . '">Custom options</a> (Custom Content tab) that will be displayed in all your pages and/or 
set a different content for each page by using the "Top Area" box below your text editor)';
$top_holder .= '</p>';

/*----------------------*/

$bottom_holder = '';

if ( class_exists( 'Wolf_Albums' ) ) {
	$bottom_holder .= '<h2 style="text-align:center;">Last Photo Sets</h2>';
	$bottom_holder .= '[wolf_last_albums]';
}

if ( class_exists( 'Wolf_Albums' ) && class_exists( 'Wolf_Videos' ) )
	$bottom_holder .= '<hr>';

if ( class_exists( 'Wolf_Videos' ) ) {
	$bottom_holder .= '<h2 style="text-align:center;">Last Videos</h2>';
	$bottom_holder .= '[wolf_last_videos]';
}


$default_pages = array(

	/* Pages */
	'Home' => array(
		'title' => 'Home',
		'post_type' => 'page',
		'content' => $home_content,
		'template' => '',
		'meta' => array(
			'_wolf_page_headline' => $top_holder
		)
		
	),

	'Blog' => array(
		'title' => 'Blog',
		'post_type' => 'page',
		'content' => '',
		'template' => '',
		
	),

);

if ( class_exists( 'Wolf_Albums' ) || class_exists( 'Wolf_Videos' ) )
	$default_pages['Home']['meta']['_wolf_page_bottomline'] = $bottom_holder;

if ( class_exists( 'Wolf_Discography' ) ) {
	
	$default_pages['Discography'] = array(
		'title' => 'Discography',
		'post_type' => 'page',
		'content' => '',
		'template' => 'discography-template.php',
	);
}

if ( class_exists( 'Wolf_Videos' ) ) {
	
	$default_pages['Videos'] = array(
		'title' => 'Videos',
		'post_type' => 'page',
		'content' => '',
		'template' => 'videos-template.php',
	);
}

if ( class_exists( 'Wolf_Albums' ) ) {
	
	$default_pages['Albums'] = array(
		'title' => 'Albums',
		'post_type' => 'page',
		'content' => '',
		'template' => 'albums-template.php',
	);
}

if ( class_exists( 'Wolf_Tour_Dates' ) ) {
	
	$default_pages['Tour Dates'] = array(
		'title' => 'Tour Dates',
		'post_type' => 'page',
		'content' => '[wolf_tour_dates]',
		'template' => '',
	);
}

if ( defined( 'WPCF7_PLUGIN_DIR' ) ) {
	
	$default_pages['Contact'] = array(
		'title' => 'Contact',
		'post_type' => 'page',
		'content' => '[contact-form-7 id="4" title="Contact form 1"]',
		'template' => '',
	);
}

new Wolf_Theme_Admin_Default_Pages( $default_pages );
