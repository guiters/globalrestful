<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 03/04/18
 * Time: 11:15
 */

class basiclogin
{
    private $token;
    function __construct($token)
    {
        $this->token = $token;
    }

    function TokenCreate()
    {
        $var = [
            'session' => session_id(),
            'time' => date('d/m/Y'),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'client' => $_SERVER['HTTP_USER_AGENT']
        ];
        file_put_contents('auth/' . session_id() . '.json', json_encode($var));
        return session_id();
    }

    function TokenValidate()
    {
        $token = $this->token;
        $res = false;
        $file = 'auth/' . $token . '.json';
        if (is_file($file)) {
            $var = [
                'session' => $token,
                'time' => date('d/m/Y'),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'client' => $_SERVER['HTTP_USER_AGENT']
            ];
            $atual = md5(json_encode($var));
            $saved = md5(file_get_contents($file));
            if ($atual == $saved) {
                $res = true;
            }
        }
        return $res;
    }
}