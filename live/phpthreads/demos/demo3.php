<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
require_once '../lib/Threads.php';

for($i = 1; $i <= 20; $i++){
	$Thread->Create(function() use ($i){
		sleep(1);
		echo $i;
		echo '<br />';
	});
}

$start = microtime(true);
$response = $Thread->Run();
$end = microtime(true);

echo '<hr /> OK ';
echo count($response);
echo ' from 20';

echo "<hr /> Script execution time: ".($end-$start)." sec.";
