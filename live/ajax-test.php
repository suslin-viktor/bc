<?php
/*
 * Template Name: Ajax test
 */
?>

<?php

$testresp = array();

$testresp['result'] = $_POST['testname'];

echo '(' . json_encode($testresp).')';
?>