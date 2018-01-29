<?php
/*
* Template Name: Ajax Rating Game
*/
?>

<?php

global $wpdb;
$game_id = $_POST['game_id'];

$html = "<tr class=\"heading-table-list\">";
$html .= "<th style=\"min-width: 130px;\">Nom</th>";
$html .= "<th>Score</th>";
$html .= "<th>Mise</th>";
$html .= "</tr>";

//Накидываем в json статистику по игре
$results_game = $wpdb->get_results("SELECT * FROM `log_game` WHERE game_id = $game_id ORDER BY balls DESC", ARRAY_A );

$i=1;
foreach( $results_game as $result_game){

    $user_info = get_userdata($result_game['user_id']);
    $html .= "<tr><td>";
    $html .= $user_info->user_login;
    $html .= "</td><td>";
    $html .= $result_game['balls'];
    $html .= "</td><td>";
    $html .= $i;
    $html .= "</td></tr>";

}

echo $html;

?>