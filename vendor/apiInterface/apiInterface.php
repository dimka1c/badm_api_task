<?php

namespace vendor\apiInterface;


interface apiInterface
{

    /**
     * Диспетчер запроса
     * Определяем метод запроса и метод обработки запроса
     * @return mixed
     */
    public function dispatchAPI();

    /**
     * Определяем разрешени ли доступ к вызываемому методу
     * @param $method - метод api
     * @param $user - пользователь (object)
     * @return mixed
     */
    public function accessAPI($method, $user);

    /**
     * Запускаем нужный метож
     * @return mixed
     */
    public function runAPI($method, array $param);

    /**
     * Установка нужных заголовков
     * @param array $headers
     * @return mixed
     */
    public function requestHeadersAPI(array $headers);

    /**
     * Возвращаем ответ сервера
     * @param $response - возвращаемое значение
     * @param $type - формат возвращаемого значения (json, text, html и т.д.)
     * @return mixed
     */
    public function responseAPI($response, $type);
}