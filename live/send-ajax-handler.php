<?php
/*
* Template Name: Send Ajax Handler
*/
?>

<?php
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

    //get string
    /* переписан метод
    $wpdb->update(
        'game_temp',
        array( 'resp_time' => $stime),
        array( 'user_id' => $user_id)
    );

    $table_row = $wpdb->get_row("SELECT * FROM game_temp WHERE user_id = $user_id");
    $diff = $table_row->resp_time - $table_row->start_time;
    $ntrack = ($diff - $diff%30)/30 + 1;
    $response_time = $diff%30;
    */

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

		/*
		if (max($percent10, $percent11) > 75) {
			$sendresp['result'] = 'correct!';
		} else {
			$sendresp['result'] = 'incorrect =((';
		}
		*/

		//$sendresp['percent'] = 'La réponse est correcte à ' . max($percent10, $percent11) . '%';

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

/*
		if( $percent30) > 75 ) {
			$sendresp['song'] = 'correct'; $sendresp['status2'] = 1;
		} else {
			$sendresp['song'] = '<span class="incorrect">incorrect, try again </span>';
		}

		if( $percent31) > 75 ) {
			$sendresp['artist'] = 'correct'; $sendresp['status1'] = 1;
		} else {
			$sendresp['artist'] = '<span class="incorrect">incorrect, try again </span>';
		}
*/
		//$sendresp['percent'] = 'La réponse est correcte à ' . $percent20 . ' , ' . $percent21 . '%';
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

echo '('.json_encode($sendresp).')';
?>