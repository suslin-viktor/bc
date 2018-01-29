<?php
/**
* Register default widget areas
*/
function bd_sidebars_init() {
	global $BD_theme;

	// Blog Sidebar
	register_sidebar( array(
		'name'          => 'Blog Sidebar',
		'id'            => 'main',
		'description' => __('The default sidebar used for the blog.', 'wolf'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="widget-content">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );


	// Footer
	register_sidebar( array(
		'name'          => 'Footer',
		'id'            => 'footer_area',
		'description' => __('Footer widget area (supports 3 widgets)', 'wolf'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	if ( class_exists( 'Woocommerce' ) ) {
		register_sidebar( array(
			'name'          => __( 'Store Sidebar', 'wolf' ),
			'id'            => 'woocommerce',
			'description'   => __( 'Appears in Woocommerce pages', 'wolf' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="widget-content">',
			'after_widget'  => '</div></aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	} else {
		register_sidebar( array(
			'name'          => __( 'Store Sidebar', 'wolf' ),
			'id'            => 'item',
			'description'   => __( 'Appears in Store pages', 'wolf' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="widget-content">',
			'after_widget'  => '</div></aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}


}
add_action( 'widgets_init', 'bd_sidebars_init' );