<?php

namespace vendor;


use app\Task;

abstract class _API
{
    /**
     * User details
     * @var array
     */
    protected $user;

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
    abstract public function dispatch();


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
        $this->user = $auth->decodeJwt($this->jwt);
        $this->jwt = $auth->generateJWT($this->user->user);
        if (!is_array($method)) {
            $method = (array) $method;
        }
        $method = array_map('strtoupper', $method);
        if (!in_array($_SERVER['REQUEST_METHOD'], $method)) {
            throw new \Exception('request method error', 422);
        }
        if ($this->user->user->$role != 1) {
            throw new \Exception('Access is denied', 403);
        }
        if ($param) {
            if (!isset($this->args[1])) {   // если  нет id user's - отправляем ошибку
                throw new \Exception('no customer id', 422);
            }
        }
        return true;
    }

    /**
     * @return mixed
     */
    abstract public function executeTask();

    /**
     * Run task
     * @return mixed
     * @throws \Exception
     */
    public function runTask()
    {
        if ($this->access(['post'], 'role_task')) {
            $requestTask = $_POST['task'];
            $requestParams = json_decode($_POST['params'], true);
            if (!empty($requestTask) && !empty($requestParams)) {
                $task = new Task($requestTask, $requestParams);
                if (!method_exists($task, $requestTask)) {
                    throw new \Exception('method not found', 422);
                }
                try {
                    $idTask = $task->task_start($this->user);
                    $resultTask = $task->$requestTask();
                    if ($resultTask) {
                        $task->task_end($idTask, $resultTask);
                    }
                    return ['result' => $resultTask, 'task_id' => $idTask];
                } catch (\Exception $e) {
                    throw new \Exception('task execution error', 500);
                }
            } else {
                $message = empty($requestTask) ? 'task no defined' : 'task parameters not defined';
                throw new \Exception($message, 422);
            }
        }
    }

    public function getTask()
    {
        if ($this->access(['get'], 'role_task', true))
        {
            $idTask = $this->args[1];
            $task = new Task($this->method, $idTask);
            $result = $task->getTask();
            if (is_null($result)) {
                throw new \Exception('task not found', 422);
            }
            return ['status' => $result['task_status'], 'result' => $result['task_result']];
        }
    }
}