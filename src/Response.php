<?php

namespace Colibri\Http;

use Colibri\Http\Response\Format\ApiJson;
use Colibri\Http\Response\Format\Html;
use Colibri\Http\Response\Format\Json;
use Colibri\Http\Response\Format\Raw;
use Colibri\Http\Response\FormatInterface;
use Colibri\Http\Response\Headers;

/**
 * Class Response
 * @package Colibri\Http
 */
class Response implements ResponseInterface
{
  
  const RESPONSE_RAW = 'raw';
  
  const RESPONSE_JSON = 'json';
  
  const RESPONSE_HTML = 'html';
  
  const RESPONSE_API_JSON = 'api_json';
  
  const RESPONSE_CUSTOM = 'custom';
  
  const CONTENT_HTML = 'text/html';
  
  const CONTENT_PLAIN = 'text/plain';
  
  const CONTENT_JSON = 'application/json';
  
  const CONTENT_XML = 'text/xml';
  
  /**
   * @var array
   */
  protected static $classesMap = [
    self::RESPONSE_RAW => Raw::class,
    self::RESPONSE_JSON => Json::class,
    self::RESPONSE_API_JSON => ApiJson::class,
    self::RESPONSE_HTML => Html::class,
    self::RESPONSE_CUSTOM => null,
  ];
  
  /**
   * @var string
   */
  protected $bodyFormat = self::RESPONSE_HTML;
  
  /**
   * @var null
   */
  protected $formatter = null;
  
  /**
   * @var int
   */
  protected $statusCode = 200;
  
  /**
   * @var null
   */
  protected $content = null;
  
  /**
   * @var \Colibri\Http\Response\HeadersInterface
   */
  protected $headers = null;
  
  /**
   * @var bool
   */
  protected $enableBody = true;
  
  /**
   * @var CookiesInterface
   */
  protected $cookies = null;
  
  
  /**
   * @param null $content
   * @param int $statusCode
   * @param null $statusMessage
   */
  public function __construct($content = null, $statusCode = 200, $statusMessage = null)
  {
    $this->setHeaders(new Headers());
    $this->setHeader('X-Greetings-From', 'colibriphp-http');
    $this->setBodyFormat(self::RESPONSE_HTML);
    
    if ($content !== null) {
      $this->setContent($content)->setStatusCode($statusCode, $statusMessage);
    }
  }
  
  /**
   * @return CookiesInterface
   */
  public function getCookies()
  {
    return $this->cookies;
  }
  
  /**
   * @param CookiesInterface $cookies
   * @return $this
   */
  public function setCookies(CookiesInterface $cookies)
  {
    $this->cookies = $cookies;
    
    return $this;
  }
  
  /**
   * @param string $name
   * @param $value
   * @return $this
   */
  public function setHeader($name = '', $value)
  {
    $this->headers->set($name, $value, true);
    
    return $this;
  }
  
  /**
   * @return int
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }
  
  /**
   * @param $statusCode
   * @param null $statusMessage
   * @return $this
   * @throws Exception
   */
  public function setStatusCode($statusCode, $statusMessage = null)
  {
    
    if ($statusMessage === null) {
      $statusMessage = $this->getStatusMessage($statusCode);
    }
    
    $this->setRawHeader("HTTP/1.1 $statusCode $statusMessage");
    $this->setHeader('Status', "$statusCode $statusMessage");
    
    $this->statusCode = $statusCode;
    
    return $this;
  }
  
  /**
   * @return Response
   */
  public function setContentTypeHtml()
  {
    return $this->setContentType(self::CONTENT_HTML);
  }
  
  /**
   * @param string $type
   * @return $this
   */
  public function setContentType($type = self::CONTENT_HTML)
  {
    $this->setHeader('Content-type', $type);
    
    return $this;
  }
  
  /**
   * @return Response
   */
  public function setContentTypeJson()
  {
    return $this->setContentType(self::CONTENT_JSON);
  }
  
  /**
   * @return Response
   */
  public function setContentTypeXml()
  {
    return $this->setContentType(self::CONTENT_XML);
  }
  
  /**
   * @return Response
   */
  public function setContentTypePlain()
  {
    return $this->setContentType(self::CONTENT_PLAIN);
  }
  
  /**
   * @param string $url
   * @return $this
   */
  public function redirect($url = '/')
  {
    $this->setEnableBody(false);
    $this->setStatusCode(302);
    $this->getHeaders()->set('Location', $url);
    
    return $this;
  }
  
  /**
   * @return \Colibri\Http\Response\HeadersInterface
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  
  /**
   * @param \Colibri\Http\Response\Headers $headers
   * @return static
   */
  public function setHeaders(Headers $headers)
  {
    $this->headers = $headers;
    
    return $this;
  }
  
  /**
   * @param null $content
   * @return static
   */
  public function appendContent($content)
  {
    $this->content = $this->getContent() . $content;
    
    return $this;
  }
  
  /**
   * @return null
   */
  public function getContent()
  {
    return $this->content;
  }
  
  /**
   * @param null $content
   * @return static
   */
  public function setContent($content)
  {
    $this->content = $content;
    
    return $this;
  }
  
  /**
   * @param null $content
   * @return static
   */
  public function prependContent($content)
  {
    $this->content = $content . $this->getContent();
    
    return $this;
  }
  
  /**
   * @param $header
   * @return $this
   */
  public function setRawHeader($header)
  {
    $this->headers->setRaw($header);
    
    return $this;
  }
  
  /**
   * @param string $name
   * @param $value
   * @return $this
   */
  public function addHeader($name = '', $value)
  {
    $this->headers->set($name, $value, false);
    
    return $this;
  }
  
  /**
   * @param $name
   * @return mixed
   */
  public function hasHeader($name)
  {
    return $this->headers->has($name);
  }
  
  /**
   * @param $name
   * @return mixed
   */
  public function getHeader($name)
  {
    return $this->headers->get($name);
  }
  
  /**
   * @return $this
   */
  public function resetHeaders()
  {
    $this->getHeaders()->reset();
    
    return $this;
  }
  
  /**
   * @return boolean
   */
  public function isEnableBody()
  {
    return $this->enableBody;
  }
  
  /**
   * @param boolean $enableBody
   * @return $this
   */
  public function setEnableBody($enableBody)
  {
    $this->enableBody = $enableBody;
    
    return $this;
  }
  
  /**
   * @return $this
   * @throws Exception
   */
  public function send()
  {
    $this->handlerContent()->sendHeaders()->sendCookies()->sendContent();
    
    return $this;
  }
  
  /**
   * @return $this
   */
  public function sendContent()
  {
    echo $this->getContent();
    
    return $this;
  }
  
  /**
   * @return $this
   * @throws Exception
   */
  public function sendCookies()
  {
    if ($this->cookies instanceof CookiesInterface) {
      $this->cookies->send();
    }
    
    return $this;
  }
  
  /**
   * @return $this
   */
  public function sendHeaders()
  {
    $this->getHeaders()->send();
    
    return $this;
  }
  
  /**
   * @throws Exception
   */
  public function handlerContent()
  {
    $reflection = new \ReflectionClass($this->formatter);
    $formatter = $reflection->newInstanceArgs([$this]);
    
    $formatter->process();
    
    return $this;
  }
  
  /**
   * @return string
   */
  public function getBodyFormat()
  {
    return $this->bodyFormat;
  }
  
  /**
   * @param string $bodyFormat
   * @param null|string $customFormatter
   * @return $this
   * @throws Exception
   */
  public function setBodyFormat($bodyFormat, $customFormatter = null)
  {
    $this->bodyFormat = $bodyFormat;
    
    if (!array_key_exists($bodyFormat, static::$classesMap)) {
      throw new Exception("Bad response formatter '{$bodyFormat}' passed.");
    }
    
    $class = static::$classesMap[$bodyFormat];
    
    if (null === $class && $customFormatter !== null && $bodyFormat === self::RESPONSE_CUSTOM) {
      $class = $customFormatter;
    }
    
    if (!is_subclass_of($class, FormatInterface::class)) {
      $formatterInterface = FormatInterface::class;
      throw new Exception("Formatter must be implement of '{$formatterInterface}'");
    }
    
    $this->formatter = $class;
    
    return $this;
  }
  
  /**
   * @param int $statusCode
   * @return string
   * @throws Exception
   */
  public function getStatusMessage($statusCode = 0)
  {
    $statusCodes = [
      100 => "Continue",
      101 => "Switching Protocols",
      102 => "Processing",
      200 => "OK",
      201 => "Created",
      202 => "Accepted",
      203 => "Non-Authoritative Information",
      204 => "No Content",
      205 => "Reset Content",
      206 => "Partial Content",
      207 => "Multi-status",
      208 => "Already Reported",
      300 => "Multiple Choices",
      301 => "Moved Permanently",
      302 => "Found",
      303 => "See Other",
      304 => "Not Modified",
      305 => "Use Proxy",
      306 => "Switch Proxy",
      307 => "Temporary Redirect",
      400 => "Bad Request",
      401 => "Unauthorized",
      402 => "Payment Required",
      403 => "Forbidden",
      404 => "Not Found",
      405 => "Method Not Allowed",
      406 => "Not Acceptable",
      407 => "Proxy Authentication Required",
      408 => "Request Time-out",
      409 => "Conflict",
      410 => "Gone",
      411 => "Length Required",
      412 => "Precondition Failed",
      413 => "Request Entity Too Large",
      414 => "Request-URI Too Large",
      415 => "Unsupported Media Type",
      416 => "Requested range not satisfiable",
      417 => "Expectation Failed",
      418 => "I'm a teapot",
      422 => "Unprocessable Entity",
      423 => "Locked",
      424 => "Failed Dependency",
      425 => "Unordered Collection",
      426 => "Upgrade Required",
      428 => "Precondition Required",
      429 => "Too Many Requests",
      431 => "Request Header Fields Too Large",
      500 => "Internal Server Error",
      501 => "Not Implemented",
      502 => "Bad Gateway",
      503 => "Service Unavailable",
      504 => "Gateway Time-out",
      505 => "HTTP Version not supported",
      506 => "Variant Also Negotiates",
      507 => "Insufficient Storage",
      508 => "Loop Detected",
      511 => "Network Authentication Required",
    ];
    
    if (!isset($statusCodes[$statusCode])) {
      throw new Exception("The incorrect status code. In this code [$statusCode] there is no message");
    }
    
    return $statusCodes[$statusCode];
  }
  
}