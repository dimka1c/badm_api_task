<?php

namespace app;

use vendor\DB;

class Task
{
    /**
     * /name task
     * @var string
     */
    public $task;

    /**
     * Params for task
     * @var array
     */
    public $params;

    /**
     * @var PDO
     */
    protected $db;

    public function __construct($task, $params)
    {
        $this->task = $task;
        $this->params = $params;
        $this->db = DB::instance();
    }


    /**
     * Translates a number into a string based on the language
     * @param $summ float
     * @param $lang string (ua,ru)
     * @return string if all ok or false, where have problem
     */
    public function get_price()
    {
        if (empty($this->params['summ']) || empty($this->params['lang'])) {
            return false;
        }
        if (!is_numeric($this->params['summ'])) return false;
        $this->params['lang'] = strtoupper($this->params['lang']);
        $arr = ['UA', 'RU'];
        $key = array_search( $this->params['lang'], $arr);
        if (!is_numeric($key)) return false;
        $post = [
            'roubles' => $this->params['summ'],
            'lang' => $key,    //0 - UA , 1-RU
            'q' => 'assets/snippets/sumprop/sumprop.php',
        ];        $ch = curl_init("http://e-kao.ru/index-ajax.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        preg_match('@<textarea.*?>(.+?)<\/textarea>@is', $response, $matches);
        if (!empty($matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * On the input it receives an integer, to the output: the value of the Fibonacci
     * number for the transmitted parameter
     * @param $n integer
     * @return integer
     */
    function fibonacci() {
        if(empty($this->params['number'])) return false;
        if(!is_numeric($this->params['number'])) return false;
        $n = $this->params['number'];
        $sq5 = sqrt(5);
        $a = (1 + $sq5) / 2;
        $b = (1 - $sq5) / 2;
        $result = (pow($a, $n) - pow($b, $n)) / $sq5;
        if (is_infinite ($result)) {
            return 'infinity';
        }
        return $result;
    }

    public function task_start($user)
    {
        try {
            $task_name = $this->task;
            $task_user = $user->user->id;
            $task_start = date('Y-m-d h:m:s');
            $task_status = 'pending';   // поставили на выполнение
            $sth = $this->db->pdo->prepare('
            INSERT INTO badm_task (task_name, task_user, task_start, task_status) 
            VALUES (:task_name, :task_user, :task_start, :task_status)
         ');
            $result = $sth->execute([
                'task_name' => $task_name,
                'task_user' => $task_user,
                'task_start' => $task_start,
                'task_status' => $task_status,
            ]);
            if ($result) {
                return $this->db->pdo->lastInsertId();
            }
        } catch (\PDOException $e) {
            throw new \Exception($e->errorInfo[2], 500);
        }
    }


    public function task_end($id, $result)
    {
        try {
            $result = json_encode($result);
            $sth = $this->db->pdo->prepare("
          UPDATE badm_task SET task_finish = :task_finish, task_status = :task_status, task_result = :result WHERE id = :id");
            $task_finish = date('Y-m-d h:m:s');
            $task_status = 'done';
            $sth->bindValue(':task_finish', $task_finish, \PDO::PARAM_STR);
            $sth->bindValue(':task_status', $task_status, \PDO::PARAM_INT);
            $sth->bindValue(':id', $id, \PDO::PARAM_INT);
            $sth->bindValue(':result', $result, \PDO::PARAM_STR);
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->errorInfo[2], 500);
        }
    }

    public function getTask()
    {
        try {
            $sth = $this->db->pdo->prepare('
            SELECT task_status, task_result 
            FROM badm_task 
            WHERE id = :id');
            $sth->bindValue(':id', $this->params, \PDO::PARAM_INT);
            $sth->execute();
            return $sth->fetchAll()[0];

        } catch (\PDOException $e) {
            throw new \Exception($e->errorInfo[2], 500);
        }
    }
}