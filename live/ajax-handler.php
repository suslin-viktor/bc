<?php
/*
* Template Name: Ajax Handler
*/
?>

<?php

//создаём выходной массив
$resp = array();
$resp['time'] = $_SERVER['REQUEST_TIME']; //время первого захода пользователя на страницу page-inner.php ( то есть в момент запуска игры)

global $wpdb;
$wpdb->delete(
	'game_temp',
	array( 'user_id' => get_current_user_id() )
);
$wpdb->insert(
	'game_temp',
	array(
		'user_id' => get_current_user_id(),
		'start_time' => $resp['time'],
		'resp_time' =>'',
	),
	array( '%d', '%d', '%d' )
);

//подаём результат в js в виде массива, а он уже решает в какой html блок что выводить
echo '(' . json_encode($resp).')';

?>