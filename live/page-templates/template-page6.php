<?php
/*
 * Template Name: Page 6
 */
get_header();
wolf_page_before();
?>
	
		<div id="primary-fullwidth" class="content-area page6">
			<div id="content" class="site-content" role="main">
				<div class="mini-container">
					<div class="row">
						<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
							<div class="wrapper-list">
								<div class="heading-list">
									<span class="heading">Extraits joués dans la partie</span>
									<span class="number"></span>
								</div>
								<ul class="list-block-holder left-column">
									<li>Lady gaga - Applause<span class="result"></span></li>
									<li>System of a down - Toxicity<span class="result">2</span></li>
								</ul>
							</div>
							<button class="bnt-next-game">jouer la prochaine partie</button>
						</div>
						<div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
							<p class="time-box">Chrono : <span id="counter-next-part" class="timer-next-part"></span> secondes avant le début de la partie, fin des mises dans <span id="counter-bet" class="timer-bet"></span> secondes</p>
							<div id="first_fone" class="amount-strike btn-red">
								<span>Miser !</span>
							</div>
							<div class="score">
								<div class="score-item">
									<p class="heading">Challenge score</p>
									<p class="holder"><span class="number-of-part">0</span></p>
								</div>
								<div class="score-item">
									<p class="heading">Challenge artiste</p>
									<p class="holder"><span class="number-of-part">0</span></p>
								</div>
								<div class="score-item">
									<p class="heading">Challenge titre</p>
									<p class="holder"><span class="number-of-part">0</span></p>
								</div>
							</div>			
							<div class="row">
								<div class="col-md-6">
									<div class="count-block">
										<strong class="number">35</strong>
										<span>gold</span>
									</div>
									<div class="wrapper-list">
										<div class="heading-list heading-list-mini">
											<span class="heading">Score pour les artistes</span>
										</div>
										<ul class="list-block-holder mini-list-block-holder">
											<li>Martinbatterie<span class="result">3,14</span></li>
											<li>Manulemalin<span class="result">2,75</span></li>
										</ul>
									</div>
								</div>
								<div class="col-md-6">
									<div class="count-block">
										<strong class="number">134</strong>
										<span>gold</span>
									</div>
									<div class="wrapper-list">
										<div class="heading-list heading-list-mini">
											<span class="heading">Score pour les titres</span>
										</div>
										<ul class="list-block-holder mini-list-block-holder">
											<li>Jean-jacques</li>
										</ul>
									</div>
								</div>	
							</div>
						</div>
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
							<div class="wrapper-list wrapper-list-table right-column">
								<table>
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