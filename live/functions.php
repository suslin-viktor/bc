<?php
/**
 * We define the parent theme template name in case a child theme is used 
 */
define( 'WOLF_THE_THEME', 'live' );

/** 
 * Sets up the content width value based on the theme's design.
 */
if ( ! isset( $content_width ) ) 
	$content_width = 745;

/**
 *  Require the core framework file to do the magic
 */
require_once get_template_directory() . '/wp-wolf-framework/wp-wolf-core.php';

/**
 * We use the Wolf_Theme class to set up the main theme structure in one single array (framework/wolf-core.php).
 * It is recommended to keep the variable name as "$wolf_theme".
 */
$wolf_theme = array(
		

	/* Menus (id => name) */
	'menus' => array(
		'primary' => 'Main Menu',
		'secondary' => 'Bottom Menu',
		),
	

	/**
	*  The thumbnails :
	*  We define wordpress thumbnail sizes that we will use in our design
	*/
	'images' => (array(
		
		/**
		*  parameters in the thumbnail array :
		*  int : max width
		*  int : max height
		*  boolean : ture/false -> hardcrop or not
		*/
		
		/*----------------------------------*
		* Default post feature image
		*/
		'default' => array(600, 800, false),
		
		/*----------------------------------*
		* Post format image
		*/
		'large' => array(750, 1000, false),

		/*----------------------------------*
		* Widget thumbnail
		*/
		'mini' => array( 80, 80, true), 

		/*----------------------------------*
		* Photo widget thumbnail
		*/
		'photo-widget-thumb' => array( 180, 180, true), 
		'photo-widget-slide' => array( 360, 360, true), 

		/*----------------------------------*
		* Album cover, video thumbnail
		*/
		'item-cover' => array(410, 280, true),

		/*----------------------------------*
		* Store thumb, release thumb
		*/
		'store-thumb' => array(410, 410, true),


		/*----------------------------------*
		* Photo thumbnail
		*/
		'photo' => array(390, 700, false),

		/*----------------------------------*
		* RSS image
		*/
		'archive-thumb' => array( 570, 400, false) 
	) ),

	/* Include helpers from the includes/helpers folder */
	'helpers' => array(
		'video-thumbnails',
		'google-fonts'
	),

	'woocommerce' => true


);
$wolf_do_theme = new Wolf_Framework( $wolf_theme );

/* Includes features */
wolf_includes_file( 'features/wolf-flexslider/wolf-flexslider.php' );
wolf_includes_file( 'features/wolf-refineslide/wolf-refineslide.php' );
wolf_includes_file( 'features/wolf-share/wolf-share.php' );
wolf_includes_file( 'widgets/custom-tabs-widget.php' );

if ( get_option( '_live_updated' ) && ! get_option( '_w_to_woocommerce' ) ) {

	wolf_includes_file( 'old-version/old-version.php' );
	
}

// Recommend plugins with TGM plugins activation
include( 'includes/admin/plugins/plugins.php' );

//добавляем файлы сравнения строк
require_once( 'includes/double_metaphone_class_1-01.php' );
require_once( 'includes/double_metaphone_func_1-02-alt.php' );

function v($var) {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}

function true_loadmore_scripts() {
	wp_enqueue_script('jquery'); // скорее всего он уже будет подключен, это на всякий случай
 	wp_enqueue_script( 'true_loadmore', get_stylesheet_directory_uri() . '/js/loadmore.js', array('jquery') );
}
add_action( 'wp_enqueue_scripts', 'true_loadmore_scripts' );


function true_load_posts() {
	$args = unserialize(stripslashes($_POST['query']));
	//$args['paged'] = $_POST['page'] + 1; // следующая страница
	$args['post_status'] = 'publish';
	$args['post_type'] = 'page';
	$args['page_id'] = 630;
	$q = new WP_Query($args);
	if( $q->have_posts() ):
		while($q->have_posts()): $q->the_post();
			?>
			<div id="post-<?php echo $q->post->ID ?>" class="post-<?php echo $q->post->ID ?> hentry">
				<h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark"><?php echo $q->post->post_title ?></a></h2>
				<div class="entry-meta">
					<span class="meta-prep meta-prep-author">Опубликовано</span> <span class="entry-date"><?php the_time('j M Y') ?></span></a>
					<span class="meta-sep">автором</span>
					<span class="author vcard"><?php the_author_link(); ?> </span>
				</div>
				<div class="entry-content"><p style="text-align: center;"><?php the_content() ?></p></div>
				<div class="entry-utility">
					<span class="cat-links">
					<span class="entry-utility-prep entry-utility-prep-cat-links">Рубрика:</span> <?php the_category(', '); ?></span>
					<span class="meta-sep">|</span>
					<span class="comments-link"><a href="<?php the_permalink() ?>#comments">Комментарии (<?php echo $q->post->comment_count ?>)</a></span>
				</div>
			</div>
			<?php
		endwhile;
	endif;
	wp_reset_postdata();
	die();
} 

add_action('wp_ajax_loadmore', 'true_load_posts');
add_action('wp_ajax_nopriv_loadmore', 'true_load_posts');

function appthemes_check_user_role( $role, $user_id = null ) {
	if ( is_numeric( $user_id ) ) {
		$user = get_userdata( $user_id );
	} else {
		$user = wp_get_current_user();
	}
	if ( empty( $user ) ) {
		return false;
	}
	return in_array( $role, (array) $user->roles );
}