<?php
/* Old Features */
wolf_includes_file( 'old-version/bd-paypal/bd-paypal.php' );
wolf_includes_file( 'old-version/last-store-item-widget.php' );

function old_store() {
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
}
add_action( 'init', 'old_store' );
