<?php

namespace vendor;


class ErrorHandler
{
    public function __construct()
    {
        set_error_handler([$this, 'errorHandler']);
        ob_start();
        register_shutdown_function([$this, 'fatalErrorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
    }

    public function errorHandler()
    {
        return true;
    }

    public function exceptionHandler(\Exception $e)
    {
        Response::response(['error' => $e->getMessage()], $e->getCode(), '');
    }

    public function fatalErrorHandler()
    {
        $error = error_get_last();
        ob_end_flush();
    }
}