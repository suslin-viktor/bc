<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: Custom Tabs Widget
	Plugin URI: http://themes.brutaldesign.com
	Description: A widget that displays 3 tabs: last post, popular post, and last commentt
	Version: 1.0
	Author: BrutalDesign
	Author URI: http://themes.brutaldesign.com

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/*  Create the widget
/*-----------------------------------------------------------------------------------*/
add_action('widgets_init', 'bd_custom_tabs_init');

function bd_custom_tabs_init(){

	register_widget('bd_custom_tabs_Widget');
	
}


/*-----------------------------------------------------------------------------------*/
/*  Widget Class
/*-----------------------------------------------------------------------------------*/
class bd_custom_tabs_Widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*  Widget Setup
	/*-----------------------------------------------------------------------------------*/
	function bd_custom_tabs_Widget(){

		// Widget settings
		$ops = array('classname' => 'widget_custom_tabs', 'description' => __('3 tabs: last posts, popular posts, and last comments', 'wolf'));

		/* Create the widget. */
		parent::__construct( 'widget_custom_tabs', __('Custom tabs', 'wolf'), $ops );
		
	}

	/*-----------------------------------------------------------------------------------*/
	/*  Display Widget
	/*-----------------------------------------------------------------------------------*/
	function widget($args, $instance){
		
		extract($args);
		echo $before_widget;
		bd_custom_tabs();
		echo $after_widget;
	
	}

}

/*-----------------------------------------------------------------------------------*/
/*  Custom tabs function
/*-----------------------------------------------------------------------------------*/
function bd_custom_tabs(){
	

	wp_enqueue_script( 'jquery-ui-tabs' );

	global $wpdb, $post;
	
	$args1 = array( 
		'post_type' => array('post'),
		'posts_per_page' => 3,
		'meta_key'    => '_thumbnail_id',
		'ignore_sticky_posts' => 1,
	);
	$args2 = array( 
		'post_type' => array('post') ,
		'orderby' => 'comment_count',
		'meta_key'    => '_thumbnail_id',
		'posts_per_page' => 3,
		'ignore_sticky_posts' => 1,
	);

	$loop1 = new WP_Query($args1);
	$loop2 = new WP_Query($args2);
	?>
	<script type="text/javascript"> jQuery(function($){ $( ".widget_custom_tabs .bd-tabgroup" ).tabs(); });</script>
	<div class="bd-tabgroup">
		<ul class="tabs-menu" style="margin-top:0">
			<li><a href="#tab-1"><?php _e('Recent', 'wolf'); ?></a></li>
			<li><a href="#tab-2"><?php _e('Popular', 'wolf'); ?></a></li>
			<li><a href="#tab-3"><?php _e('Comments', 'wolf'); ?></a></li>
		</ul>
		<div style="clear:both;"></div>

		<div class="widget-custom-tabs-container">
			<div id="tab-1">
				<div class="widget-thumbnails-list">
					<?php while ($loop1->have_posts()) : $loop1->the_post(); ?>
					<article>
						<a href="<?php esc_url(the_permalink()); ?>" class="widget-thumb-link">
							<?php the_post_thumbnail('mini', array('title' => "")); ?>
						</a>
						<span class="entry-title"><a href="<?php esc_url(the_permalink()); ?>" title="<?php printf(__('Permanent Link to %s', 'wolf'), get_the_title()); ?>"><?php echo wolf_sample(get_the_title(), 40); ?></a></span>
							<br>
						<span class="time"><?php echo get_the_date( get_option('date_format') ); ?></span><br>
						<span class="comment-count">
							<?php if ( comments_open() ) : ?>
								<?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a comment', 'wolf' ) . '</span>', __( 'One comment so far', 'wolf' ), __( 'View all % comments', 'wolf' ) ); ?>
							<?php endif; // comments_open() ?>
						</span>
		
						<div class="clear"></div>
					</article>
					<?php endwhile; ?>
				</div>

			</div>

			<div id="tab-2">
				<div class="widget-thumbnails-list">
				<?php while ($loop2->have_posts()) : $loop2->the_post(); ?>
				<article>
					<a href="<?php esc_url(the_permalink()); ?>" class="widget-thumb-link">
						<?php the_post_thumbnail('mini', array('title' => "")); ?>
					</a>
					<span class="entry-title"><a href="<?php esc_url(the_permalink()); ?>" title="<?php printf(__('Permanent Link to %s', 'wolf'), get_the_title()); ?>"><?php echo wolf_sample(get_the_title(), 40); ?></a></span>
						<br>
					<span class="time"><?php echo get_the_date( get_option('date_format') ); ?></span><br>
					<span class="comment-count"><?php bd_comment_number(); ?></span>
					<div class="clear"></div>
				</article>
				<?php endwhile; ?>
				</div>
			</div>
			<div id="tab-3">
			<?php 
			/**
			* bd_recent_comments() is used from the recent-comments-widget.php file
			*/
			if(function_exists('bd_recent_comments'))
				bd_recent_comments(3); 
			?>
			</div>
		</div>
	</div>
	<?php	
}

function bd_recent_comments($nbr_comments = 6, $comment_len = 60) {
    	global $wpdb;

    	$comments = get_comments( apply_filters( 'widget_comments_args', array( 'number' => $nbr_comments, 'status' => 'approve', 'post_status' => 'publish' ) ) );

	if ($comments) {
		echo "<div class=\"widget-thumbnails-list\">";
		foreach ($comments as $comment) {
			$comment_text = wolf_sample($comment->comment_content, $comment_len);
			$comment_img_title_attr = wolf_sample($comment->comment_content, 250);
			$comment_post_title = wolf_sample($comment->post_title, 35);
			ob_start();
			?>
			<article class="widget_reaction">
				<a href="<?php echo esc_url(get_permalink( $comment->comment_post_ID )) . '#comment-' . $comment->comment_ID; ?>" class="widget-thumb-link">
					<?php echo get_avatar($comment->comment_author_email); ?>
				</a>
				<a href="<?php echo esc_url(get_permalink( $comment->comment_post_ID )) . '#comment-' . $comment->comment_ID; ?>">
					<?php echo bd_get_author($comment); ?></a> <?php _e('said', 'wolf'); ?>
				<strong>
					<a title="<?php echo $comment_img_title_attr; ?>" href="<?php echo esc_url(get_permalink( $comment->comment_post_ID )) . '#comment-' . $comment->comment_ID; ?>">
						"<?php echo $comment_text; ?>"
					</a>
				</strong> <?php _e('on', 'wolf'); ?> <a href="<?php echo esc_url(get_permalink( $comment->comment_post_ID )) . '#comment-' . $comment->comment_ID; ?>" title="<?php _e('Read the comment', 'wolf'); ?>">
					<?php echo $comment_post_title; ?>
				</a>
				<div class="clear"></div>
			</article>
			<?php
			ob_end_flush();
		}
		echo "</div>";
	} else {
		echo __('No comment', 'wolf').'';
	}
}

/**
	* Get the author name
	**/
	function bd_get_author($comment) {
		$author ='';

		if ( empty($comment->comment_author) )
			$author = __('Anonymous', 'wolf');
		else
			$author = $comment->comment_author;

		return $author;
	}

function bd_comment_number($link = true)
{
	$num_comments = get_comments_number(); //  numeric item  comments
	if ( comments_open() ){
		if($num_comments == 0){
			$comments = __('No Response', 'wolf');
	}
	elseif($num_comments > 1){
		$comments = $num_comments.' '.__('Responses', 'wolf');
	}
	else{
		$comments ='1 '. __('Responses', 'wolf');
	}
	if($link==false)
		$write_comments = $comments;
	else
		$write_comments = '<a title="'.__('Leave a reply', 'wolf').'" href="'.get_comments_link().'">'. $comments.'</a>';

	}
	else{ $write_comments = ''; 
	//$write_comments =  ''.__('Responses are off', 'wolf'); 
	}
	echo $write_comments;
}
?>