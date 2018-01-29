<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
require_once '../lib/Threads.php';

$_SESSION['test'] = 'TEST';
$test = 'Test variable';

$Thread->Create(function(){
	sleep(1);
	echo 'Sleep 1<br/>';
	return 'This threads don`t pring anything';
});

$Thread->Create(function(){
	sleep(5);
	echo 'Sleep 5<br/>';
});

$Thread->Create(function(){
	sleep(3);
	echo 'Sleep 3<br/>';
});

$Thread->Create(function(){
	sleep(6);
	echo 'Sleep 6<br/>';
});

$Thread->Create(function(){
	sleep(6);
	echo 'Sleep 6<br/>';
});

$Thread->Create(function(){
	sleep(6);
	echo 'Sleep 6<br/>';
});

$Thread->Create('string'); //not valid, notice
$resp = $Thread->Run(false); //run without printing on the screen
print_r($resp);

$Thread->Create(function(){
	echo 'Another thread!<br/>';
});

$Thread->Create(function() use($_SESSION){ //use session
	echo '<br/>And more!<br/>';
	echo 'Get something from session: '.$_SESSION['test'].'<br/>';
});

$Thread->Create(function() use($test){ //use variables
 echo $test;
});

$Thread->Run(); //run with printing on the screen, return also supported
