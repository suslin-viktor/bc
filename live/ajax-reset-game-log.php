<?php
/*
* Template Name: Ajax Reset game_log
*/
?>

<?php

global $wpdb;
$resp = array();

$user_id = get_current_user_id();
$game_id = $_POST['game_id'];

// Содаем следуцющий раунд
$last_round = $wpdb->get_var("SELECT MAX(round) FROM log_round WHERE user_id = $user_id AND game_id = $game_id");
/*
if(!$last_round){
    $last_round = 0;
}
*/
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


$ntrack = $last_round - 1;
if( $ntrack != 0 ){
    // Выводим названия композиции
    $artist_arr = $wpdb->get_results(("SELECT artist FROM game_playlist WHERE tracknum='$ntrack'"), ARRAY_N);
    $song_arr = $wpdb->get_results(("SELECT song FROM game_playlist WHERE tracknum='$ntrack'"), ARRAY_N);

    $artist = $artist_arr[0];
    $song = $song_arr[0];
    $resp['artist'] = $artist;
    $resp['song'] = $song;
}

// Монипуляции для бонусов по страйку
$result = $wpdb->get_row("SELECT * FROM log_strike WHERE user_id = $user_id AND game_id = $game_id", ARRAY_A );
$count = $result['count']; // Количество прошедших вопросов
$count_bonus = $result['count_bonus']; // Количество бонусных ответов ( $response_time < 10 )
$bonus = 0;

if( $count <= 8 && $count_bonus >=6 ){
    $bonus = 1;
    // Бонус заработан, зарабатываем дальше
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

//Пишем время
$start_time = $_SERVER['REQUEST_TIME']; //время запуска раунда
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

//для вывода страйка временно
echo '('.json_encode($resp).')';

?>