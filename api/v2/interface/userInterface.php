<?php
/**
 * Created by PhpStorm.
 * User: dimka1c
 * Date: 30.10.2018
 * Time: 19:58
 */

namespace api\v2\apiInterface;


interface userInterface
{

    /**
     * Get user info
     * @return mixed
     */
    public function getUser();

    /**
     * Generate JWT Token
     * @return mixed
     */
    public function generateJwtToken();

    /**
     * Regenerate JWT Token
     * @return mixed
     */
    public function refreshJwtToken();

}