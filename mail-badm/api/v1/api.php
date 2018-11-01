<?php

use vendor\ErrorHandler;
use app\API;


require '../../vendor/autoload.php';

new ErrorHandler();
$api = new Api();
$result = $api->dispatch();

