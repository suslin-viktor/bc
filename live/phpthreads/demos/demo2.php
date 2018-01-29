<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
require_once '../lib/Threads.php';

for($i = 1; $i <= 100; $i++){
	$Thread->Create(function() use ($i){
		echo $i;
		echo '<br />';
	});
}

$response = $Thread->Run();
echo '<hr /> OK ';
echo count($response);
echo ' from 100';
