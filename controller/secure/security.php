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

    function __construct($mode, $auth)
    {
        $this->mode = $mode;
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

// Método para mod_php (Apache)
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

// Se a autenticação não foi enviada
        if (is_null($username)) {
            header('WWW-Authenticate: Basic realm="GlobalRestFul"');
            header('HTTP/1.0 401 Unauthorized');
            die('Acesso negado.');
// Se houve envio dos dados
        } elseif ($username === $this->auth['user'] && $password === $this->auth['pass']) {
            header('WWW-Authenticate: Basic realm="GlobalRestFul"');
            header('HTTP/1.0 200 OK');
            return true;
        }
    }


    function TokenCreate()
    {
        return 'AEEEEEE CARALHOOO';
    }

    function TokenValidate()
    {

    }
}