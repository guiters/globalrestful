<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 07/03/18
 * Time: 14:46
 */

class ProcessResultClass
{

    private $result = false;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function json($options = JSON_PRETTY_PRINT)
    {
        header('Content-Type: application/json');
        return json_encode($this->result, $options);
    }

}