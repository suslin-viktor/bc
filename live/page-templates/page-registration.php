<?php
/*
* Template Name: Page Register
*/
get_header();
wolf_page_before();
?>
	<div id="primary-fullwidth" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail( 'extra-large' ); ?>
						</div>
					<?php endif; ?>
					
					<div class="entry-content">
						<div class="row">
							<div class="col-xs-12 col-sm-offset-3 col-sm-6 col-md-offset-3 col-md-6 col-lg-offset-3 col-lg-6">
								
								<form class="form-register">
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<h2 class="title">Terminer mon inscription et recevoir un mail de validation</h2>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<p><input type="text" class="form-control required-fields" placeholder="Pseudo*" pattern="[A-Za-z]{1,}[A-Za-z0-9]{3,}" required=""/></p>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<p><input type="text" class="form-control required-fields" placeholder="Adresse e-mail*" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required=""/></p>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<p><input type="password" class="form-control required-fields" placeholder="Mot de passe*" required=""/></p>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<p class="keeplogin custom-checkbox">
												<input type="checkbox" id="conditions" name="conditions" required="">
												<label for="conditions">J'accepte les conditions d'utilisation*</label>
											</p>
										</div>
									</div>
									
									<div class="row">
										<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
											<p><input type="text" class="form-control first-row required-fields" placeholder="Nom*" pattern="[A-Za-z]{1,}[A-Za-z0-9]{3,}" required=""/></p>
										</div>
										<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
											<p><input type="text" class="form-control last-row required-fields" placeholder="Prenom*" pattern="[A-Za-z]{1,}[A-Za-z0-9]{3,}" required=""/></p>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<p><input type="text" class="form-control" placeholder="Addresse" pattern="[A-Za-z]{1,}[A-Za-z0-9]{3,}" /></p>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
											<p><input type="text" class="form-control first-row" placeholder="Code postal" pattern="[A-Za-z0-9]{3,}" /></p>
										</div>
										<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
											<p><input type="text" class="form-control last-row" placeholder="Ville" pattern="[A-Za-z]{1,}[A-Za-z0-9]{3,}" /></p>
										</div>
										<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
											<p>
												<select required="" class="select-required-fields required-fields jqui-select">
													<option disabled selected class="red-color">Pays*</option>
													<option value="France">France</option>
													<option value="Germany">Germany</option>
													<option value="Ukraine">Ukraine</option>
													<option value="USA">USA</option>
												</select>
											</p>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
											<input type="submit" class="submit btn-red" value="Page Création de compte " name="submit" />
										</div>
									</div>
								</form>
							</div>
						</div>
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'wolf' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
					</div><!-- .entry-content -->
					
					<footer class="entry-meta">
						<?php edit_post_link( __( 'Edit', 'wolf' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->
				</article><!-- #post -->

				<?php // comments_template(); ?>
			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
<?php 
wolf_page_after();
get_footer(); 
?>