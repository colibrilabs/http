<?php

namespace Colibri\Http;

use Colibri\Http\Cookies\Cookie;

/**
 * Class Cookies
 * @package Colibri\Http
 */
class Cookies implements CookiesInterface
{
  
  /**
   * @var \Colibri\Http\Cookies\Cookie[]
   */
  protected $cookies = [];
  
  /**
   * @param $name
   * @param $value
   * @param int $expired
   * @param string $path
   * @param null|string $domain
   * @param null|bool $secure
   * @param null|bool $httpOnly
   * @return $this
   */
  public function set($name, $value = '', $expired = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false)
  {
    $this->cookies[$name] = new Cookie($name, $value, $expired, $path, $domain, $secure, $httpOnly);
    return $this;
  }
  
  /**
   * @param $name string
   * @return $this
   */
  public function delete($name)
  {
    
    if ($this->has($name)) {
      $this->get($name)->delete();
    }
    
    return $this;
  }
  
  /**
   * @param $name string
   * @return bool
   */
  public function has($name)
  {
    return isset($this->cookies[$name], $_COOKIE[$name]);
  }
  
  /**
   * @param $name string
   * @param $default string
   * @return Cookie
   */
  public function get($name, $default = '')
  {
    
    if ($this->has($name)) {
      return $this->cookies[$name];
    }
    
    $cookie = new Cookie($name);
    $this->cookies[$name] = $cookie;
    
    return $cookie;
  }
  
  /**
   * @return $this
   */
  public function send()
  {
    
    if (!headers_sent()) {
      foreach ($this->cookies as $cookie) {
        $cookie->send();
      }
    }
    
    return $this;
  }
  
}