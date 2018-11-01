<?php
/**
 * Created by PhpStorm.
 * User: dimka1c
 * Date: 30.10.2018
 * Time: 19:16
 */

namespace vendor;


use vendor\apiInterface\apiInterface;

abstract class API implements apiInterface
{

    /**
     * Request method (POST, GET, PUT, DELETE, PATCH, etc)
     * @var string
     */
    protected $requestMthod;


    public function dispatchAPI()
    {
    }

    public function accessAPI($method, $user)
    {
    }

    function runAPI($method, array $param)
    {
    }

    public function requestHeadersAPI(array $headers)
    {
    }

    public function responseAPI($response, $type)
    {
    }

}