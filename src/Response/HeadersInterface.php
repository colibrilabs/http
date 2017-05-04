<?php

namespace Colibri\Http\Response;

/**
 * Interface HeadersInterface
 * @package Colibri\Http\Response
 */
interface HeadersInterface
{
  
  public function has($name);
  
  public function get($name);
  
  public function set($name, $value, $replace = true);
  
  public function setRaw($header);
  
  public function reset();
  
  public function send();
  
}