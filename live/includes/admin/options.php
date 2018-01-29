<?php
/**
 * Live! theme options
 *
 * @package WordPress
 * @subpackage Live!
 * @since Live! 2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wolf_theme_options;

$wolf_fonts = array(
		"'CamboRegular'" => "Cambo",
		"'CrimsonItalic'" => "Crimson (Italic)",
		"'ProclamateLight'" => "Proclamate light",
		"'LeanderRegular'" => "Leander",
		"'LeagueGothicRegular'" => "League Gothic",
		"'MisoRegular'" => "Miso",
		"'BoycottRegular'" => "Boycott (uppercase)",
		"'UbuntuCondensedRegular'" => "Ubuntu Condensed", 
		"'GermaniaOneRegular'" => "Germania One",
		"'BebasRegular'" => "Bebas (uppercase)",
		"'BlackoutMidnight'" => "Blackout Midnight",
		"Lobster13Regular" => "Lobster",
		"'OstrichSansMedium'" => "Ostrich (uppercase)",
		"'AftaserifRegular'" => "Aftaserif",
		"'AurulentSansRegular'" => "Aurulent",
		"'DroidSerifRegular'" => "Droid Serif",
		"'HillHouseMedium'" => "Hill House (uppercase)",
		"'NobileRegular'" => "Nobile",
		"'Helvetica Neue', Helvetica, Arial, sans-serif" => "Helvetica",
);		

/*-----------------------------------------------------------------------------------*/

// Available types : text, textarea, checkbox, select, background, colopicker, font-body, font-title

$wolf_theme_options = array(
 
array( "name" => __('Options', 'wolf'),
	"type" => "title"),

/*-----------------------------------------------------------------------------------*/
/*  General
/*-----------------------------------------------------------------------------------*/

array( 
	"type" => "open",
	"name" => __('General', 'wolf'),
),

	array( "name" => __('Main Settings', 'wolf'),
		"type" => "section_open"),

	array( "name" => __('Logo', 'wolf'),
		"id" => "logo",
		"type" => "image",
		),

	array( "name" => __('Skin', 'wolf'),
		"id" => "skin",
		"type" => "select",
		"options" => array(
			"dark" => __('dark', 'wolf'),
			"light" => __('light', 'wolf'),
			"grunge" => __('grunge', 'wolf'),
		),
	),

	array( "name" => __('Accent Font Color', 'wolf'),
		"desc" => __('Font color for links, accent color etc...', 'wolf'),
		"id" => "custom_color",
		"type" => "colorpicker",
	),

	array( "name" => __('Lightbox', 'wolf'),
		"id" => "lightbox",
		"type" => "select",
		"options" => array(
			"fancybox" , "swipebox", "none"
		)
	),

	array( "name" => __('Layout', 'wolf'),
		"type" => "section_open"),

		array( "name" => __('Page Container', 'wolf'),
			"id" => "page_container",
			"type" => "select",
			"options" => array(
				"fullwidth" => __('fullwidth', 'wolf'),
				"wrapped" => __('wrapped', 'wolf'),
			),
		),

		array( "name" => __('Sidebar Position', 'wolf'),
			"id" => "sidebar_position",
			"type" => "select",
			"options" => array(
				"right" => __('right', 'wolf'),
				"left" => __('left', 'wolf'),
				),
		),

		
		array( "name" => __('Display page title', 'wolf'),
			"desc" => __('Would you like to display the page title on every page?', 'wolf'),
			"id" => "show_title",
			"type" => "checkbox",
		),


		array( "name" => __('Sticky main menu', 'wolf'),
			"id" => "sticky_menu",
			"desc" => __('The admin bar on the front end will be hidden', 'wolf'),
			"type" => "checkbox",
			),

	array(  "type" => "section_close"),
	
	array( "name" => __('Footer', 'wolf'),
		"type" => "section_open"),

		array( "name" => __('Footer copyright text', 'wolf'),
			"desc" => __('Enter the text used in the right side of the footer. It can contain HTML', 'wolf'),
			"id" => "footer_text",
			"type" => "text",
		),


	array(  "type" => "section_close"),

array( "type" => "close"),


/*-----------------------------------------------------------------------------------*/
/*  Custom Content
/*-----------------------------------------------------------------------------------*/

array( "type" => "open",
	"name" => __('Custom Content', 'wolf') ),

	array( 'name' => __('Additional Page Content Areas', 'wolf'),
		'type' => 'section_open',
		'desc' => __( 'Two additional areas to display any content above and below your main page content.', 'wolf' ),

		),

		array( 'name' => __( 'Default Top Content Area', 'wolf' ),
			'desc' => __( 'Any content to display at the top of your pages after the header (can be overwritten in post/page options, in the "Top Area" box below your text editor).', 'wolf' ),
			'id' => 'headline',
			'type' => 'editor'
		),

		array( 'name' => __( 'Default Bottom Content Area', 'wolf' ),
			'desc' => __( 'Any content to display at the bottom of your pages before the footer (can be overwritten in post/page options, in the "Bottom Area" box below your text editor).', 'wolf' ),
			'id' => 'bottomline',
			'type' => 'editor'
		),
	array(  "type" => "section_close"),


array( "type" => "close"),

/*-----------------------------------------------------------------------------------*/
/*  Styles
/*-----------------------------------------------------------------------------------*/

array( "type" => "open", "name" => __('Backgrounds', 'wolf') ),

	array( "name" => __('Backgrounds', 'wolf'),
		"type" => "section_open"),

		array( "name" => __('Body Background', 'wolf'),
			"id" =>"body_bg",
			"type" => "background",
			),

		array( "name" => __('Wrapper', 'wolf'),
			"desc" => __('If you have set the page container to "wrap" in the general tab, set the background of the page container here', 'wolf'),
			"id" =>"wrapper",
			"type" => "background",
			),


		array( "name" => __('Header Background', 'wolf'),
			"id" =>"header",
			"type" => "background",
			),

		array( "name" => __('Top Holder Background', 'wolf'),
			"id" =>"headline",
			"type" => "background",
			),

		array( "name" => __('Bottom Holder Background', 'wolf'),
			"id" =>"bottomline",
			"type" => "background",
			),


		array( "name" => __('Footer Widget Area', 'wolf'),
			"id" =>"footer",
			"type" => "background",
			// 'parallax' => true
			),

		array( "name" => __('Footer Social Logos Background', 'wolf'),
			"id" =>"footer_socials",
			"desc" => __('This area is visible only if you have installed the <a href="http://wolfthemes.com/plugin/wolf-music-network" target="_blank">WolfMusicNetwork plugin</a>.', 'wolf'),
			"type" => "background",
			// 'parallax' => true
			),
	array(  "type" => "section_close"),
	

array( "type" => "close"),


/*-----------------------------------------------------------------------------------*/
/*  Fonts
/*-----------------------------------------------------------------------------------*/

array( "type" => "open", "name" => __(' Custom Fonts', 'wolf')),

	array( "name" => __('Body', 'wolf'),
		"type" => "section_open"),

		array( "name" => __('Body font', 'wolf'),
			"desc" => __('Choose your font for the content', 'wolf'),
			"id" => "body_font",
			"type" => "font-body",
			"options" => array(
				"'CamboRegular'" => "Cambo",
				"'Times New Roman', serif" => "Times New Roman",
				"'Helvetica Neue', Helvetica, Arial, sans-serif" => "Helvetica", 
				"Verdana, Arial, Helvetica, sans-serif" => "Verdana", 
				"Tahoma, serifSansSerifMonospace" => "Tahoma", 
				"'Trebuchet MS', sans-serif" => "Trebuchet",
				"monaco, sans-serif" => "Monaco",
				"Geneva, 'MS Sans Serif', sans-serif" => "Geneva", 
				"Georgia, serif" => "Georgia", 
				"'Courier New', Courier, monospace" => "Courier New",
				"Arial, Helvetica, sans-serif" => "Arial", 
				),
			"def" => "'Helvetica Neue', Helvetica, Arial, sans-serif"),

	array(  "type" => "section_close"),

	array( "name" => __('Headings', 'wolf'),
		"type" => "section_open"),

		array( "name" => __('Title font', 'wolf'),
			"desc" => __('Choose your font for the headings', 'wolf'),
			"id" => "title_font",
			"type" => "font-title",
			"options" => $wolf_fonts 
		),

	array(  "type" => "section_close"),

	array( "name" => __('Main Menu', 'wolf'),
		"type" => "section_open"),


		array( "name" => __('Menu font', 'wolf'),
			"desc" => __('Choose your font for the main menu', 'wolf'),
			"id" => "menu_font",
			"type" => "font-title",
			"options" => $wolf_fonts		
		),

	array(  "type" => "section_close"),


array( "type" => "close"),

/*-----------------------------------------------------------------------------------*/
/*  Google fonts
/*-----------------------------------------------------------------------------------*/
array(
	 'type' => 'open', 
	'name' => __( 'Google Fonts', 'wolf' ), 
	'desc' => __( 'Use this panel to import fonts from the Google fonts API. If you enter a font code, it will overwrite the Custom Font settings', 'wolf' ),
),

	array( 'name' => __('Headings', 'wolf'),
		'type' => 'section_open',
		'desc' =>  __('Leave the fields below empty to use the default font', 'wolf')
	),

		array( 'name' => __('Titles google font code', 'wolf'),
			'id' => 'heading_google_font_code',
			'desc' => __('eg: "Lora:400,700". 400 and 700 are the available font weight', 'wolf'),
			'help' => 'google-fonts',
			'type' => 'text',
		),

		array( 'name' => __('Titles font name', 'wolf'),
			'id' => 'heading_google_font_name',
			'desc' => __('eg: Lora', 'wolf'),
			'type' => 'text',
		),

		array( 'name' => __('Titles font weight', 'wolf'),
			'id' => 'heading_google_font_weight',
			'desc' => __( 'For example: 400 is normal, 700 is bold.<br> The available font weight depends on your font.', 'wolf' ),
			'type' => 'int',
			'def' => 400,
		),

		array( 'name' => __('Titles text transform', 'wolf'),
			'id' => 'heading_google_font_transform',
			'type' => 'select',
			'options' => array(
				'standard' => __( 'Standard', 'wolf' ),
				'uppercase' => __( 'Uppercase', 'wolf' ),
			)

		),

	array(  "type" => "section_close"),

	array( 'name' => __('Menu', 'wolf'),
		'type' => 'section_open',
		'desc' =>  __('Leave the fields below empty to use the default font', 'wolf')
	),

		array( 'name' => __('Menu google font code', 'wolf'),
			'id' => 'menu_google_font_code',
			'desc' => __('eg: "Lora:400,700"', 'wolf'),
			'type' => 'text',
		),

		array( 'name' => __('Menu font name', 'wolf'),
			'id' => 'menu_google_font_name',
			'desc' => __('eg: Lora', 'wolf'),
			'type' => 'text',
		),

		array( 'name' => __('Menu font weight', 'wolf'),
			'id' => 'menu_google_font_weight',
			'type' => 'int',
			'def' => 400,
		),

		array( 'name' => __('Menu text transform', 'wolf'),
			'id' => 'menu_font_transform',
			'type' => 'select',
			'options' => array(
				'standard' => __( 'Standard', 'wolf' ),
				'uppercase' => __( 'Uppercase', 'wolf' ),
			)

		),

	array(  "type" => "section_close"),

	array( 'name' => __('Body', 'wolf'),
		'type' => 'section_open',
	),

		array( 'name' => __('Body google font code', 'wolf'),
			'id' => 'body_google_font_code',
			'desc' => __('eg: "Lora:400,700"', 'wolf'),
			'type' => 'text',
		),

		array( 'name' => __('Body font name', 'wolf'),
			'id' => 'body_google_font_name',
			'desc' => __('eg: Lora', 'wolf'),
			'type' => 'text',
		),

	array(  "type" => "section_close"),


array( 'type' => 'close'),


/*-----------------------------------------------------------------------------------*/
/*  Homepage
/*-----------------------------------------------------------------------------------*/

array( "type" => "open", "name" => __('Homepage', 'wolf')),

	array( 'name' => __('Home Page Header', 'wolf'),
		'type' => 'section_open'
	),

		array( "name" => __('Header Background Parallax', 'wolf'),
			"id" =>"header_parallax",
			"type" => "checkbox",
			),

		array( "name" => __('Home Header Type', 'wolf'),
			"desc" => __('Select what you want to display on your homepage header', 'wolf'),
			"id" => "header",
			"type" => "select",
			"options" => array(
				"embed" => __( 'Standard', 'wolf' ),
				"slider" => "Slider", 
				"static" => __('Static Header', 'wolf'), 
				),
			"def" => "slider"),

		array( "name" => __('Header Header Content', 'wolf'),
			"desc" => __('Any content to display in the home page header: text, HTML, shortcodes etc...', 'wolf'),
			"id" => "embed_header",
			"type" => "editor",
			),	

		array( "name" => __('Static Header Image', 'wolf'),
			"desc" => __('If you choose "Static Header" as header type, upload your image here', 'wolf'),
			"id" => "static_header",
			"type" => "file",

			),

		array( "name" => __('Static Header Padding', 'wolf'),
			"desc" => __('Space above and below the static image', 'wolf'),
			"id" => "static_header_padding",
			"type" => "int",
			"app" => "px",
			),

	array(  "type" => "section_close"),

array( "type" => "close"),


/*-----------------------------------------------------------------------------------*/
/*  Analytics
/*-----------------------------------------------------------------------------------*/

array( 'type' => 'open', 'name' => __( 'Tracking Code', 'wolf' ) ),
	
	array( 'name' => __( 'Tracking Code', 'wolf' ),
		'type' => 'section_open' ),

	
		array( 'name' => __( 'Tracking Code', 'wolf' ),
			'desc' => __( 'You can paste your <strong>Google Analytics</strong> or other tracking code in this box. 
				<br>Note that your tracking code will not be output when you\'re logged in to not count your own page views.', 'wolf' ),
			'id' => 'tracking_code',
			'type' => 'javascript',
		),

	array( 'type' => 'section_close' ),

		
array( 'type' => 'close' ),

/*-----------------------------------------------------------------------------------*/
/*  CSS
/*-----------------------------------------------------------------------------------*/

array( 'type' => 'open', 'name' => 'CSS' ),

	array( 'name' => 'CSS',
		'type' => 'section_open',
	),

		'css' => array( 
			'name' => __( 'Custom CSS', 'wolf' ),
			'desc' => __( 'Want to add any custom CSS code? Put in here, and the rest is taken care of.', 'wolf' ),
			'id' => 'custom_css',
			'type' => 'textarea',
		),

	array( 'type' => 'section_close' ),


array( 'type' => 'close' ),


/*-----------------------------------------------------------------------------------*/
/*  Misc
/*-----------------------------------------------------------------------------------*/

array( 'type' => 'open', 'name' => __( 'Misc', 'wolf'  ) ),
	
	array( 
		'name' => __( 'Misc', 'wolf' ),
		'type' => 'section_open',
	),

		array(

			'id' => 'login_logo',
			'name' => __( 'Admin Login Logo', 'wolf' ),
			'desc' => __( 'It will replace the Wordpress logo on the admin login page. ( It must be 80px X 80px )', 'wolf' ),
			'type' => 'image',

		),

		array(

			'id' => 'custom_avatar',
			'name' => __( 'Custom Default Avatar (80px X 80px)', 'wolf' ),
			'desc' => sprintf( __( 'Once uploaded and saved, you will have to set your new avatar in the <a href="%s">discussion settings</a> (at the bottom of the page)', 'wolf' ), esc_url( admin_url( 'options-discussion.php' ) ) ),
			'type' => 'image',
			'help' => 'custom-avatar'

		),



		array( "name" => __('404 page text', 'wolf'),
			"id" => "404",
			"type" => "text",
			"desc" => __('Text to display on 404 error page', 'wolf'),
			"def" => __('You\'ve tried to reach a page that doesn\'t exist or has moved.', 'wolf')
		),

	
	array( 'type' => 'section_close' ),
		
array( 'type' => 'close'),


); // end $theme_options

/*-----------------------------------------------------------------------------------*/
/*  Plugin Settings
/*-----------------------------------------------------------------------------------*/

/* Display this section only if Wolf Videos is installed */
if (  class_exists( 'Wolf_Videos' ) ) {

	$wolf_theme_options[] = array( 'type' => 'open' ,  'name' => __( 'Plugin', 'wolf'  ) );

		$wolf_theme_options[] = array( 'name' => __( 'Plugin Settings', 'wolf' ),
					'type' => 'section_open',
		);

		if ( class_exists( 'Wolf_Videos' ) ) {
			
			$wolf_theme_options[] =array( 'name' => __( 'Open Videos in a lightbox', 'wolf' ),
					'id' => 'video_lightbox',
					'type' => 'checkbox',
			);
		}

		$wolf_theme_options[] = array( 'type' => 'section_close' );

	$wolf_theme_options[] = array( 'type' => 'close' );
}


/* end options */

if ( ! is_child_theme() ) {
	
	$child_theme_message = sprintf(
		__( 'Want to add any custom CSS code? Put in here, and the rest is taken care of. If you need more CSS customization, you should create a <a href="%s" target="_blank">Child Theme</a>', 'wolf' ),
		'http://codex.wordpress.org/Child_Themes'
	);

	$wolf_theme_options['css']['desc'] = $child_theme_message;
}

if ( class_exists( 'Wolf_Theme_Options' ) ){
	new Wolf_Theme_Options( $wolf_theme_options );
}