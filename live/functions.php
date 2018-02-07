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

add_action( 'wp_ajax_game_balls', 'function_player_points' ); // wp_ajax_{ЗНАЧЕНИЕ ПАРАМЕТРА ACTION!!}
add_action( 'wp_ajax_nopriv_game_balls', 'function_player_points' );  // wp_ajax_nopriv_{ЗНАЧЕНИЕ ACTION!!}
// первый хук для авторизованных, второй для не авторизованных пользователей

//round statistics and games
function function_player_points(){

    $game_id = $_POST['game_id'];
    global $wpdb;
    $round = $wpdb->get_var("SELECT MAX(`round`) FROM `log_round` WHERE game_id = $game_id");
    $results_round = $wpdb->get_results("SELECT * FROM `log_round` WHERE game_id = $game_id AND round = $round", ARRAY_A );
    $results_game = $wpdb->get_results("SELECT user_id, balls FROM `log_game` WHERE game_id = $game_id ORDER BY balls DESC", ARRAY_A );
    $game_info = array();
    $round_info = array();
    $reply = array();
    $i=0;
    foreach( $results_round as $result_round){

        $user_info = get_userdata($result_round['user_id']);
        $balls1 = $result_round['game_balls1'] + $result_round['bonus_balls1'];
        $balls2 = $result_round['game_balls2'] + $result_round['bonus_balls2'];

        $round_info[$i] = array(
            'user_name' => $user_info->user_login,
            'balls' => array(
                    'balls_artist' => $balls1,
                    'balls_song' => $balls2
            )
        );
        $i++;
    }
    $reply['round'] = $round_info;

    $k=1;
    foreach( $results_game as $result_game){
        $user_info = get_userdata($result_game['user_id']);
        $game_balls = $result_game['balls'];
        $game_info[$k] = array(
            'user_name' => $user_info->user_login,
            'balls' => $game_balls,
            'prize_winner' => $k
        );
        $k++;
    }
    $reply['game'] = $game_info;

    echo json_encode( $reply );

    die; // даём понять, что обработчик закончил выполнение
}

add_action( 'wp_ajax_input_data', 'function_input_data' ); // wp_ajax_{ЗНАЧЕНИЕ ПАРАМЕТРА ACTION!!}
add_action( 'wp_ajax_nopriv_input_data', 'function_input_data' );  // wp_ajax_nopriv_{ЗНАЧЕНИЕ ACTION!!}
// первый хук для авторизованных, второй для не авторизованных пользователей

//verification of entered data
function function_input_data(){

	global $wpdb; //подключаемся к базе
	$sendresp = array(); //создаем массив вывода

	$stime = strtotime("now"); //получаем время нажатия кнопки send
	$user_id = get_current_user_id(); //получаем id пользователя

	// обработка пользовательского ввода
	if (isset($_POST["user_input"])) :
	    $user_in = strtolower($_POST["user_input"]); //переводит в нижний регистр
	    $user_in = trim($user_in); //удаляет пробелы из конца и начала строки
	    $user_in = strip_tags($user_in); //удаляет HTML и PHP-тегов
	    $user_in = htmlspecialchars($user_in); //преобразует специальные символы в HTML-сущности
	    $input = $user_in;
	    $is_dash = stristr($user_in, '-'); //false если нет дефиса || возврат строки начиная с дефиса
	    $game_id = $_POST["game_id"]; //извлекаем id игры (hidden в форме)

	    //обновляем время нажатия кнопки в таблице
	    $wpdb->update(
	        'log_time',
	        array(
	            'resp_time' => $stime,
	        ),
	        array( 'user_id' => $user_id, 'game_id' => $game_id )
	    );

	    $table_row = $wpdb->get_row("SELECT * FROM log_time WHERE user_id = $user_id AND game_id = $game_id");
	    $response_time = $table_row->resp_time - $table_row->start_time;

	    //Какой идет раунд
	    $query = "SELECT MAX(round) FROM log_round WHERE user_id = $user_id AND game_id = $game_id";
	    $last_round = $wpdb->get_var($query);

	    // номер трека = номер раунда
	    $ntrack = $last_round;

	    $playlist = array();

	    $artist_arr = $wpdb->get_results(("SELECT artist FROM game_playlist WHERE tracknum='$ntrack'"), ARRAY_N);
	    $song_arr = $wpdb->get_results(("SELECT song FROM game_playlist WHERE tracknum='$ntrack'"), ARRAY_N);

	    $artist = $artist_arr[0];
	    $song = $song_arr[0];

	    array_push($playlist, array($artist[0],$song[0]));

	    foreach ($playlist as $key => &$value) {
	        $value[0] = strtolower($value[0]);
	        $value[1] = strtolower($value[1]);
	    }
	    unset($value);

	    $ntrack = $ntrack - 1;

	    $correct_artist = double_metaphone($playlist[0][0]);
	    $correct_song = double_metaphone($playlist[0][1]);

	    //Для записи в логи
	    $status1 = $status2 = $proc_artist = $proc_song = 0;

		if (!$is_dash) { //если не было дефиса
			$user_in = double_metaphone( $user_in );

			$similar_artist = similar_text( $correct_artist['primary'] , $user_in['primary'], $percent10 );
			$similar_song = similar_text( $correct_song['primary'] , $user_in['primary'], $percent11 );
	        $proc_artist = $percent10;
	        $proc_song = $percent11;

	        if ($percent10 > 75 ) {
	            $sendresp['artist'] = 'correct';
	            $sendresp['status1'] = 1;
	            $status1 = 1;
	        } elseif ($percent11 > 75) {
	            $sendresp['song'] = 'correct';
	            $sendresp['status2'] = 1;
	            $status2 = 1;
	        } else {
	            $sendresp['artist'] = '<span class="incorrect">incorrect, try again </span>';
	            $sendresp['song'] = '<span class="incorrect">incorrect, try again </span>';
	        }


		} else {
			$user_in_arr = explode("-", $user_in);

			$ifirst = $user_in_arr[0];
			$isecond = $user_in_arr[1];

			$percent = 60;

			$input_first = double_metaphone( $ifirst );
			$input_second = double_metaphone( $isecond );

			$var1 = similar_text( $correct_artist['primary'] , $input_first['primary'], $percent1 );
			$var2 = similar_text( $correct_song['primary'] , $input_first['primary'], $percent2 );

			$var3 = similar_text( $correct_artist['primary'] , $input_second['primary'], $percent3 );
			$var4 = similar_text( $correct_song['primary'] , $input_second['primary'], $percent4 );


			if( $percent1 > $percent) {
			    $sendresp['artist'] = 'correct';
			    $sendresp['status1'] = 1;
	            $status1 = 1;
	            $proc_artist = $percent1;

			}elseif( $percent3 > $percent ){
	            $sendresp['artist'] = 'correct';
	            $sendresp['status1'] = 1;
	            $status1 = 1;
	            $proc_artist = $percent3;

	        } else {
			    $sendresp['artist'] = '<span class="incorrect">incorrect, try again </span>';
			}

			if( $percent2 > $percent ) {
			    $sendresp['song'] = 'correct';
			    $sendresp['status2'] = 1;
	            $status2 = 1;
	            $proc_song = $percent2;
			} elseif($percent4 > $percent){
	            $sendresp['song'] = 'correct';
	            $sendresp['status2'] = 1;
	            $status2 = 1;
	            $proc_song = $percent4;
	        } else {
			    $sendresp['song'] = '<span class="incorrect">incorrect, try again </span>';
			}

			$sendresp['view1'] = $correct_artist['primary']; //ARTIST - song s
			$sendresp['view2'] = $correct_song['primary']; //artist - SONG false
			$sendresp['view3'] = $input_first['primary']; //song - ARTIST false
			$sendresp['view4'] = $input_second['primary']; //SONG - artist s
		}

		//Извлекаем балы за игру
	    $result = $wpdb->get_row("SELECT * FROM log_game WHERE user_id = $user_id AND game_id = $game_id", ARRAY_A );
		$balls1 = $result['balls1']; // балы за артиста
		$balls2 = $result['balls2']; // балы за песню
		$balls = $result['balls']; //общие кол. балов за игру

	    //Извлекаем статусы и балы за раунд
	    $result = $wpdb->get_row("SELECT * FROM log_round WHERE user_id = $user_id AND game_id = $game_id AND round = $last_round", ARRAY_A );
	    $game_balls1 = $result['game_balls1']; // балы за артиста
	    $game_balls2 = $result['game_balls2']; // балы за песню
	    $bonus_balls1 = $result['bonus_balls1']; // бонус за артиста
	    $bonus_balls2 = $result['bonus_balls2']; // бонус за песню
	    $game_balls = $result['game_balls']; //общие кол. балов за игру

		//Извлекаем балы общие игрока
	    $all_balls = $wpdb->get_var("SELECT balls FROM user_balls WHERE user_id = $user_id");

	    //Извлекаем количество правельных ответов для страйка
	    $count_bonus = $wpdb->get_var("SELECT count_bonus FROM log_strike WHERE user_id = $user_id AND game_id = $game_id");

	    //Проверяем есть ли страйк
	    $bonus = $wpdb->get_var("SELECT bonus FROM log_bonus WHERE user_id = $user_id AND game_id = $game_id");

	    // Проверяем был ли правельный ответ
	    $correct_answer1 = false;
	    $correct_answer2 = false;
		if ( $game_balls1 == 0 && $status1 == 1 ){ //был неправильный и стал правильным (тут добавляем балл)
	        $game_balls1++;
	        // Если ответ дан за 10 сек
	        if( $response_time < 10 ){
	            $bonus_balls1 += ((10 - $response_time)/10);
	            $count_bonus++;
	        }
	        // Если есть страйк
	        if( $bonus == 1 ){
	            $game_balls1 *= 2;
	            $bonus_balls1 *= 2;
	        }
	        $correct_answer1 = true;
	    }elseif( $game_balls1 != 0 && $status1 == 0 ){ //Был правильным стал не правильным
	        $status1 = 1;
	    }


	    if ( $game_balls2 == 0 && $status2 == 1 ){ //был неправильный и стал правильным (тут добавляем балл)
	        $game_balls2++;
	        // Если ответ дан за 10 сек
	        if( $response_time < 10 ){
	            $bonus_balls2 += ((10 - $response_time)/10);
	            $count_bonus++;
	        }
	        // Если есть страйк
	        if( $bonus == 1 ){
	            $game_balls2 *= 2;
	            $bonus_balls2 *= 2;
	        }
	        $correct_answer2 = true;
	    }elseif( $game_balls2 != 0 && $status2 == 0 ){ //Был правильным стал не правильным
	        $status2 = 1;
	    }

	    //Проверяем есть ли второй бонусный ответ что бы вернуть счет бонусу
	    if( ($response_time < 10) && ($status1 == 1 xor $status2 == 1) ){
	        if($correct_answer1){
	            $bonus_balls1 /= 2;
	        }else{
	            $bonus_balls2 /= 2;
	        }
	    }elseif( ($response_time < 10) && ($game_balls1 != 0 && $game_balls1 != 0) && ( $correct_answer1 xor $correct_answer2) ){ //Если дан второй бонусный ответ
	        if($correct_answer1){
	            $balls2 += $bonus_balls2;
	            $balls += $bonus_balls2;
	            $all_balls += $bonus_balls2;
	            $bonus_balls2 *= 2;
	        }
	        if($correct_answer2){
	            $balls1 += $bonus_balls1;
	            $balls += $bonus_balls1;
	            $all_balls += $bonus_balls1;
	            $bonus_balls1 *= 2;
	        }
	    }

	    //Сумма очков за раунд
	    $game_balls = $game_balls1 + $bonus_balls1 + $game_balls2 + $bonus_balls2;

	    // Добовляем к общему количеству если есть правильный ответ
	    if( $correct_answer1 ){
	        $balls1 += $game_balls1 + $bonus_balls1;
	        $balls += $game_balls1 + $bonus_balls1;
	        $all_balls += $game_balls1 + $bonus_balls1;
	    }
	    if( $correct_answer2 ){
	        $balls2 += $game_balls2 + $bonus_balls2;
	        $balls += $game_balls2 + $bonus_balls2;
	        $all_balls += $game_balls2 + $bonus_balls2;
	    }

	    // Пишем балы за игру
	    $wpdb->update(
	        'log_game',
	        array( 'balls1' => $balls1, 'balls2' => $balls2, 'balls' => $balls ),
	        array( 'game_id' => $game_id, 'user_id' => $user_id )
	    );

	    $wpdb->delete( 'user_balls', array('user_id' => $user_id ) );
	    $wpdb->insert(
	        'user_balls',
	        array(
	            'user_id' => $user_id,
	            'balls' => $all_balls
	        ),
	        array(
	            '%d',
	            '%f'
	        )
	    );

	    // Статусы и балы за раунд
	    $wpdb->update(
	        'log_round',
	        array( 'game_balls1' => $game_balls1, 'bonus_balls1' => $bonus_balls1, 'game_balls2' => $game_balls2, 'bonus_balls2' => $bonus_balls2, 'game_balls' => $game_balls ),
	        array( 'game_id' => $game_id, 'user_id' => $user_id, 'round' => $last_round )
	    );

	    //Фиксируем правильный ответ в log_strike
	    $wpdb->update(
	        'log_strike',
	        array( 'count_bonus' => $count_bonus ),
	        array( 'game_id' => $game_id, 'user_id' => $user_id )
	    );

	    //Пишем все вводы
	    $wpdb->insert(
	        'log_inpute',
	        array(
	            'game_id' => $game_id,
	            'user_id' => $user_id,
	            'round' => $last_round,
	            'start_time' => $table_row->start_time,
	            'resp_time' => $table_row->resp_time,
	            'artist' => $playlist[0][0],
	            'song' => $playlist[0][1],
	            'input' => $input,
	            'proc_artist' => $proc_artist,
	            'proc_song' => $proc_song
	        ),
	        array(
	            '%d',
	            '%d',
	            '%d',
	            '%d',
	            '%d',
	            '%s',
	            '%s',
	            '%s',
	            '%d',
	            '%d'
	        )
	    );

	    $sendresp['all_balls'] = $all_balls;
	    $sendresp['balls1'] = $balls1;
	    $sendresp['balls2'] = $balls2;

	endif;

	echo json_encode( $sendresp );

    die; // даём понять, что обработчик закончил выполнение
}

add_action( 'wp_ajax_round_start', 'function_round_start' ); // wp_ajax_{ЗНАЧЕНИЕ ПАРАМЕТРА ACTION!!}
add_action( 'wp_ajax_nopriv_round_start', 'function_round_start' );  // wp_ajax_nopriv_{ЗНАЧЕНИЕ ACTION!!}
// первый хук для авторизованных, второй для не авторизованных пользователей

//round start
function function_round_start(){

	global $wpdb;
	$resp = array();

	$user_id = get_current_user_id();
	$game_id = $_POST['game_id'];

	// Create the next round
	$last_round = $wpdb->get_var("SELECT MAX(round) FROM log_round WHERE user_id = $user_id AND game_id = $game_id");

	$last_round++;
	$wpdb->insert(
	    'log_round',
	    array(
	        'round' => $last_round,
	        'game_balls1' => 0,
	        'bonus_balls1' => 0,
	        'game_balls2' => 0,
	        'bonus_balls2' => 0,
	        'game_balls' => 0,
	        'game_id' => $game_id,
	        'user_id' => $user_id
	    ),
	    array( '%d', '%f', '%f', '%f', '%f', '%f', '%d', '%d' )
	);

	// Manipulation for bonuses on strike
	$result = $wpdb->get_row("SELECT * FROM log_strike WHERE user_id = $user_id AND game_id = $game_id", ARRAY_A );
	$count = $result['count']; // Количество прошедших вопросов
	$count_bonus = $result['count_bonus']; // Количество бонусных ответов ( $response_time < 10 )
	$bonus = 0;

	if( $count <= 8 && $count_bonus >=6 ){
	    $bonus = 1;
	    // Bonus is received, we begin anew
	    $count = 0;
	    $count_bonus = 0;
	}
	$count += 2;
	if ($count > 8 ){
	    $count = 2;
	    $count_bonus = 0;
	}

	$resp['bonus'] = $bonus;

	$wpdb->update(
	    'log_strike',
	    array( 'count' => $count, 'count_bonus' => $count_bonus ),
	    array( 'game_id' => $game_id, 'user_id' => $user_id )
	);

	$wpdb->update(
	    'log_bonus',
	    array( 'bonus' => $bonus ),
	    array( 'game_id' => $game_id, 'user_id' => $user_id )
	);

	// Write time
	$start_time = $_SERVER['REQUEST_TIME']; //time start round
	global $wpdb;
	$wpdb->delete(
	    'log_time',
	    array( 'game_id' => $game_id, 'user_id' => $user_id  )
	);
	$wpdb->insert(
	    'log_time',
	    array(
	        'user_id' => $user_id,
	        'game_id' => $game_id,
	        'start_time' =>$start_time,
	        'resp_time' =>'',
	    ),
	    array( '%d', '%d', '%d', '%d' )
	);

    echo json_encode( $resp );

    die; // даём понять, что обработчик закончил выполнение
}

add_action( 'wp_ajax_round_end', 'function_round_end' ); // wp_ajax_{ЗНАЧЕНИЕ ПАРАМЕТРА ACTION!!}
add_action( 'wp_ajax_nopriv_round_end', 'function_round_end' );  // wp_ajax_nopriv_{ЗНАЧЕНИЕ ACTION!!}
// первый хук для авторизованных, второй для не авторизованных пользователей

//round end
function function_round_end(){

	global $wpdb;
	$resp = array();

	$user_id = get_current_user_id();
	$game_id = $_POST['game_id'];

	// Create the next round
	$ntrack = $wpdb->get_var("SELECT MAX(round) FROM log_round WHERE user_id = $user_id AND game_id = $game_id");

	// Show the names of the composition
	$artist_arr = $wpdb->get_results(("SELECT artist FROM game_playlist WHERE tracknum='$ntrack'"), ARRAY_N);
	$song_arr = $wpdb->get_results(("SELECT song FROM game_playlist WHERE tracknum='$ntrack'"), ARRAY_N);

	$artist = $artist_arr[0];
	$song = $song_arr[0];
	$resp['artist'] = $artist;
	$resp['song'] = $song;

    echo json_encode( $resp );

    die; // даём понять, что обработчик закончил выполнение
}
