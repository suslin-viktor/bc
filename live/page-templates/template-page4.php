<?php
/*
 * Template Name: Page 4
 */
get_header();
wolf_page_before();
?>
	
		<div id="primary-fullwidth" class="content-area page4">
			<div id="content" class="site-content" role="main">
				
				<div class="mini-container">
					<div class="row">
						<div class="col-md-12">
							<ul class="list-link">
								<li><a href="#" class="active">Mon profil</a></li>
								<li><a href="http://breizh-concept.flntest.com/page-5/">Mes succès/ quête</a></li>
							</ul>
						</div>
						<div class="col-md-4">
							<div class="wrapper-item">
								<div class="holder-item">
									<div class="btn-red">
										<p class="table-elem">
											<span class="cell-elem">Mon Or</span>
										</p>
									</div>
									<div class="bg-grey">
										<p class="table-elem">
											<span class="cell-elem">376</span>
										</p>
									</div>
								</div>
								<div class="holder-item">
									<div class="btn-red">
										<p class="table-elem">
											<span class="cell-elem">Statut : Débutant, vétérant, maitre</span>
										</p>
									</div>
									<div class="bg-grey">
										<p class="table-elem">
											<span class="cell-elem">Débutant</span>
										</p>
									</div>
								</div>
								<div class="holder-item">
									<div class="btn-red">
										<p class="table-elem">
											<span class="cell-elem">Nombre total de parties disputées</span>
										</p>
									</div>
									<div class="bg-grey">
										<p class="table-elem">
											<span class="cell-elem">10</span>
										</p>
									</div>
								</div>
								<div class="holder-item">
									<div class="btn-red">
										<p class="table-elem">
											<span class="cell-elem">Nombre total de parties disputées cette semaine</span>
										</p>
									</div>
									<div class="bg-grey">
										<p class="table-elem">
											<span class="cell-elem">1</span>
										</p>
									</div>
								</div>
								<div class="holder-item">
									<div class="btn-red">
										<p class="table-elem">
											<span class="cell-elem">Meilleur score</span>
										</p>
									</div>
									<div class="bg-grey">
										<p class="table-elem">
											<span class="cell-elem">10 532</span>
										</p>
									</div>
								</div>
								<div class="holder-item">
									<div class="btn-red">
										<p class="table-elem">
											<span class="cell-elem">Meilleur strike</span>
										</p>
									</div>
									<div class="bg-grey">
										<p class="table-elem">
											<span class="cell-elem">0</span>
										</p>
									</div>
								</div>
							</div>
							<div class="counter-result">
								<p>Nombre de partie quotidienne restante:</p><span>2/5</span>
							</div>
						</div>
						<div class="col-md-4">
							<div class="ability-scope bg-orange"><a href="#">Je n'ai plus rien donnez moi 100 pièces !</a></div>
							<div class="ability-scope bg-green"><a href="#">Acheter 5 parties bonus pour 500 pièces</a></div>
							<div class="counter-result">
								<p>Nombre de partie Bonus en stock</p>
							</div>
						</div>
						<div class="col-md-4">
							<div class="ability-scope bg-violet"><a href="#">Donnez moi 1000 pièces je test le site ! ( Beta)</a></div>
							<div class="ability-scope bg-blue"><a href="#">Achetez 72h de partie illimité pour 5€ par paypal</a></div>
							<div class="counter-result">
								<p>Illimité : Oui / non jusqu'a <span class="date">12/03/17 14h24</span></p>
							</div>
						</div>
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