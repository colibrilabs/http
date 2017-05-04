<?php

namespace Colibri\Http;

use Colibri\Http\Request\File;

/**
 * Class Request
 * @package Colibri\Http
 */
class Request implements RequestInterface
{
  
  /**
   * @param $key
   * @param $value
   * @return bool
   */
  public function equal($key, $value)
  {
    return $this->has($key) && $this->get($key) === $value;
  }
  
  /**
   * @param $key
   * @return bool
   */
  public function has($key)
  {
    return isset($_REQUEST[$key]);
  }
  
  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function get($key = null, $default = null)
  {
    return $this->getFromArray($_REQUEST, $key, $default);
  }
  
  /**
   * @param array $source
   * @param $key
   * @param mixed $default
   * @return mixed
   */
  public function getFromArray(array $source, $key, $default = null)
  {
    return $key === null ? $source : (isset($source[$key]) ? $source[$key] : $default);
  }
  
  /**
   * @param $key
   * @param $value
   * @return bool
   */
  public function equalQuery($key, $value)
  {
    return $this->hasQuery($key) && $this->getQuery($key) === $value;
  }
  
  /**
   * @param $key
   * @return bool
   */
  public function hasQuery($key)
  {
    return isset($_GET[$key]);
  }
  
  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function getQuery($key = null, $default = null)
  {
    return $this->getFromArray($_GET, $key, $default);
  }
  
  /**
   * @param $key
   * @param $value
   * @return bool
   */
  public function equalPost($key, $value)
  {
    return $this->hasPost($key) && $this->getPost($key) === $value;
  }
  
  /**
   * @param $key
   * @return bool
   */
  public function hasPost($key)
  {
    return isset($_POST[$key]);
  }
  
  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function getPost($key = null, $default = null)
  {
    return $this->getFromArray($_POST, $key, $default);
  }
  
  /**
   * @param $key
   * @param $value
   * @return bool
   */
  public function equalServer($key, $value)
  {
    return $this->hasServer($key) && $this->getServer($key) === $value;
  }
  
  /**
   * @param $key
   * @return bool
   */
  public function hasServer($key)
  {
    $key = $key !== null ? strtoupper($key) : null;
    
    return isset($_SERVER[$key]);
  }
  
  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function getServer($key = null, $default = null)
  {
    $key = $key !== null ? strtoupper($key) : null;
    
    return $this->getFromArray($_SERVER, $key, $default);
  }
  
  /**
   * @return string
   */
  public function getRawBody()
  {
    static $rawBody;
    if (!$rawBody)
      $rawBody = file_get_contents('php://input');
    return $rawBody;
  }
  
  /**
   * @return string
   */
  public function getServerIP()
  {
    return $this->getServer('server_addr');
  }
  
  /**
   * @return string
   */
  public function getRealClientIP()
  {
    
    $remoteAddr = $this->getServerHttp('x_forwarded_for');
    
    if (!$remoteAddr) {
      $remoteAddr = $this->getServerHttp('client_ip');
      
      if (!$remoteAddr) {
        $remoteAddr = $this->getClientIP();
      }
    }
    
    if (strpos($remoteAddr, ',') !== false) {
      $remoteAddr = explode(',', $remoteAddr)[0];
    }
    
    return $remoteAddr;
  }
  
  /**
   * @param string $key
   * @param mixed $default
   * @return string
   */
  public function getServerHttp($key = null, $default = null)
  {
    return $this->getServer("http_$key", $default);
  }
  
  /**
   * @return string
   */
  public function getClientIP()
  {
    return $this->getServer('remote_addr');
  }
  
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->getServerHttp('user_agent');
  }
  
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->getServerHttp('host', '127.0.0.1');
  }
  
  /**
   * @param array $methods
   * @return bool
   */
  public function isMethod(array $methods)
  {
    return in_array($this->requestMethod(), $methods);
  }
  
  /**
   * @return string
   */
  public function requestMethod()
  {
    return strtoupper($this->getServer('request_method'));
  }
  
  /**
   * @return bool
   */
  public function isAjax()
  {
    return strtolower($this->getServerHttp('x_requested_with')) === 'xmlhttprequest';
  }
  
  /**
   * @return bool
   */
  public function isGet()
  {
    return $this->requestMethod() === 'GET';
  }
  
  /**
   * @return bool
   */
  public function isPost()
  {
    return $this->requestMethod() === 'POST';
  }
  
  /**
   * @return bool
   */
  public function isPut()
  {
    return $this->requestMethod() === 'PUT';
  }
  
  /**
   * @return bool
   */
  public function isDelete()
  {
    return $this->requestMethod() === 'DELETE';
  }
  
  /**
   * @return bool
   */
  public function isPatch()
  {
    return $this->requestMethod() === 'PATCH';
  }
  
  /**
   * @return bool
   */
  public function isHead()
  {
    return $this->requestMethod() === 'HEAD';
  }
  
  /**
   * @return bool
   */
  public function isOptions()
  {
    return $this->requestMethod() === 'OPTIONS';
  }
  
  /**
   * @return bool
   */
  public function isSecure()
  {
    return $this->getSchema() === 'https';
  }
  
  /**
   * @return string
   */
  public function getSchema()
  {
    $schema = $this->getServer('https');
    return !$schema || $schema === 'off' ? 'http' : 'https';
  }
  
  /**
   * @param null $filterKey
   * @return Request\File[]
   */
  public function getUploadedFiles($filterKey = null)
  {
    /** @var $temporaryFiles File[] */
    $temporaryFiles = [];
    
    if ($this->hasFiles()) {
      foreach ($_FILES as $index => $file) {
        if (gettype($file['name']) === 'string') {
          $temporaryFiles[] = new File($file + ['key' => $index]);
        } else if (gettype($file['name']) === 'array') {
          $preparedFiles = $this->prepareFiles($file['name'], $file['tmp_name'], $file['type'], $file['size'], $file['error'], $index);
          foreach ($preparedFiles as $preparedFile) {
            $temporaryFiles[] = new File($preparedFile);
          }
        }
      }
      unset($file);
    }
    
    $files = [];
    
    if (null !== $filterKey && gettype($filterKey) === 'string') {
      foreach ($temporaryFiles as $temporaryFile) {
        if (strpos($temporaryFile->getKey(), $filterKey) === 0) {
          $files[] = $temporaryFile;
        }
      }
    } else {
      $files = $temporaryFiles;
    }
    
    return $files;
  }
  
  /**
   * @return bool
   */
  public function hasFiles()
  {
    return isset($_FILES) && count($_FILES) > 0;
  }
  
  /**
   * @param array $names
   * @param array $temporaryNames
   * @param array $types
   * @param array $sizes
   * @param array $errors
   * @param null $prefix
   * @return array
   */
  private function prepareFiles(array $names, array $temporaryNames, array $types, array $sizes, array $errors, $prefix = null)
  {
    $files = [];
    
    foreach ($names as $index => $name) {
      $currentPrefix = "{$prefix}.{$index}";
      
      if (gettype($name) === 'array') {
        foreach ($this->prepareFiles($names[$index], $temporaryNames[$index], $types[$index], $sizes[$index], $errors[$index], $currentPrefix) as $file) {
          $files[] = $file;
        }
      } else {
        $files[] = [
          'name' => $names[$index],
          'tmp_name' => $temporaryNames[$index],
          'type' => $types[$index],
          'size' => $sizes[$index],
          'error' => $errors[$index],
          'key' => $currentPrefix,
        ];
      }
    }
    
    return $files;
  }
  
}
