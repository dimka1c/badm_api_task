<?php

namespace app;

use vendor\DB;


class User
{

    protected $user = false;

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
        $this->db = DB::instance();
    }

    /**
     * Get user data
     * @param $login string
     * @param $password string
     * @return bool
     */
    public function getUser($login, $password)
    {
        try {
            $sth = $this->db->pdo->prepare('
                select 
	                badm_user.id, badm_user.name, badm_user.email, badm_user.password,
	                access.role_read, access.role_write, access.role_delete, access.role_update, access.role_task
                from badm_user
                inner join badm_role on badm_role.id = badm_user.role
                inner join badm_access_role as access on access.role = badm_role.id
                where badm_user.email = :email');
            $sth->bindValue(':email', $login, \PDO::PARAM_STR);
            $sth->execute();
            $user = $sth->fetchAll()[0];
            if (password_verify($password, $user['password'])) {
                $this->user = $user;
                unset($user['password']);
                unset($user['email']);
                return $user;
            }
        } catch (\Exception $e) {

        }
        return false;
    }

    /**
     * Update Token in table auth
     *
     * @param $token
     * @param $expiration
     * @return bool
     */
    public function setToken($token, $expiration)
    {
        if ($this->user) {
            $sth = $this->db->pdo->prepare('SELECT id FROM badm_auth WHERE user_id = :id');
            $sth->bindValue(':id', $this->user['id'], \PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetchAll();
            if (!empty($result)) {
                $sth = $this->db->pdo->prepare("UPDATE badm_auth SET access_token = :token, expiration = :expiration WHERE user_id = :id");
                $sth->bindValue(':token', $token, \PDO::PARAM_STR);
                $sth->bindValue(':expiration', $expiration, \PDO::PARAM_INT);
                $sth->bindValue(':id', $this->user['id'], \PDO::PARAM_INT);
                $result = $sth->execute();
            } else {
                $sth = $this->db->pdo->prepare('INSERT INTO badm_auth (access_token, expiration, user_id) 
                        VALUES (:token, :expiration, :id)');
                $result = $sth->execute([
                    'token' => $token,
                    'expiration' => $expiration,
                    'id' => $this->user['id'],
                ]);
            }
            if ($result) {
                return true;
            }
        }
        return false;
    }


    public function getUserInfo($id)
    {
        try {
            $sth = $this->db->pdo->prepare('SELECT 
                                                    badm_user.name, 
                                                    badm_user.email, 
                                                    badm_user.phone,
                                                    badm_user.created_at,
                                                    badm_user.role,
                                                    role.role as role, 
                                                    role.id as role_id
                                                  FROM badm_user
                                                  INNER JOIN badm_role AS role ON role.id = badm_user.role 
                                                  WHERE badm_user.id = :id');
            $sth->bindValue(':id', $id, \PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetchAll();
            if (!empty($result)) {
                return $result[0];
            }
        } catch (\Exception $e) {
            throw new \PDOException($e->errorInfo[2], 500);
        }
        return false;
    }

    /**
     * Update data info user from table 'user'
     *
     * @param $id
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function updateUserInfo($id, $data)
    {
        try {
            // записываем данные
            $sth = $this->db->pdo->prepare("UPDATE badm_user SET 
                                                          email = :email, 
                                                          name = :name,
                                                          phone = :phone,
                                                          role = :access,
                                                          updated_at = :updated_at
                                                      WHERE id = :id");
            $sth->bindValue(':email', $data['email'], \PDO::PARAM_STR);
            $sth->bindValue(':name', $data['name'], \PDO::PARAM_STR);
            $sth->bindValue(':phone', $data['phone'], \PDO::PARAM_STR);
            $sth->bindValue(':access', $data['access'], \PDO::PARAM_INT);
            $sth->bindValue(':id', $id, \PDO::PARAM_STR);
            $updated_at = date('Y-m-d h:m:s');
            $sth->bindValue(':updated_at', $updated_at);
            $sth->execute();
            // отдаем данные из таблицы
            $sth = $this->db->pdo->prepare('SELECT badm_user.email, 
                                                        badm_user.name, 
                                                        badm_user.phone, 
                                                        badm_user.created_at,
                                                        badm_user.updated_at
                                                  FROM badm_user WHERE id = :id');
            $sth->bindValue(':id', $id, \PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetchAll();
            if (!empty($result)) {
                $updateData = [
                    'user_id' => $id,
                    'created_at' => $result[0]['created_at'],
                    'updated_at' => $result[0]['updated_at'],
                    'user' => [
                        'email' => $result[0]['email'],
                        'name' => $result[0]['name'],
                        'phone' => $result[0]['phone'],
                    ]
                ];
                return $updateData;
            }
        } catch (\Exception $e) {
            $errCode = 500;
            if ($e->getCode() == 2300) $errCode = 422;
            throw new \PDOException($e->errorInfo[2], $errCode);
        }
    }

    /**
     * Add data user to table 'user'
     * @param $user
     */
    public function AddUser($user)
    {
        try {
            $sth = $this->db->pdo->prepare('INSERT INTO badm_user (email, name, phone, role) 
                        VALUES (:email, :name, :phone, :access)');
            $result = $sth->execute([
                'email' => $_POST['email'],
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
                'access' => $_POST['access'],
            ]);
            $id = $this->db->pdo->lastInsertId();
            $newUserData = $this->getUserInfo($id);
            $newUser = [
                'user_id' => $id,
                'created_at' => $newUserData['created_at'],
                'user' => $newUserData,
            ];
            return $newUser;
        } catch (\Exception $e) {
            throw new \PDOException($e->errorInfo[2], 422);
        }
    }
}