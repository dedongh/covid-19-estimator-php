<?php

include "estimator.php";
header("Content-Type: text/plain");
$time2 = microtime(true);

$exe_time = ($time2- $_SERVER["REQUEST_TIME_FLOAT"])* 1000;
$logMessage = $_SERVER['REQUEST_METHOD']. "\t\t".$_SERVER['REQUEST_URI']. "\t\t".http_response_code()."\t\t". round($exe_time,2)."ms";
file_put_contents('logs.txt', $logMessage."\n", FILE_APPEND | LOCK_EX);

$outputMessage = file_get_contents('logs.txt');
echo $outputMessage;