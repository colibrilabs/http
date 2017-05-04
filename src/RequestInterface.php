<?php

namespace Colibri\Http;

interface RequestInterface
{
  
  public function requestMethod();
  
  public function has($key);
  
  public function hasQuery($key);
  
  public function hasPost($key);
  
  public function getRawBody();
  
  public function get($key, $default);
  
  public function getPost($key, $default);
  
  public function getQuery($key, $default);
  
  public function getServerHttp();
  
  public function getServer();
  
  public function getServerIP();
  
  public function getClientIP();
  
  public function getRealClientIP();
  
  public function getUserAgent();
  
  public function getSchema();
  
  public function getFromArray(array $source, $key, $default);
  
  public function isMethod(array $methods);
  
  public function isAjax();
  
  public function isGet();
  
  public function isPost();
  
  public function isPut();
  
  public function isDelete();
  
  public function isPatch();
  
  public function isHead();
  
  public function isOptions();
  
  public function isSecure();
  
}