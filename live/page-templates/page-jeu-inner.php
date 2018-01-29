<?php 

require_once( explode( "wp-content" , __FILE__ )[0] . "wp-load.php" ); ?>

<div id="first_fone" class="amount-strike btn-red" style="display: none">
	<span>Strike ! X2</span>
</div>

<?
$user_id = get_current_user_id();
$time_current = strtotime("now");
$time_current_correct = $time_current + 3*3600;

$current_playlist_id = $wpdb->get_var("SELECT playlist_id FROM game_playlist_order WHERE playlist_time < $time_current_correct ORDER BY playlist_time DESC", 0, 0);

// Вытаскиваем ID для записи лога
$game_id = $wpdb->get_var("SELECT ID FROM game_playlist_order WHERE playlist_time < $time_current_correct ORDER BY playlist_time DESC", 0, 0);

// Делаем запись в log_game для видения счета игры
$wpdb->insert(
    'log_game',
    array(
        'game_id' => $game_id,
        'user_id' => $user_id,
        'balls1' => 0,
        'balls2' => 0,
        'balls' => 0
    ),
    array( '%d', '%d', '%f', '%f', '%f' )
);

// Делаем запись в log_round для видения счета по раунду
/*
$wpdb->insert(
    'log_round',
    array(
        'game_id' => $game_id,
        'user_id' => $user_id,
        'round' => 1,
        'status1' => 0,
        'status2' => 0,
        'game_balls1' => 0,
        'bonus_balls1' => 0,
        'game_balls2' => 0,
        'bonus_balls2' => 0,
        'game_balls' => 0
    ),
    array( '%d', '%d', '%d', '%d', '%d', '%f', '%f', '%f', '%f', '%f' )
);
*/

//Делаем запись в log_strike
$wpdb->insert(
    'log_strike',
    array(
        'game_id' => $game_id,
        'user_id' => $user_id,
        'count' => 0,
        'count_bonus' => 0,
    ),
    array( '%d', '%d', '%d', '%d' )
);

//И пишем бонус
$wpdb->insert(
    'log_bonus',
    array(
        'game_id' => $game_id,
        'user_id' => $user_id,
        'bonus' => 0
    ),
    array( '%d', '%d', '%d' )
);
//echo '<p>current_playlist id: '.$current_playlist_id.'</p>';

$args = array(
	'post_status' => 'publish',
	'posts_per_page' => 1,
	'post_type'=>'playlists',
	'p' => $current_playlist_id //id, который будем тянуть из 'game_playlist_order' => 'playlist_id'
	/*
	'meta_key' => 'playlist_order',
	'orderby' => 'meta_value',
	'order' => 'ASC'
	 */
);

$query = new WP_Query( $args );

// Цикл
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
	
	$query->the_post();
		
		//echo '<p>current playlist id: '.$post->ID.'</p>';
		
		if ( have_rows('playlist') ) {
			
			$playlist = array();
			$shuffled_playlist = array();
			$list_url = array();
			$list_artist = array();
			$list_song = array();
			$i=0;
			
			//берем текущий плейлист..
			while( have_rows('playlist') ): the_row();
				$list_url = get_sub_field('track');
				$list_artist = get_sub_field('artist');
				$list_song = get_sub_field('song');
				$playlist['elem'.$i] = array($list_url,$list_artist,$list_song);
				$i++;
			endwhile;
			
			// .. и перемешиваем
			$keys = array_keys( $playlist );
			shuffle($keys);
			
			//записываем перемешанный массив в таблицу бд с меткой времени
			global $wpdb;
			$time_current = strtotime("now");
			
			//если плейлист в бд пуст
			$table_row = $wpdb->get_row("SELECT time FROM game_playlist");
			if (!$table_row) {
				$tn = 1;
				foreach ($keys as $key) {
					$url = $playlist[$key][0];
					$artist = $playlist[$key][1];
					$song = $playlist[$key][2];
					$wpdb->insert(
						'game_playlist',
						array(
							'tracknum' => $tn,
							'time' => $time_current,
							'url' => $url,
							'artist' => $artist,
							'song' => $song,
							'playlist_id' => $post->ID
						),
						array(
							'%d',
							'%s',
							'%s',
							'%s',
							'%s',
							'%d'
						)
					);
					$tn++;
				}
				
			} else {
				
				// TRUNCATE TABLE  table_name
				$wpdb->query("TRUNCATE TABLE game_playlist");
				
				$tn = 1;
				foreach ($keys as $key) {
					$url = $playlist[$key][0];
					$artist = $playlist[$key][1];
					$song = $playlist[$key][2];
					$wpdb->insert(
						'game_playlist',
						array(
							'tracknum' => $tn,
							'time' => $time_current,
							'url' => $url,
							'artist' => $artist,
							'song' => $song,
							'playlist_id' => $post->ID
						),
						array(
							'%d',
							'%s',
							'%s',
							'%s',
							'%s',
							'%d'
						)
					);
					$tn++;
				}
				
			}

			$urls = $wpdb->get_results("SELECT url FROM game_playlist ORDER BY tracknum", ARRAY_N);
			?>
			
			<audio id="audio" preload="auto" tabindex="0" controls="" type="audio/mpeg" autoplay onended="reloadGame()" onplaying="onNextTrack()" style="display: none;">
				<source type="audio/mp3" src="<?php echo $urls[0][0]; ?>">
				Sorry, your browser does not support HTML5 audio.
			</audio>
			
			<ul id="playlist" style="display: none;">
				<?php
				$i = 1;
				foreach($urls as $list_item) {
					$i == 1 ? $class = 'active' : $class = '' ; ?>
					<li class="<?php echo $class; ?>"><a href="<?php echo $list_item[0]; ?>">track<?php echo $i; ?></a></li>
				<?php $i++;
				}
				?>
			</ul>
			
			<?php
			/*
			$current_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>
			
			<script>
				var cur_url = <?=json_encode( $current_url )?>;
				function reloadGame() {
					document.location.href= cur_url;
				}
			</script>
			*/
			?>
	<?php }

	} //endwhile (have_posts)

} else {
	// Постов не найдено
}

wp_reset_postdata();

?>

<div class="progress-box">
	<ul class="line-progress">
		<li class="big-progress-line"><span class="start-line">0:00</span></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		
		<li class="big-progress-line"><span class="middle-line">0:10</span></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li class="big-progress-line"><span class="finish-line">0:30</span></li>
	</ul>
	
	<div class="progress-bar">
		<div class="progress" style="width: 0%"></div>
	</div>
	
	<!-- <ul class="time-progress">
		<li>0:00</li>
		<li class="mark-time-progress">0:10</li>
		<li>0:30</li>
	</ul> -->
</div>

<div class="status">
	<p class="correct">Correctement!</p>
	<p class="incorrect">Incorrectement!</p>
</div>

<div class="send-form">
	
	<form id="formid" name="" action="" method="post" onsubmit="getAjaxSend()">
		<!--
		<textarea id="user_input" name="user_input" placeholder="Taper les titres de chansons et des artistes." maxlength="80"></textarea>
		-->
		<input style="width: 100%; padding: 20px;" type="text" id="user_input" name="user_input" placeholder="Tapez le nom de artiste et le titre de chanson." maxlength="80">
        <!--
		<a id="submit"  class="send" href="javascript: getAjaxSend();">Send</a>
        -->
        <input type="hidden" id="game_id" name="game_id" value="<?= $game_id ;?>">
		<script>
			getAjax();
		</script>
	</form>
	
	<script>
	(function ( $ ) {
		jQuery(document).ready(function () {
			$('#formid').submit(function (e) {
				e.preventDefault();
				var elem = document.getElementById("formid");
				elem.setAttribute("name", "new");
			});
		});
	})(jQuery);
	</script>
	
</div>

<div>Text print: <span id="text_print"></span></div>

<div>Artist: <span id="artist"></span></div>
<div>Song: <span id="song"></span></div>
<div>Status: <span id="status1"></span>; <span id="status2"></span></div>
<!--
<div>correct_artist: <span id="view1"></span></div>
<div>correct_song: <span id="view2"></span></div>
<div>input_first: <span id="view3"></span></div>
<div>input_second: <span id="view4"></span></div>
-->
<script>
    function onNextTrack() {
        //document.getElementById("submit").style.display = "block";
        document.getElementById("artist").innerHTML = '';
        document.getElementById("song").innerHTML = '';
        document.getElementById('user_input').removeAttribute("disabled");
        document.getElementById('first_fone').style.display = 'none';

        //Далее обнуляем статусы в game_log для второго раунда
        var url = 'http://' + location.hostname + '/ajax-reset-game-log/'; //тут сброс на рнр
        var fd = new FormData(); //зарезервированный класс
        fd.append('game_id', document.getElementById('game_id').value);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", url);

        xhr.onreadystatechange = function() {
            if (this.readyState !== 4) {
                return;
            }
            resp = eval(this.responseText);

            var newLi = document.createElement('li');
            if(!!resp.artist&&!!resp.song){
                newLi.innerHTML = resp.artist + ' - ' + resp.song;
                var list = document.getElementById('list-artist-song');
                list.insertBefore(newLi, list.children[1]);
            }

            if( resp.bonus == 1 ){
                document.getElementById('first_fone').style.display = 'block';
            }
        };

        xhr.send(fd);
        //конец обнуленяи в game_log

    }
</script>

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
			<ul id="balls_artist" class="list-block-holder mini-list-block-holder">

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
			<ul id="balls_song" class="list-block-holder mini-list-block-holder">

			</ul>
		</div>
	</div>	
</div>

<script>

(function ( $ ) {
	
	$(document).ready(function() {
		initTrackListener();
	});

    setTimeout(function run() {
        updatePointsArtist(<?= $game_id ;?>);
        updatePointsSong(<?= $game_id ;?>);
        updateRatingGame(<?= $game_id ;?>);
        setTimeout(run, 1000); //кажду секунду
    }, 10);

    function updatePointsArtist( game_id ) {
        // Отправляем запро с id игры
        var url = 'http://' + location.hostname + '/ajax-balls-artist/'; //тут php обработчик, ожидвем от него нужный нам блок html
        var fd = new FormData(); //зарезервированный класс
        fd.append('game_id', game_id);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", url);

        xhr.onreadystatechange = function() {
            if (this.readyState !== 4) {
                return;
            }

            document.getElementById("balls_artist").innerHTML = xhr.responseText;

        };

        xhr.send(fd);
    }

    function updatePointsSong( game_id ) {
        // Отправляем запро с id игры
        var url = 'http://' + location.hostname + '/ajax-balls-song/'; //тут php обработчик, ожидвем от него нужный нам блок html
        var fd = new FormData(); //зарезервированный класс
        fd.append('game_id', game_id);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", url);

        xhr.onreadystatechange = function() {
            if (this.readyState !== 4) {
                return;
            }

            document.getElementById("balls_song").innerHTML = xhr.responseText;

        };

        xhr.send(fd);
    }

    function updateRatingGame( game_id ) {
        // Узнаем лидиров игры
        var url = 'http://' + location.hostname + '/ajax-rating-game/'; //тут сброс на рнр
        var fd = new FormData(); //зарезервированный класс
        fd.append('game_id', game_id);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", url);

        xhr.onreadystatechange = function() {
            if (this.readyState !== 4) {
                return;
            }

            document.getElementById("list-rating-game").innerHTML = xhr.responseText;

        };
        xhr.send(fd);
    }
	
	function initTrackListener() {
		
		var track = document.getElementsByTagName('audio')[0];
		//var track = document.getElementsByClassName("amazingaudioplayer-play");
		if (!track) { return; }
	
		track.ontimeupdate = onTrackTimeChanged;
	}
						
	function onTrackTimeChanged() {
	
		var progressBar = $('.progress-box').find('.progress');
		var progressBarFullWidth = $('.progress-box').find('.progress-bar').width() || 0;
		var progressBarCurreentWidth = progressBar.width();
		var progressWidth;
	
		if (!progressBar.hasClass('bg-grey') && this.currentTime > 10) {
			progressBar.addClass('bg-grey');
		}

        if (progressBar.hasClass('bg-grey') && this.currentTime < 10) {
            progressBar.removeClass('bg-grey');
        }

	
		if(this.currentTime < 30) {
			progressWidth = parseInt(this.currentTime) * progressBarFullWidth/30;
			progressBar.css('width', progressWidth + 'px');
		} else {
			progressWidth = parseInt(this.currentTime) * progressBarFullWidth/30;
			progressBar.css('width', '100%');
		}


	}
	
	//onTrackTimeChanged();
	
	//music player
	var audio;
	var playlist;
	var tracks;
	var current;
	
	init();
		
	function init(){
		current = 0;
		audio = $('audio');
		playlist = $('#playlist');
		tracks = playlist.find('li a');
		//len = tracks.length - 1;
		len = tracks.length;
		audio[0].volume = 1;
		playlist.find('a').click(function(e){
			e.preventDefault();
			link = $(this);
			current = link.parent().index();
			run(link, audio[0]);
		});
		audio[0].addEventListener('ended',function(e){
			current++;
			link = playlist.find('a')[current];
			
			if(current == len) {
				location.reload();
			}
			
			/*
			if(current == len) {
				current = 0;
				link = playlist.find('a')[0];
			} else {
				link = playlist.find('a')[current];
			}
			*/
			run($(link),audio[0]);
		});
	}
		
	function run(link, player) {
		player.src = link.attr('href');
		par = link.parent();
		par.addClass('active').siblings().removeClass('active');
		audio[0].load();
		audio[0].play();
	}
	

})(jQuery);
</script>
