<?php

namespace vendor;


use app\User;
use Firebase\JWT\JWT;

class Auth
{

    /**
     * secret key
     * @var string
     */
    private $secret;

    /**
     * Algorithm used to encode
     * @var string
     */
    private $encodeAlg;

    /**
     * Expiration time token
     * @var integer
     */
    private $expiration;

    public function __construct()
    {
        $config = require $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        $this->secret = $config['secret'];
        $this->expiration = $config['expiration'];
        $this->encodeAlg = $config['alg'];
    }

    /**
     * Generate JWT-token
     *
     * @param $user
     * @param $expiration
     * @return string
     */
    public function generateJWT($user)
    {
        $data = [
            'iat' => time(),
            'exp' => time() + $this->expiration,
            'user' => $user,
        ];

        return JWT::encode(
            $data,
            $this->secret,
            $this->encodeAlg
        );
    }

    /*
     * Authorization
     * generate JWT-token and write token to table auth
     * return false in error or return token if no errors
     * @var $login string
     * @var $password string
     */
    public function auth($login, $password)
    {
        $modelUser = new User();
        $user = $modelUser->getUser($login, $password);
        if ($user) {
            $expiration = time() + $this->expiration;
            $token = $this->generateJWT($user);
            if ($modelUser->setToken($token, $expiration)) {
                return $token;
            }
        }
        return false;
    }

    public function decodeJwt($jwt)
    {
        try {
            $decode = JWT::decode($jwt, $this->secret, array($this->encodeAlg));
            return $decode;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 401);
        }
    }
}