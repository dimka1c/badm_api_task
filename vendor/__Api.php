<?php

namespace vendor;

use app\User;

class Api
{

    /**
     * Request method (Ppost, get, update, patch... etc)
     * @var string
     */
    protected $method = '';

    /**
     * Arguments from request
     * @var array
     */
    protected $args = [];

    /**
     * token
     * @var string
     */
    protected $token = '';

    /**
     * Client Token from Header Autorization
     * @var string
     */
    protected $clientToken = '';

    protected $headers = [];

    public function __construct()
    {
        $request = $_REQUEST['request'];
        $headers = getallheaders();
        $this->clientToken = isset($headers['Authorization']) ? $headers['Authorization'] : '';

/*        $this->setHeader("Access-Control-Allow-Orgin: *");
        $this->setHeader("Access-Control-Allow-Methods: *");
        $this->setHeader("Content-Type: application/json");
*/
        $this->args = explode('/', rtrim(strtolower($request), '/'));
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
    }


    public function dispatch()
    {
        switch ($this->args[0]) {
            case 'login':

                break;
            case 'user':
                break;
            case 'UpdateUserInfo':
                break;
            case 'AddUser':
                break;
            case 'GetTaskStatus':
                break;
        }
        $this->headersToSent();
    }

    public function APIrun()
    {
        if (empty($this->method) || empty($this->args)) {
            $this->setHeader("HTTP/1.1 422  Unprocessable Entity");
            return $this->headers;
        }
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $this->clientToken = $headers['Authorization'];
        }
        switch ($this->args[0]) {
            case 'login':
                $this->token = $this->login();
                if ($this->token != false) {
                    $this->setHeader("Authorization: " . $this->token);
                }
                break;
            case 'user':
                break;
            case 'UpdateUserInfo':
                break;
            case 'AddUser':
                break;
            case 'GetTaskStatus':
                break;
        }
        $this->headersToSent();
    }


    public function login()
    {
        //echo password_hash("123", PASSWORD_DEFAULT); die;
        if (!empty($_POST['email'] && !empty($_POST['password'])) && $this->method == 'POST') {
            $user = new User();
            if ($user == false) {
                $this->setHeader("HTTP/1.1 401 Unauthorized");
                return false;
            }
            return $user->login();
        } else {
            $this->setHeader("HTTP/1.1 401 Unauthorized");
            throw new \Exception('Error API registration');
        }
    }

    public function getToken()
    {
        return $this->token;
    }

    protected function setHeader($header)
    {
        $this->headers[] = $header;
    }

    public function getResponseHeader()
    {
        return $this->headers;
    }

    private function headersToSent()
    {
        foreach ($this->headers as $header) {
            header($header);
        }
    }
}