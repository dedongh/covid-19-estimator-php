<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//Make sure that it is a POST request.
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
    throw new Exception('Request method must be POST!');
}

//Make sure that the content type of the POST request has been set to application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if (strcasecmp($contentType, 'application/json') != 0) {
    throw new Exception('Content type must be: application/json');
}

//Receive the RAW post data.
$content = trim(file_get_contents("php://input"));

//Attempt to decode the incoming RAW post data from JSON.
$decoded = json_decode($content, true);

//If json_decode failed, the JSON is invalid.
if (!is_array($decoded)) {
    throw new Exception('Received content contained invalid JSON!');
}

//$data = json_decode(file_get_contents('php://input'));

$responseData = array();
$responseImpact = array();
$responseSevereImpact = array();
if (!empty($decoded)) {

    // set response code - 200 OK
    http_response_code(200);

    $name = $decoded["region"]["name"];
    $avgAge = $decoded["region"]["avgAge"];
    $avgDailyIncomeInUSD = $decoded["region"]["avgDailyIncomeInUSD"];
    $avgDailyIncomePopulation = $decoded["region"]["avgDailyIncomePopulation"];
    $periodType = $decoded["periodType"];
    $timeToElapse = $decoded["timeToElapse"];
    $reportedCases = $decoded["reportedCases"];
    $population = $decoded["population"];
    $totalHospitalBeds = $decoded["totalHospitalBeds"];

    $currentlyInfected = $reportedCases * 10;
    $severeCurrentlyInfected = $reportedCases * 50;

    $impactInfectionsByRequestedTime = $currentlyInfected * 512;
    $severeInfectionsByRequestedTime = $severeCurrentlyInfected * 512;

    $responseImpact = array(
        "currentlyInfected" => $currentlyInfected,
        "infectionsByRequestedTime" =>$impactInfectionsByRequestedTime
    );

    $responseSevereImpact = array(
        "currentlyInfected" => $severeCurrentlyInfected,
        "infectionsByRequestedTime" =>$severeInfectionsByRequestedTime
    );


    echo json_encode(array(
        "data" => $decoded,
        "impact" => $responseImpact,
        "severeImpact" => $responseSevereImpact
    ));

} else {


    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Data should not be empty"));
    /*echo json_encode(array
    (
        "data" => $responseData,
        "impact" => $responseImpact,
        "severeImpact" => $responseSevereImpact
    ));*/
}