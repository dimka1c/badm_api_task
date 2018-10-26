<?php

require '../../vendor/autoload.php';


$api = new \vendor\Api($_REQUEST['request']);

$result = $api->APIrun();

//header("HTTP/1.1 401 Unauthorized");
echo json_encode(['token' => $api->getToken()]);