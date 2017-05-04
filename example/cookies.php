<?php

    namespace Http;

    error_reporting(1);
    ini_set('display_errors', 1);

    include_once '../vendor/autoload.php';

    $cookies = new \Dez\Http\Cookies();

    $cookies->set('test', rand(1, 10000), time() + 10000000);

    var_dump($cookies, $_COOKIE);
    $cookies->send();
    var_dump($cookies, $_COOKIE);

