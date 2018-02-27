<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 24/02/18
 * Time: 11:28
 */

class CustomResponse
{
    private $pattern;

    function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    function tokenSession(){
        return md5(session_id());
    }
}