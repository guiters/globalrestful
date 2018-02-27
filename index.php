<?php
/*
 * Aplicacao dinamica para qualquer tipo de base de dados
 * Nesta aplicacao voce podera criar sua api apenas criando patterns de exibição
 * Não necessitando conhecimento de desenvolvimento de software
 * A aplicacao é totalmente dinamica e automatica.
 * Para solicitar novos drivers de conexao contate-me Guilhermecamachop@gmail.com
 * */

session_start();
$time_start = microtime(true);
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");

setlocale(LC_ALL, 'pt_BR.UTF-8');

/* as configuracoes a baixo sao para desenvolvimento */
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_erros', 0);
/* as configuracoes a acima sao para desenvolvimento */

/* Arquivos necessarios base para o funcionamento */
require 'config.php';
require 'controller/helpers/helpers.function.php';
require 'controller/urlservices/urlservice.php';
require 'controller/secure/security.php';
require 'controller/pattern/pattern.php';
require 'controller/DriverConnection/DriverConnection.php';
require 'controller/CustomResponse/CustomResponse.php';
/* Arquivos necessarios base para o funcionamento */

/*Inicio o controle de rota*/
$url = new urlService($_SERVER['REQUEST_URI']);
$route = $url->getPages();
/*Inicio a base de seguranca*/
$secure = new security('GET', $auth);

if ($secure->basicauth()) {
    header('Content-Type: application/json');
    $pattern = new pattern($route, $pattern_path);
    $pattern = $pattern->start();
    $result = $pattern;
    if (!in_array_r_like_key('error', $pattern)) {
        $driver = new DriverConnection($pattern);
        $result = $driver->start();
    }
    echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    header('HTTP/1.0 403 Forbidden');
    echo 'You are forbidden!';
}

$time_end = microtime(true);
$execution_time = round(($time_end - $time_start) * 1000);
toconsole(json_encode($result, JSON_PRETTY_PRINT));