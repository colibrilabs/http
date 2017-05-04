<?php

error_reporting(1);
ini_set('display_errors', 1);

include_once '../vendor/autoload.php';

$request = new \Dez\Http\Request();

print_r(
    [
        $request->getUserAgent(),
        $request->getClientIP(),
        $request->getRealClientIP(),
        $request->getPost(),
        $request->getQuery(),
        $request->getSchema(),
        $request->getServerIP(),
        $request->getRawBody(),
        $request->requestMethod(),
    ]
);