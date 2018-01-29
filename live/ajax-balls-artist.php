<?php
/*
* Template Name: Ajax Balls Artist
*/
?>

<?php

global $wpdb;

$game_id = $_POST['game_id'];

$html = "";

//Накидываем в json статистику по игре
$results_game = $wpdb->get_results("SELECT * FROM `log_round` WHERE game_id = $game_id AND round IN (SELECT MAX(`round`) FROM `log_round` WHERE game_id = $game_id) GROUP BY `game_balls1` AND `bonus_balls1` DESC", ARRAY_A );

foreach( $results_game as $result_game){

    $user_info = get_userdata($result_game['user_id']);
    $html .= "<li>";
    $html .= $user_info->user_login;
    $html .= "<span class='result'>";
    $html .= $result_game['game_balls1'] + $result_game['bonus_balls1'];
    $html .= "</span></li>";

}

echo $html;

?>