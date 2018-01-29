<?php
/*
 * Template Name: Page Jeu
 */

/*
//date_default_timezone_set('Europe/Paris');
//date_default_timezone_set('Europe/London');
$stime = strtotime("now");

$args = array(
	'posts_per_page' => 1,
	'post_type'=>'playlists',
	'meta_key' => 'game_start',
	'orderby' => 'game_start',
	'order' => 'ASC'
);
$query = new WP_Query( $args );
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		if ( (get_post_meta( $post->ID, 'game_start', 1 ) - $stime) < 0 ) {
			//уже игра идет,пройдите на главную
		} elseif ((get_post_meta( $post->ID, 'game_start', 1 ) - $stime) > 600) {
		 	//тоже на главную, ещё не началась
		} elseif ((get_post_meta( $post->ID, 'game_start', 1 ) - $stime) < 600) {
			
		} else {
	//игра начинается
		}
		//echo '<p> list time:' . date( "F j, Y, g:i a" , $time_stamp ) . '</p>';
		//echo '<p> current time: ' . date( "F j, Y, g:i a" , $stime)  . '</p>';
	}
} else {
	// Постов не найдено
}
wp_reset_postdata();
*/
get_header();
wolf_page_before();

?>
<script src="<?php echo get_template_directory_uri();?>/js/ajax-handler.js"></script>

<?php /* ?>
<audio  autoplay>
	<source src="<?php echo get_template_directory_uri(); ?>/media/mademoiselle_chante_le_blues.mp3" type="audio/mpeg">
	<source src="<?php echo get_template_directory_uri(); ?>/media/mademoiselle_chante_le_blues.ogg" type="audio/ogg">
	<source src="<?php echo get_template_directory_uri(); ?>/media/mademoiselle_chante_le_blues.wav" type="audio/wav">
</audio>
<?php */?>

<div id="primary-fullwidth" class="content-area">
	<div id="content" class="site-content" role="main">
		<div class="mini-container">
			<div class="row">
				<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
					<div class="wrapper-list">
						<div class="heading-list">
							<span class="heading">mon score</span>
							<span class="number">6.42</span>
							<p class="heading">Wait for: <span id="counter2" class="timer"></span></p>
						</div>
						<ul class="list-block-holder" id="list-artist-song">

						</ul>
					</div>
				</div>
				
				<div class="col-xs-12 col-sm-8 col-md-6 col-lg-6" id="jeuinner">
					
				</div>
				<?php
				/*
				if ($_SESSION['playid'] == $_POST['play_id']) : ?>
					
				<script>
					
				
				
				</script>
				
				<?php endif;
				*/
				?>
				
				<div class="col-xs-12 col-sm-8 col-md-3 col-lg-3">
					
					<p class="head">Tableau des scores général</p>
					
					<div class="count-block">
						<strong class="number">264</strong>
						<span>gold</span>
					</div>
					
					<div class="chois-players-box">
						<input id="chois-players" type="checkbox" name="chois-players">
						<label for="chois-players">cocher pour n'afficher que les joueurs avec mises</label>
					</div>
					
					<div class="wrapper-list wrapper-list-table">
						<table id="list-rating-game">
							<tr class="heading-table-list">
								<th style="min-width: 130px;">Nom</th>
								<th>Score</th>
								<th>Mise</th>
							</tr>
							<tr>
								<td>Martinbatterie</td>
								<td>5,17</td>
								<td>100</td>
							</tr>
							<tr>
								<td>Jean-jacques</td>
								<td>4,85</td>
								<td>100</td>
							</tr>
							<tr>
								<td>Manulemalin</td>
								<td>3,14</td>
								<td>50</td>
							</tr>
						</table>
					</div>
					
				</div>
			</div>
		</div>
			
		<?php /* The loop  ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail( 'extra-large' ); ?>
						</div>
					<?php endif; ?>
				</header>

				<div class="entry-content">
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-links"><p class="page-links-title">' . __( 'Pages:', 'wolf' ) . '</p>', 'after' => '</div>', 'link_before' => '<p>', 'link_after' => '</p>' ) ); ?>
				</div><!-- .entry-content -->

				<footer class="entry-meta">
					<?php edit_post_link( __( 'Edit', 'wolf' ), '<p class="edit-link">', '</p>' ); ?>
				</footer><!-- .entry-meta -->
			</article><!-- #post -->
			
			<?php  //comments_template(); ?>
		<?php endwhile; */ ?>

	</div><!-- #content -->
</div><!-- #primary -->

<?php
//get_sidebar();
wolf_page_after();
get_footer();
?>