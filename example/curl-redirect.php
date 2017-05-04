<?php

namespace HttpClientTest;

use Dez\Http\Client\Provider\Curl;

error_reporting(1);
ini_set('display_errors', 1);

include_once '../vendor/autoload.php';

$curl = new Curl();
$curl->uri('http://my.local/test-curl-redirect.php?go');
$curl->setTimeout(3);

$response = $curl->get();

if($response->isRedirect()) {
    var_dump($curl->getRedirectURL());
}

die(var_dump($response));