<?php session_start();
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<!-- Meta Tags -->
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<?php wolf_meta_head(); ?>

	<!-- Title -->
	<title><?php wp_title(''); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />

	<!-- RSS & Pingbacks -->
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ); ?> RSS Feed" href="<?php  bloginfo( 'rss2_url' ); ?>" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<link href="<?php echo get_template_directory_uri(); ?>/css/jquery-ui.min.css" rel="stylesheet">
	<link href="<?php echo get_template_directory_uri(); ?>/css/custom.css" rel="stylesheet">
	<link href="<?php echo get_template_directory_uri(); ?>/css/fonts.css" rel="stylesheet">
	<link href="<?php echo get_template_directory_uri(); ?>/css/font-awesome.css" rel="stylesheet">
	
	<?php wp_head(); ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-backward-timer.min.js" type="text/javascript"></script>
	<?php wolf_head(); ?>

	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.js" type="text/javascript"></script>
	<![endif]-->
	<script src='https://www.google.com/recaptcha/api.js'></script>
	
<?php
// проверяем, включена ли игра
if( get_post_meta( 853, 'trigger', 1 ) ) { //птичка switch game status
	
	global $wpdb;
	global $time_current_correct;

	define("PAUSE", get_post_meta( 853, 'pause', 1 ));
	define('ITER', get_post_meta( 853, 'iter', 1 ));
	
	$time_current = strtotime("now");
	$time_current_correct = $time_current + 3*3600; //for Paris change to 2*3600
	$time_start = get_post_meta( 853, 'start_game', 1 );
	
	//echo '<p>'.$time_current.' time current</p>';
	//echo '<p>'.$time_current_correct.' current correct: </p>';
	//echo '<p>'.$time_start.' time start: </p>';
	

	$nearest_playlist = $wpdb->get_var("SELECT playlist_id FROM game_playlist_order WHERE playlist_time > $time_current_correct ORDER BY playlist_time", 0, 0);
	$nearest_playlist_time = $wpdb->get_var("SELECT playlist_time FROM game_playlist_order WHERE playlist_time > $time_current_correct ORDER BY playlist_time", 0, 0);
	
	if ( ($nearest_playlist_time - $time_current_correct) > 0 ) {
		$next_pl_diff = $nearest_playlist_time - $time_current_correct;
	} else {
		$next_pl_diff = PAUSE;
	}
	
	//echo '<p>next playlist id: ' . $nearest_playlist.'</p>';
	//echo '<p>next playlist time: ' . $nearest_playlist_time.'</p>';
	//echo '<p>next_pl_diff: '.$next_pl_diff.'</p>';
	
	/* ----- проверяем, изменилось ли время старта игры и если да, обновляем в базе ----- */
	
	$args = array(
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'post_type'=>'playlists',
		'meta_key' => 'playlist_order',
		'orderby' => 'meta_value',
		'order' => 'ASC'
	);
	
	$query = new WP_Query( $args );
	
	// Цикл
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
		$query->the_post();
			
			$time_start = get_post_meta( 853, 'start_game', 1 ); //скорее всего можно закомментировать
			$row_time = $wpdb->get_results("SELECT time FROM game_playlist", ARRAY_A);
			
			//v($time_start);
			//v($row_time[0]['time']);
			
			if(!$row_time) {
				$wpdb->insert(
					'game_playlist',
					array(
						'time' => $time_start,
					),
					array(
						'%d',
					)
				);
			} elseif ( $time_start != $row_time[0]['time'] ) {
				$wpdb->query("TRUNCATE TABLE game_playlist"); 
				$wpdb->insert(
					'game_playlist',
					array(
						'time' => $time_start,
					),
					array(
						'%d'
					)
				);
			}
			//elseif (когда кончился последний плейлист)
		}
		//endwhile (have_posts)
	} else {
		// Постов не найдено
	}
	wp_reset_postdata();
	/* ----- # проверяем, изменилось ли время старта игры и если да, обновляем в базе ----- */
	
	/* ----- определяем длительности плейлистов ----- */
	
	$args = array(
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'post_type'=>'playlists',
		'meta_key' => 'playlist_order',
		'orderby' => 'meta_value',
		'order' => 'ASC'
	);
	
	$query = new WP_Query( $args );
	
	$count_tracks_array = array(); //id -> num of tracks
	// Цикл
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
		$query->the_post();
			if ( have_rows('playlist') ) {
				$count_track=0;	
					while( have_rows('playlist') ): the_row();
						$count_track=$count_track+1;
				endwhile;
			}
			$count_tracks_array[get_the_ID()] = $count_track;
		}
	}
	//v($count_tracks_array);
	wp_reset_postdata();
	/* ----- # определяем длительности плейлистов ----- */
	
	$gpo = $wpdb->get_results("SELECT * FROM game_playlist_order", ARRAY_A);
	
	//если таблица пуста или текущее время меньше чем время старта игры
	if ( (!$gpo) || ($time_current_correct < $time_start)) {
		
		//echo '1) таблица пуста или текущее время меньше чем время старта игры';
		if ($gpo) {
			$wpdb->query("TRUNCATE TABLE game_playlist_order");
		}
		//select all published playlists sorted by order
		//скорее всего эта хрень не нужна, и можно использовать count_tracks_array
		/*
		$args = array(
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'post_type'=>'playlists',
			'meta_key' => 'playlist_order',
			'orderby' => 'meta_value',
			'order' => 'ASC'
		);
		
		$query = new WP_Query( $args );
		*/
		$i = 1; //номер плейлиста
		for ($j=1; $j <= ITER; $j++) {
			foreach ($count_tracks_array as $id => $num_tracks) {
				
				if ($i == 1) {
					$wpdb->insert(
						'game_playlist_order',
						array(
							'playlist_id' => $id,
							'playlist_time' => $time_start
						),
						array(
							'%d',
							'%d'
						)
					);
					$last_id = $id;
					
				} else {
						
					$duration = $count_tracks_array[$last_id]*30;
					
					// старый запрос
					//$cur_pl_start = $wpdb->get_var("SELECT playlist_time FROM game_playlist_order WHERE playlist_id = $last_id", 0, 0 ) + $duration + PAUSE;
					
					//$cur_pl_start = $wpdb->get_var("SELECT MAX(playlist_time) AS playlist_time FROM game_playlist_order") + $duration + PAUSE;
					$cur_pl_start = $wpdb->get_var("SELECT playlist_time FROM game_playlist_order GROUP BY playlist_time DESC", 0, 0 ) + $duration + PAUSE;
					$cur_pl_start = (int) $cur_pl_start;
					
					$wpdb->insert(
						'game_playlist_order',
						array(
							'playlist_id' => $id,
							'playlist_time' => $cur_pl_start,
						),
						array(
							'%d',
							'%d'
						)
					);
					
					$last_id = $id;
					
				}
					
				$i++;
					
			} // # foreach
		} // # for 
	
	//если таблица не пуста
	
	} else {
		
		$cur_max_pl_start = $wpdb->get_var("SELECT playlist_time FROM game_playlist_order GROUP BY playlist_time DESC", 0, 0 );
		$cur_max_pl_start = (int)$cur_max_pl_start;
		
		// если текущее время меньше чем начало последнего плейлиста
		
		if ( $time_current_correct < $cur_max_pl_start ) {
			//echo '2) если текущее время меньше чем начало последнего плейлиста';
			//echo '<p>max pl start:'.$cur_max_pl_start.'</p>';
			/*
			
			
			$wpdb->query("TRUNCATE TABLE game_playlist_order");
			
			//update table
			
			$i=1;
			for ($j=1; $j < ITER; $j++) {
				foreach ($count_tracks_array as $id => $num_tracks) {
					
					if ($i == 1) {
						
						$wpdb->insert(
						'game_playlist_order',
							array(
								'playlist_id' => $id,
								'playlist_time' => $cur_max_pl_start + PAUSE,
								//'playlist_time' => $time_start,
							),
							array(
								'%d',
								'%d'
							)
						);
						
						$last_id = $id;
						
					} else {
						
						$duration = $count_tracks_array[$last_id]*30;
						$cur_pl_start = $wpdb->get_var("SELECT playlist_time FROM game_playlist_order GROUP BY playlist_time DESC", 0, 0 ) + $duration + PAUSE;
						$cur_pl_start = (int)$cur_pl_start;
						
						$wpdb->insert(
						'game_playlist_order',
							array(
								'playlist_id' => $id,
								'playlist_time' => $cur_pl_start,
							),
							array(
								'%d',
								'%d',
							)
						);
						
						$last_id = $id;
						
					}
					
					$i++;
					
				}
			}
		*/
		
		//если текущее время больше чем начало последнего плейлиста на менее чем 30 сек
		
		} elseif ( ( $time_current_correct > $cur_max_pl_start ) && ( ( $time_current_correct - $cur_max_pl_start ) < 30) ) {
			
			//echo '3) если текущее время больше чем начало последнего плейлиста на менее чем 30 сек';
			
			//получить длительность последнего плейлиста
			$count = count($content_tracks_array);
			$duration = $content_tracks_array[$count - 1] * 30;
			
			//v($content_tracks_array);
			//v($duration);
			
			$i=1;
			for ($j=1; $j < ITER; $j++) {
				foreach ($count_tracks_array as $id => $num_tracks) {
					if ($i == 1) {
						$wpdb->insert(
						'game_playlist_order',
							array(
								'playlist_id' => $id,
								'playlist_time' => $cur_max_pl_start + $duration + PAUSE,
							),
							array(
								'%d',
								'%d'
							)
						);
						$last_id = $id;
					} else {
						$cur_pl_start = $wpdb->get_var("SELECT playlist_time FROM game_playlist_order GROUP BY playlist_time DESC", 0, 0 );
						$cur_pl_start = (int)$cur_pl_start;
						
						$duration = $count_tracks_array[$last_id]*30;
						$cur_pl_start = $cur_pl_start + $duration + PAUSE;
						
						$wpdb->insert(
						'game_playlist_order',
							array(
								'playlist_id' => $id,
								'playlist_time' => $cur_pl_start,
							),
							array(
								'%d',
								'%d',
							)
						);
						$last_id = $id;
					}
					$i++;
				} // # foreach
			} // # for
		
		//если текущее время больше чем начало последнего плейлиста на более чем 30 сек
		
		} elseif ( ($time_current_correct > $cur_max_pl_start) && (($time_current_correct - $cur_max_pl_start) > 30)  ) {
			
			//echo '4) если текущее время больше чем начало последнего плейлиста на более чем 30 сек';
			//echo '<p>time_current_correct: '.$time_current_correct.'</p>';
			
			$i=1;
			for ($j=1; $j < ITER; $j++) {
				foreach ($count_tracks_array as $id => $num_tracks) {
					if ($i == 1) {
						$wpdb->insert(
						'game_playlist_order',
							array(
								'playlist_id' => $id,
								'playlist_time' => $time_current_correct + PAUSE,
							),
							array(
								'%d',
								'%d'
							)
						);
						$last_id = $id;
					} else {
						$cur_pl_start = $wpdb->get_var("SELECT playlist_time FROM game_playlist_order GROUP BY playlist_time DESC", 0, 0 );
						$cur_pl_start = (int)$cur_pl_start;
						
						$duration = $count_tracks_array[$last_id]*30;
						$cur_pl_start = $cur_pl_start + $duration + PAUSE;
						
						$wpdb->insert(
						'game_playlist_order',
							array(
								'playlist_id' => $id,
								'playlist_time' => $cur_pl_start,
							),
							array(
								'%d',
								'%d',
							)
						);
						$last_id = $id;
					}
					$i++;
				} // # foreach
			} // # for
		}
	} // # если таблица не пуста
	
	//инициализируем переменные для счетчика
	
	if ($time_start > $time_current) {
		$time_diff = $time_start - $time_current; //время до первой игры
	} else {
		$time_next = $time_start + $duration;
		//т.к первый плейлист уже 4й, вычисляем время до старта 2 плейлиста
		//
		// $time_diff = ($time_start + колво-треков в макс pl_order плейлисте * 30 + константа паузы между играми) - $stime
	}
	
} else {
	$wpdb->query("TRUNCATE TABLE game_playlist_order");
}
// # проверяем, включена ли игра
?>
	
	<input type="hidden" id="temptimer" value=""/>
	<script type="text/javascript">
		
		function getJeu() {
			(function ( $ ) {
				jQuery(document).ready(function () {
					$("#jeuinner").load('http://breizh-concept.flntest.com/wp-content/themes/live/page-templates/page-jeu-inner.php');
				});
			})(jQuery);
		}
		
		function getDummy() {
			(function ( $ ) {
				jQuery(document).ready(function () {
					$("#jeuinner").load('http://breizh-concept.flntest.com/wp-content/themes/live/page-templates/page-dummy.php');
				});
			})(jQuery);
		}
		
		//отображение таймера
		var x = new Date();
		var timeZone = -x.getTimezoneOffset()*60 //in seconds;
		
		var time_diff = <?=json_encode($time_diff)?> - timeZone; //время до первой игры
		document.getElementById('temptimer').value = time_diff;
		
		var next_pl_diff = <?=json_encode($next_pl_diff)?>; //время до следующего плейлиста
		
		//document.getElementById('temptimer').value = 605;
		
		//таймер на главной странице
		if (document.getElementById('temptimer').value > 0) {
			$(document).ready(function(){
				
				$('#counter').backward_timer({
					seconds: time_diff,
					//seconds: 605,
					//format: 'm%:s%'
					
					on_tick: function(timer) {
						if (timer.seconds_left < 600) {
							//$("#linkstart").attr("href", "/jeu");
							
							$("#formstart").attr("action", "http://breizh-concept.flntest.com/jeu");
						}
					},
					
					on_exhausted: function(timer) {
						$("#linkstart").attr("href", "/jeu");
					}
					
				});
				
				$('#counter').backward_timer('start');
			});
		
		} /*else {

			$(document).ready(function(){
				
				$('#counter').backward_timer({
					seconds: time_diff,
					//seconds: 605,
					//format: 'm%:s%'
					
					on_tick: function(timer) {
						if (timer.seconds_left < 600) {
							//$("#linkstart").attr("href", "/jeu");
							
							$("#formstart").attr("action", "http://breizh-concept.flntest.com/jeu");
						}
					},
					
					on_exhausted: function(timer) {
						$("#linkstart").attr("href", "/jeu");
					}
					
				});
				
				$('#counter').backward_timer('start');
			});
		}*/
		
		//таймер на page-jeu.php
		
		if (document.getElementById('temptimer').value > 0) { //игра не началась, время до ACF: game_start
			
			$(document).ready(function(){
				$('#counter2').backward_timer({
					seconds: time_diff,
					
					on_tick: function(timer) {
						if (timer.seconds_left > 1) {
							getDummy();
						}
					},
					on_exhausted: function(timer) {
						getJeu();
					}
				});
				$('#counter2').backward_timer('start');
			});
			
		} else { //игра началась, время до ближайшего будущего плейлиста
			
			$(document).ready(function() {
				$('#counter2').backward_timer({
					
					seconds: next_pl_diff, 
					
					on_tick: function(timer) {
						if (timer.seconds_left > 1) {
							getDummy();
						}
					},
					
					on_exhausted: function(timer) {
						getJeu();
					}
					
				});
				
				$('#counter2').backward_timer('start');
			});
		
		}
		
		//хз для чего
		/*
		$(document).ready(function(){
			$('#counter3').backward_timer({
				seconds: time_diff,
				format: 'm%:s%'
			});
			
			$('#counter3').backward_timer('start');
		});
		*/
	</script>

    <script src="//ulogin.ru/js/ulogin.js"></script>
	
</head>

<body <?php body_class(); ?> <?php/* if (is_front_page()) : ?> onload="showtime()" <?php endif;*/ ?>>
	<span id="timer_on_exhausted" class="timer"></span>
	<?php if ( function_exists( 'wolf_message_bar' ) ) wolf_message_bar(); ?>
	<div id="top"></div><a id="top-arrow" class="scroll" href="#top"></a>
	<?php wolf_body_start(); ?>
	
	<div id="page" class="hfeed site">
		<header id="masthead" class="site-header" role="banner">
			<div class="container">
				<div class="row">
					
					<div class="col-xs-12 col-sm-12 col-md-3">
						<div class="header-left">
							<div>
								<a class="create-acc" href="<?php echo get_the_permalink(488) ?>">Creer un compte</a>
							</div>
                            <!--
							<div>
								<a class="fb-reg" href="#">S'incrire via facebook</a>
							</div>
							-->
						</div>
					</div>
					
					<div class="col-xs-12 col-sm-12 col-md-6">
						<div class="table-head">
							<?php wolf_logo(); ?>
						</div>
					</div>
					
					<div class="col-xs-12 col-sm-12 col-md-3">
						<div class="holder-header-right">
                            <div class="header-right">
                            <?php

                            if ( is_user_logged_in() ) {
                                $current_user = wp_get_current_user();
                                $user_name = $current_user->user_login;
                                ?>
                                <p>Bonjour, <?= $user_name;?>.</p>
                                <a href="<?php echo wp_logout_url( home_url() ); ?>" title="exit">Se déconnecter</a>

                                <?php
                            }
                            else {
                                ?>

                                    <?php $current_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>
                                    <form action="<?php echo wp_login_url(get_permalink()); ?>" method="post">
                                        <p><i class="fa fa-user" aria-hidden="true"></i><input name="log" type="text" value="" placeholder="Login" /></p>
                                        <p><i class="fa fa-lock" aria-hidden="true"></i><span class="span-pass"></span><input name="pwd" type="password" value="" placeholder="Mot de passe" /></p>
                                        <p class="keeplogin clearfix custom-checkbox">
                                            <input id="rememberme" type="checkbox" value="forever" name="rememberme">
                                            <label for="rememberme">Rester connecter</label>
                                        </p>
                                        <p><input type="submit" value="S'identifier" /></p>
                                    </form>
                                    <div class="social-buttons">
                                        <div class="ulogin_block">
                                            <div class="ulogin_label">S`incrire via&nbsp;</div>
                                            <div id="uLogin05381714" class="ulogin_panel" data-uloginid="2fb60897" data-ulogin="redirect_uri=http%3A%2F%2Fbreizh-concept.flntest.com%2F%3Fulogin%3Dtoken%26backurl%3D%252Fyour-profile%252F;" data-ulogin-inited="1517324945931">
                                                <div class="ulogin-buttons-container" style="margin: 0px; padding: 0px; outline: none; border: none; border-radius: 0px; cursor: default; float: none; position: relative; display: inline-block; width: 84px; height: 32px; left: 0px; top: 0px; box-sizing: content-box; max-width: 100%; vertical-align: top; line-height: 0;">
                                                    <div class="ulogin-button-google" data-uloginbutton="google" role="button" title="Google" style="margin: 0px 10px 10px 0px; padding: 0px; outline: none; border: none; border-radius: 0px; cursor: pointer; float: left; position: relative; display: inherit; width: 32px; height: 32px; left: 0px; top: 0px; box-sizing: content-box; background: url(&quot;https://ulogin.ru/version/2.0/img/providers-32-flat.png?version=img.2.0.0&quot;) 0px -206px / 32px no-repeat;"></div>
                                                    <div class="ulogin-button-facebook" data-uloginbutton="facebook" role="button" title="Facebook" style="margin: 0px 10px 10px 0px; padding: 0px; outline: none; border: none; border-radius: 0px; cursor: pointer; float: left; position: relative; display: inherit; width: 32px; height: 32px; left: 0px; top: 0px; box-sizing: content-box; background: url(&quot;https://ulogin.ru/version/2.0/img/providers-32-flat.png?version=img.2.0.0&quot;) 0px -138px / 32px no-repeat;"></div>
                                                </div>
                                            </div>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>

                                <?php
                            }
                            ?>
                            </div>
						</div>
					</div>
					
				</div>
					
				<div class="row">
					<div class="col-sm-offset-2 col-sm-10 col-md-offset-2 col-md-10 col-lg-offset-3 col-lg-6">
						<nav id="primary-menu-container" role="navigation" class="site-navigation main-navigation clearfix">
							<div class="wrap wrap-list-menu">
								<?php
								wp_nav_menu(array(
									'theme_location' => 'primary',
									'menu_id'         => 'primary-menu',
									'menu_class' => 'nav-menu'
								));
								?>
							</div>
						</nav>
					</div>
				</div>
			</div>
		</header>
		
		<nav id="mobile-menu-container" role="navigation" class="site-navigation mobile-navigation">
			<ul id="mobile-menu-dropdown">
				<li><?php _e(); ?></li>
			</ul>
			<?php
			wp_nav_menu(array(
				'theme_location' => 'primary',
				'menu_id'         => 'mobile-menu',
			));
			?>
		</nav>
	
		<?php wolf_header_after(); ?>
	
		<section id="main" class="site-main clearfix">
			<div class="wrap">