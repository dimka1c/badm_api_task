<?php

namespace app;

class User
{
    public $user = false;

    /**
     * @var PDO
     */
    private $db;

    /**
     * Configuration
     */
    private $config;

    public function __construct()
    {
        $this->config = require $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        try {
            $this->db = \vendor\DB::instance();
            $sth = $this->db->pdo->prepare('SELECT id, name, password, token, time_token, role 
                                                     FROM user 
                                                     WHERE email = :email');
            $sth->bindValue(':email', $_POST['email'], \PDO::PARAM_STR);
            $sth->execute();
            $user = $sth->fetchAll()[0];
            if (password_verify($_POST['password'], $user['password'])) {
                unset($user['password']);
                $this->user = $user;
            }
        } catch (\Exception $e) {

        }
    }

    /*
     * записываем/update токен и время действия токена в таблицу user
     */
    public function login()
    {
        try {

            $str = $this->user['name']. $this->user['id'] . strval(time());
            $token = password_hash($str, PASSWORD_DEFAULT);
            $time_token = time() + $this->config['tokenExp'];
            $sth = $this->db->pdo->prepare("UPDATE user SET token = :token, time_token = :time_token WHERE id = :id");
            $sth->bindValue(':token', $token, \PDO::PARAM_STR);
            $sth->bindValue(':time_token', $time_token, \PDO::PARAM_INT);
            $sth->bindValue(':id', $this->user['id'], \PDO::PARAM_INT);
            $result = $sth->execute();
            return $token;
        } catch (\Exception $e) {

        }
    }
}