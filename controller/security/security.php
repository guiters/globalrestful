<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 11/02/18
 * Time: 13:22
 */

class security
{
    public $mode;
    public $auth;
    public $token;

    function __construct($mode, $auth, $token = null)
    {
        $this->mode = $mode;
        $this->token = $token;
        $this->auth = $auth;
    }

    public function start()
    {
        if ($this->mode == 'GET' || $this->mode == 'POST') {
            return in_arrays($this->auth, $_REQUEST);
        }
    }

    public function basicauth()
    {
        $username = null;
        $password = null;
        $mod = NULL;

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
            $mod = 'PHP_AUTH_USER';
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            if (preg_match('/^basic/i', $_SERVER['HTTP_AUTHORIZATION'])) {
                list($username, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
            }
            $mod = 'HTTP_AUTHORIZATION';
        }
        if (is_null($username)) {
            header('WWW-Authenticate: Basic realm="GlobalRestFul"');
            http_set_code(401);
            die('Acesso negado.');
        } elseif ($username === $this->auth['user'] && $password === $this->auth['pass']) {
            header('WWW-Authenticate: Basic realm="GlobalRestFul"');
            http_set_code(200);
            return true;
        }
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
        $token = $this->mode;
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