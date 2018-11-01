<?php
/**
 * Created by PhpStorm.
 * User: dimka1c
 * Date: 30.10.2018
 * Time: 19:23
 */

namespace api\v2\apiInterface;


interface api_v2_Interface
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