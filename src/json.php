<?php
header("Content-Type: application/json; charset=UTF-8");
include "estimator.php";

print print_json($decoded);

function print_json($data)
{

    $json_array = covid19ImpactEstimator($data);

    return json_encode($json_array);
}
$time2 = microtime(true);

$exe_time = ($time2- $_SERVER["REQUEST_TIME_FLOAT"])* 1000;
$logMessage = $_SERVER['REQUEST_METHOD']. "\t\t".$_SERVER['REQUEST_URI']. "\t\t".http_response_code()."\t\t". round($exe_time,2)."ms";
file_put_contents('logs.txt', $logMessage."\n", FILE_APPEND | LOCK_EX);
