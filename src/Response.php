<?php

namespace Subapp\Http;

use Subapp\Http\Response\Format\ApiJson;
use Subapp\Http\Response\Format\Html;
use Subapp\Http\Response\Format\Json;
use Subapp\Http\Response\Format\Raw;
use Subapp\Http\Response\FormatInterface;
use Subapp\Http\Response\Headers;

/**
 * Class Response
 * @package Subapp\Http
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
  
  const INFO_CONTINUE = 100;
  const INFO_SWITCHING_PROTOCOL = 101;
  const INFO_PROCESSING = 102;
  const SUCCESS_OK = 200;
  const SUCCESS_CREATED = 201;
  const SUCCESS_ACCEPTED = 202;
  const SUCCESS_NON_AUTHORITATIVE_INFO = 203;
  const SUCCESS_NO_CONTENT = 204;
  const SUCCESS_RESET_CONTENT = 205;
  const SUCCESS_PARTIAL_CONTENT = 206;
  const SUCCESS_MULTI_STATUS = 207;
  const SUCCESS_ALREADY_REPORTED = 208;
  const REDIRECT_MULT_CHOICE = 300;
  const REDIRECT_MOVED = 301;
  const REDIRECT_FOUND = 302;
  const REDIRECT_SEE_OTHER = 303;
  const REDIRECT_NOT_MODIFIED = 304;
  const REDIRECT_USE_PROXY = 305;
  const REDIRECT_UNUSED = 306;
  const REDIRECT_REDIRECT_TEMP = 307;
  const REDIRECT_REDIRECT_PERMANENT = 308;
  const ERROR_BAD_REQUEST = 400;
  const ERROR_UNAUTHORIZED = 401;
  const ERROR_PAYMENT_REQUIRED = 402;
  const ERROR_FORBIDDEN = 403;
  const ERROR_NOT_FOUND = 404;
  const ERROR_METHOD_NOT_ALLOWED = 405;
  const ERROR_NOT_ACCEPTABLE = 406;
  const ERROR_AUTHENTICATE_WITH_PROXY = 407;
  const ERROR_REQUEST_TIMEOUT = 408;
  const ERROR_CONFLICT = 409;
  const ERROR_REQUESTED_CONTENT_GONE = 410;
  const ERROR_CONTENT_LENGTH_REQUIRED = 411;
  const ERROR_PRECONDITION_FAILED = 412;
  const ERROR_PAYLOAD_TOO_LARGE = 413;
  const ERROR_URI_TOO_LONG = 414;
  const ERROR_UNSUPPORTED_MEDIA_TYPE = 415;
  const ERROR_REQUESTED_RANGE = 416;
  const ERROR_EXPECTATION_FAILED = 417;
  const ERROR_IAM_TEAPOT = 418;
  const ERROR_MISDIRECTED_REQUEST = 421;
  const ERROR_UNPROCESSABLE_ENTITY = 422;
  const ERROR_LOCKED = 423;
  const ERROR_FAILED_DEPENDENCY = 424;
  const ERROR_UPGRADE_REQUIRED = 426;
  const ERROR_PRECONDITION_REQUIRED = 428;
  const ERROR_TOO_MANY_REQUESTS = 429;
  const ERROR_REQUEST_HEADERS_TOO_LONG = 431;
  const SERVER_INTERNAL_ERROR = 500;
  const SERVER_NOT_IMPLEMENTED = 501;
  const SERVER_BAD_GATEWAY = 502;
  const SERVER_SERVICE_UNAVAILABLE = 503;
  const SERVER_GATEWAY_TIMEOUT = 504;
  const SERVER_HTTP_VERSION_NOT_SUPPORTED = 505;
  const SERVER_VARIANT_ALSO_NEGOTIATES_1 = 506;
  const SERVER_VARIANT_ALSO_NEGOTIATES_2 = 507;
  const SERVER_LOOP_DETECTED = 508;
  const SERVER_NETWORK_AUTHENTICATION_REQUIRED = 511;
  
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
   * @var \Subapp\Http\Response\HeadersInterface
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
    $this->setHeader('X-Php-Lib', 'Subapp/Http');
    $this->setHeader('X-Author', 'Ivan Hontarenko');
    $this->setHeader('X-Author-Email', 'ihontarenko@gmail.com');
    $this->setBodyFormat(self::RESPONSE_HTML);
    
    if ($content !== null) {
      $this->setContent($content)->setStatusCode($statusCode, $statusMessage);
    }
  }
  
  /**
   * @param string $name
   * @param $value
   * @return $this
   */
  public function setHeader($name, $value)
  {
    $this->headers->set($name, $value, true);
    
    return $this;
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
   * @return \Subapp\Http\Response\HeadersInterface
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  
  /**
   * @param \Subapp\Http\Response\Headers $headers
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
      static::INFO_CONTINUE => "Continue",
      static::INFO_SWITCHING_PROTOCOL => "Switching Protocols",
      static::INFO_PROCESSING => "Processing",
      static::SUCCESS_OK => "OK",
      static::SUCCESS_CREATED => "Created",
      static::SUCCESS_ACCEPTED => "Accepted",
      static::SUCCESS_NON_AUTHORITATIVE_INFO => "Non-Authoritative Information",
      static::SUCCESS_NO_CONTENT => "No Content",
      static::SUCCESS_RESET_CONTENT => "Reset Content",
      static::SUCCESS_PARTIAL_CONTENT => "Partial Content",
      static::SUCCESS_MULTI_STATUS => "Multi-status",
      static::SUCCESS_ALREADY_REPORTED => "Already Reported",
      static::REDIRECT_MULT_CHOICE => "Multiple Choices",
      static::REDIRECT_MOVED => "Moved Permanently",
      static::REDIRECT_FOUND => "Found",
      static::REDIRECT_SEE_OTHER => "See Other",
      static::REDIRECT_NOT_MODIFIED => "Not Modified",
      static::REDIRECT_USE_PROXY => "Use Proxy",
      static::REDIRECT_UNUSED => "Switch Proxy",
      static::REDIRECT_REDIRECT_TEMP => "Temporary Redirect",
      static::REDIRECT_REDIRECT_PERMANENT => "Permanent Redirect",
      static::ERROR_BAD_REQUEST => "Bad Request",
      static::ERROR_UNAUTHORIZED => "Unauthorized",
      static::ERROR_PAYMENT_REQUIRED => "Payment Required",
      static::ERROR_FORBIDDEN => "Forbidden",
      static::ERROR_NOT_FOUND => "Not Found",
      static::ERROR_METHOD_NOT_ALLOWED => "Method Not Allowed",
      static::ERROR_NOT_ACCEPTABLE => "Not Acceptable",
      static::ERROR_AUTHENTICATE_WITH_PROXY => "Proxy Authentication Required",
      static::ERROR_REQUEST_TIMEOUT => "Request Time-out",
      static::ERROR_CONFLICT => "Conflict",
      static::ERROR_REQUESTED_CONTENT_GONE => "Gone",
      static::ERROR_CONTENT_LENGTH_REQUIRED => "Length Required",
      static::ERROR_PRECONDITION_FAILED => "Precondition Failed",
      static::ERROR_PAYLOAD_TOO_LARGE => "Request Entity Too Large",
      static::ERROR_URI_TOO_LONG => "Request-URI Too Large",
      static::ERROR_UNSUPPORTED_MEDIA_TYPE => "Unsupported Media Type",
      static::ERROR_REQUESTED_RANGE => "Requested range not satisfiable",
      static::ERROR_EXPECTATION_FAILED => "Expectation Failed",
      static::ERROR_IAM_TEAPOT => "I'm a teapot",
      static::ERROR_MISDIRECTED_REQUEST => "Misdirected Request",
      static::ERROR_UNPROCESSABLE_ENTITY => "Unprocessable Entity",
      static::ERROR_LOCKED => "Locked",
      static::ERROR_FAILED_DEPENDENCY => "Failed Dependency",
      static::ERROR_UPGRADE_REQUIRED => "Upgrade Required",
      static::ERROR_PRECONDITION_REQUIRED => "Precondition Required",
      static::ERROR_TOO_MANY_REQUESTS => "Too Many Requests",
      static::ERROR_REQUEST_HEADERS_TOO_LONG => "Request Header Fields Too Large",
      static::SERVER_INTERNAL_ERROR => "Internal Server Error",
      static::SERVER_NOT_IMPLEMENTED => "Not Implemented",
      static::SERVER_BAD_GATEWAY => "Bad Gateway",
      static::SERVER_SERVICE_UNAVAILABLE => "Service Unavailable",
      static::SERVER_GATEWAY_TIMEOUT => "Gateway Time-out",
      static::SERVER_HTTP_VERSION_NOT_SUPPORTED => "HTTP Version not supported",
      static::SERVER_VARIANT_ALSO_NEGOTIATES_1 => "Variant Also Negotiates",
      static::SERVER_VARIANT_ALSO_NEGOTIATES_2 => "Insufficient Storage",
      static::SERVER_LOOP_DETECTED => "Loop Detected",
      static::SERVER_NETWORK_AUTHENTICATION_REQUIRED => "Network Authentication Required",
    ];
    
    if (!isset($statusCodes[$statusCode])) {
      throw new Exception("The incorrect response status code passed. For code ({$statusCode}) don't have any message");
    }
    
    return $statusCodes[$statusCode];
  }
  
}
