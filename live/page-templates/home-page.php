<?php
/*
 * Template Name: Home Page
 */
get_header();
wolf_page_before();
?>

<div id="primary-fullwidth" class="content-area">
	<div class="row">
		<div class=" wrap-home-block">
			<div class="col-md-12">
				<div class="home-block home-block1">
					<!--
					<a href="<?php echo get_the_permalink(478); ?>" class="table-elem">
					-->
					<?php
					$play_id = 456;
					$_SESSION['playid'] = $play_id;
					?>
					
					<form id="formstart" method="POST" action="javascript:void(0);">
						<input type="hidden" name="play_id" value="<?php echo $play_id ?>"/>
						<button class="table-elem" id="linkstart" onclick="document.form.submit(); return false;"> <span class="cell-elem"> <strong class="ico"></strong>
							<p class="holder-timer">
								Début de la prochaine partie dans <span id="counter" class="timer"></span>
							</p></span></button>
							
							
					</form>
					
					<script>
					/*
						function getCurCountdown() {
							var curcountdown = document.getElementById('counter').innerHTML;
							
						}
						setTimeout(function run() {
  							getCurCountdown();
  						setTimeout(run, 1000);
						}, 1000);
						
						
						
						(function($) {
							jQuery(document).ready(function() {

								function checktime() {
									if (timeD > 600) {
										$("#linkstart").attr("href", "javascript:void(0);");
										//alert(timeD  + 'true');
									} else {
										//alert(timeD  + 'false');
										$("#linkstart").attr("href", "/jeu");
									}
								}

								setInterval(checktime, 1000);

							});
						})(jQuery);
						/*
						 window.setInterval(function(){
						 checktime();
						 }, 2000)
						 */
					</script>
					<input type="hidden" value="<?php echo get_the_permalink(478); ?>" />
				</div>
				<div class="home-block home-block2">
					<a href="<?php echo wp_registration_url(); ?>" class="table-elem"> <span class="cell-elem"> <strong class="ico"></strong>
						<p>
							Creer un compte/ Se conecter pour jouer la prochaine partie !
						</p> </span> </a>
				</div>
				<div class="home-block home-block2 home-block2-regist">
					<a href="#" class="table-elem"> <span class="cell-elem"> <strong class="ico"></strong>
						<p>
							Démarrer le jeu
						</p> </span> </a>
				</div>
				<div class="home-block home-block3">
					
					
					
					<a href="#" class="table-elem"> <span class="cell-elem"> <strong class="ico"></strong>
						<p>
							Rejoindre la partie en mode spéctateur
						</p> </span> </a>
				</div>
			</div>
		</div>
	</div>

	<div id="content" class="site-content" role="main">
		<?php /* The loop */ ?>
		<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( has_post_thumbnail() && ! post_password_required() ) :
			?>
			<div class="entry-thumbnail">
				<?php the_post_thumbnail('extra-large'); ?>
			</div>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
				
				
				
				<?php //wp_link_pages(array('before' => '<div class="page-links"><span class="page-links-title">' . __('Pages:', 'wolf') . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>')); ?>
			</div><!-- .entry-content -->

			<footer class="entry-meta">
				<?php edit_post_link(__('Edit', 'wolf'), '<span class="edit-link">', '</span>'); ?>
			</footer><!-- .entry-meta -->
		</article><!-- #post -->

		<?php // comments_template(); ?>
		<?php endwhile; ?>
		
	</div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>