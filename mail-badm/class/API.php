<?php

namespace app;

use vendor\_API;
use vendor\Auth;
use vendor\Response;

class API extends _API
{

    public function __construct()
    {
        parent::__construct();
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
                switch ($this->method) {
                    case 'POST':
                        $this->executeTask();
                        break;
                    case 'GET':
                        $this->getTaskID();
                        break;
                }
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
     * Execute Task
     * @throws \Exception
     */
    public function executeTask()
    {
        $resultTask = $this->runTask();
        if ($resultTask == false) {
            throw new \Exception('error execute task', 500);
        }
        Response::response($resultTask, 200, $this->jwt);
    }

    public function getTaskID()
    {
        $resultTask = $this->getTask();
        if ($resultTask == false) {
            throw new \Exception('error getting task status', 500);
        }
        Response::response($resultTask, 200, $this->jwt);
    }
}