<?php
if ( ! function_exists( 'wolf_do_metaboxes' ) ) :

function wolf_do_metaboxes() {

	$wolf_header_background_metabox = array(

		'background' => array(
				
				'title' => __( 'Custom Backgrounds', 'wolf' ),
				'page' => array( 'post', 'page', 'gallery', 'release', 'video', 'show', 'product' ),

				'metafields' => array(

					array(
						'label'	=> __( 'Header Background', 'wolf' ),
						'id'	=> '_header_bg',
						'type'	=> 'background',
					),


					array(
						'label'	=> __( 'Custom CSS (will be applied on this post only)', 'wolf' ),
						'id'	=> '_custom_css',
						'type'	=> 'textarea',
					),
					
				)

			),

	);

	/*-----------------------------------------------------------------------------------*/
	/*  Top & Bottom Area
	/*-----------------------------------------------------------------------------------*/

	$wolf_bottom_area_metabox = array(
		'Bottom Area' => array(

			'title' => __( 'Custom Content Areas', 'wolf' ),
			'page' => array( 'post', 'page', 'video', 'gallery', 'release', 'show', 'product' ),
			'metafields' => array(

				array(
					'label'	=> __( 'Top Area', 'wolf' ),
					'id'	=> '_top_holder',
					'desc'	=> __( 'Can contain HTML and shortcodes ', 'wolf' ),
					'type'	=> 'editor'
				),

				array(
					'label'	=> __( 'Bottom Area', 'wolf' ),
					'id'	=> '_bottom_holder',
					'desc'	=> __( 'Can contain HTML and shortcodes ', 'wolf' ),
					'type'	=> 'editor'
				),


			)
		)
	);

	new Wolf_Theme_Admin_Metabox( $wolf_bottom_area_metabox );
	new Wolf_Theme_Admin_Metabox( $wolf_header_background_metabox );

} // end function
	
wolf_do_metaboxes(); // do metaboxes

endif; // end function check