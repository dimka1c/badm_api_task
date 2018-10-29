<?php

use vendor\Api;
use vendor\ErrorHandler;

require '../../vendor/autoload.php';


new ErrorHandler();
$api = new Api();
$result = $api->dispatch();
//echo $result;
