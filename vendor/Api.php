<?php

namespace vendor;

use app\Task;
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
     * JWT Token
     * if false - token missing
     * @var string
     */
    protected $jwt = false;

    /**
     * Response headers
     * @var array
     */
    protected $headers = [];

    /**
     * Response
     * @var array
     */
    protected $response = [];


    public function __construct()
    {
        $request = $_REQUEST['request'];
        $this->args = explode('/', rtrim(strtolower($request), '/'));
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $headers = getallheaders();
        $this->jwt = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    }

    /**
     * Run api
     * @throws \Exception
     */
    public function dispatch()
    {
        switch ($this->args[0]) {
            case 'login':
                $this->login();
                break;
            case 'user':    //api/user/{id}
                switch ($this->method) {
                    case 'GET':
                        $this->getUserInfo();
                        break;
                    case 'PATCH':
                        $this->UpdateUserInfo();
                        break;
                    case 'POST':
                        $this->AddUser();
                        break;
                }
                break;
            case 'task':
                $this->runTask();
                break;
        }
    }

    /**
     * Method login
     * /api/login
     * @return bool
     */
    protected function login()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            throw new \Exception('invalid query', 422);
        }
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $modelAuth = new Auth();
            $this->jwt = $modelAuth->auth($_POST['email'], $_POST['password']);
            if ($this->jwt == false) {
                throw new \Exception('error autorization', 401);
            }
            Response::response(['token' => $this->jwt], 200, $this->jwt);
            return true;
        }
        throw new \Exception('error autorization', 401);
    }

    /**
     * Method Get user info
     * /api/user/{id}
     * Request method - GET
     * @return bool
     */
    protected function getUserInfo()
    {
        if ($this->access('GET', 'role_read', true)) {
            $modelUser = new User();
            $dataUser = $modelUser->getUserInfo($this->args[1]);
            if ($dataUser != false) {
                Response::response([
                    'email' => $dataUser['email'],
                    'name' => $dataUser['name'],
                    'phone' => $dataUser['phone']],
                    200,
                    $this->jwt);
            } else {
                throw new \Exception('user not found', 422);
            }
        }
        return;
    }

    /**
     * Update User Info
     * /api/user/{id}
     * Request_method - PATCH
     */
    public function updateUserInfo()
    {
        $patch = json_decode(file_get_contents('php://input', 'r'), true);
        if ($this->access('patch', 'role_update', true)) {
            $modelUser = new User();
            $dataUser = $modelUser->updateUserInfo($this->args[1], $patch);
            if ($dataUser != false) {
                Response::response($dataUser, 200, $this->jwt);
            } else {
                throw new \Exception('user not found', 422);
            }
        }
        return;
    }

    /**
     * Add User
     * /api/user/{id}
     * Request_method - POST
     */
    public function AddUser()
    {
        if ($this->access('post', 'role_write')) {
            $modelUser = new User();
            $dataUser = $modelUser->AddUser($_POST);
            if ($dataUser != false) {
                Response::response($dataUser,200, $this->jwt);
            } else {
                throw new \Exception('invalid query', 422);
            }
        }
        return;
    }

    /**
     * Сheck access to action
     *
     * @param $method - request method (get, post, patch, etc...)
     * @param $role - role user
     * @param $param - check request parameters
     * @return bool
     * @throws \Exception
     */
    protected function access($method, $role, $param = false)
    {
        if (!$this->jwt) {
            throw new \Exception('Access is denied', 403);
        }
        $auth = new Auth();
        $user = $auth->decodeJwt($this->jwt);
        // сразу его обновим до времени жизни не зависимо от того, есть ли право на выполнение операции
        //$auth->setExpiration($user->user->id);
        $this->jwt = $auth->generateJWT($user->user);
        if (!is_array($method)) {
            $method = (array) $method;
        }
        $method = array_map('strtoupper', $method);
        if (!in_array($_SERVER['REQUEST_METHOD'], $method)) {
            throw new \Exception('request method error', 422);
        }
        /*
        if ($_SERVER['REQUEST_METHOD'] != strtoupper($method)) {
            throw new \Exception('request method error', 422);
        }
        */
        // проверим на разрешение выполнения операции
        if ($user->user->$role != 1) {
            throw new \Exception('Access is denied', 403);
        }
        // проверка на наличие аргументов
        if ($param) {
            if (!isset($this->args[1])) {   // если  нет id user's - отправляем ошибку
                throw new \Exception('no customer id', 422);
            }
        }
        return true;
    }

    public function runTask()
    {
        if ($this->access(['post', 'get'], 'role_task')) {
            $requestTask = $_POST['task'];
            $requestParams = json_decode($_POST['params'], true);
            if (!empty($requestTask) && !empty($requestParams)) {
                $task = new Task($requestTask, $requestParams);
                if (!method_exists($task, $requestTask)) {
                    throw new \Exception('method not found', 422);
                }
                try {
                    $resultTask = $task->$requestTask();
                    if ($resultTask == false) {
                        throw new \Exception();
                    }
                    Response::response($resultTask, 200, $this->jwt);
                } catch (\Exception $e) {
                    throw new \Exception('task execution error', 500);
                }

            } else {
                $message = empty($requestTask) ? 'task no defined' : 'task parameters not defined';
                throw new \Exception($message, 422);
            }
        }
    }
}