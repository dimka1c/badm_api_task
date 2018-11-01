<?php

namespace vendor;


class Response
{
    public static function response($message, $code, $jwt)
    {
        if (!empty($jwt)) {
            $headers[] = "Authorization: " . $jwt;
        }
        $headers[] = "Access-Control-Allow-Orgin: *";
        $headers[] = "Access-Control-Allow-Methods: *";
        $headers[] = "Content-Type: application/json";
        switch ($code) {
            case 200:       // OK
                $headers[] = "HTTP/1.1 200 OK";
                break;
            case 401:       // Ошибка авторизации
                $headers[] = "HTTP/1.1 401 Unauthorized";
                break;
            case 403:       // Ошибка доступа
                $headers[] = "HTTP/1.1 403 Forbidden";
                break;
            case 422:       // Ошибка валидации данных
                $headers[] = "HTTP/1.1 422 Unprocessable Entity";
                break;
            case 500:       //ошибка сервера
                $headers[] = "HTTP/1.1 500 Server Error";
                break;
        }
        //$response = ['error' => $message];

        foreach ($headers as $header) {
            header($header);
        }
        echo json_encode($message);
    }
}