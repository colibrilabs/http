<?php

namespace Subapp\Http;

/**
 * Interface CookiesInterface
 * @package Subapp\Http
 */
interface CookiesInterface
{
  
  /**
   * @param $name
   * @param $value
   * @param $expired
   * @param $path
   * @param $domain
   * @param $secure
   * @param bool|false $httpOnly
   * @return mixed
   */
  public function set($name, $value, $expired, $path, $domain, $secure, $httpOnly = false);
  
  /**
   * @param $name
   * @param $default
   * @return mixed
   */
  public function get($name, $default);
  
  /**
   * @param $name
   * @return mixed
   */
  public function has($name);
  
  /**
   * @param $name
   * @return mixed
   */
  public function delete($name);
  
  /**
   * @return mixed
   */
  public function send();
  
}