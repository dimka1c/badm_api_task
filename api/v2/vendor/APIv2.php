<?php

namespace api\v2\vendor;


use api\v2\apiInterface\api_v2_Interface;
use api\v2\apiInterface\userInterface;

abstract class APIv2 implements api_v2_Interface, userInterface
{
    /**
     * Request method
     * @var string
     */
    protected $requestMethod;

    /**
     * Avtion API
     * @var string
     */
    protected $action;

    /**
     * Parameters action
     * @var array
     */

    protected $paramAction;
    /**
     * @return mixed
     */

    public function dispatchAPI()
    {
        // TODO: Implement dispatchAPI() method.
    }

    /**
     * @param $method
     * @param $user
     * @return mixed
     */
    public function accessAPI($method, $user)
    {
        // TODO: Implement accessAPI() method.
    }

    /**
     * @param $method
     * @param array $param
     * @return mixed
     */
    public function runAPI($method, array $param)
    {
        // TODO: Implement runAPI() method.
    }

    /**
     * @param array $headers
     * @return mixed
     */
    public function requestHeadersAPI(array $headers)
    {
        // TODO: Implement requestHeadersAPI() method.
    }

    /**
     * @param $response
     * @param $type
     * @return mixed
     */
    public function responseAPI($response, $type)
    {
        // TODO: Implement responseAPI() method.
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        // TODO: Implement getUser() method.
    }

    /**
     * @return mixed
     */
    public function generateJwtToken()
    {
        // TODO: Implement generateJwtToken() method.
    }

    /**
     * @return mixed
     */
    public function refreshJwtToken()
    {
        // TODO: Implement refreshJwtToken() method.
    }
}