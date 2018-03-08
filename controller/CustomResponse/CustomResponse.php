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
    private $result;

    function __construct($pattern, $result)
    {
        $this->pattern = $pattern;
        $this->result = $result;
    }

    function redirect($to){

    }

    function tokenSession(){
        return md5(session_id());
    }
}