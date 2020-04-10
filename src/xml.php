<?php
include "estimator.php";
print (print_xml($decoded));
function print_xml($data)
{
    header("Content-Type: text/plain");
    $xml_array = covid19ImpactEstimator($data);


    $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");

    array_walk_recursive($xml_array, array($xml, 'addChild'));

    return $xml->asXML();


}

$time2 = microtime(true);

$exe_time = ($time2- $_SERVER["REQUEST_TIME_FLOAT"])* 1000;
$logMessage = $_SERVER['REQUEST_METHOD']. "\t\t".$_SERVER['REQUEST_URI']. "\t\t".http_response_code()."\t\t". round($exe_time,2)."ms";
file_put_contents('logs.txt', $logMessage."\n", FILE_APPEND | LOCK_EX);