<?php

error_reporting(1);
ini_set('display_errors', 1);

include_once '../vendor/autoload.php';

$container  = new \Dez\DependencyInjection\Container();

$container->set( 'response', new \Dez\Http\Response() );
$container->set( 'cookies', new \Dez\Http\Cookies() );

/** @var $response \Dez\Http\Response */
$response   = $container->get( 'response' );

class CustomFormatter extends \Dez\Http\Response\Format {

    public function process()
    {
        $this->response->setContentTypePlain();
        $this->response->setContent(
            "before".
            json_encode([
                'content' => $this->response->getContent()
            ], JSON_PRETTY_PRINT).
            "after"
        );
    }

}

$response->setBodyFormat(\Dez\Http\Response::RESPONSE_CUSTOM, CustomFormatter::class);

$response->setContent( '<h1>Hello world!</h1>' );

$response->send();