<?php

error_reporting(1);
ini_set('display_errors', 1);

include_once '../vendor/autoload.php';

$container  = new \Dez\DependencyInjection\Container();

$container->set( 'response', new \Dez\Http\Response() );
$container->set( 'cookies', new \Dez\Http\Cookies() );

/** @var $cookies \Dez\Http\CookiesInterface */
$cookies    = $container->get( 'cookies' );

/** @var $response \Dez\Http\Response */
$response   = $container->get( 'response' );

$cookies->set( 'test', rand(), time() * 100 );

$response->setHeader( 'Test', '123qwe' );

$response->setContent( [ 'data' => [1,2,3] ] );

$response->setStatusCode( 418 );

$response->setBodyFormat( \Dez\Http\Response::RESPONSE_API_JSON );

$response->send();

$response->resetHeaders();

$response->setBodyFormat( \Dez\Http\Response::RESPONSE_HTML );

$response->setContent( '<h1>Hello world!</h1>' );

$response->send();

( new \Dez\Http\Response( '<h2>Page not found!</h2>', 401 ) )->sendHeaders()->sendContent();

// for for response add manually Di Container
$cookies->set( 'randomCookie_'.rand(), '1', time() + 365 * 86400 );
( new \Dez\Http\Response( '<h3>Full response!</h3>', 401 ) )->setDi( $container )->send();