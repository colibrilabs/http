<?php

namespace Dez\Http\Client\Provider;

use Dez\Http\Client\HttpRequest;
use Dez\Http\Client\HttpRequestException;
use Dez\Http\Client\Response;

/**
 * Class Curl
 * @package Dez\Http\Client\Provider
 */
class Curl extends HttpRequest {

    const RESPONSE_INFO_URL = 'url';

    const RESPONSE_INFO_CONTENT_TYPE = 'content_type';

    const RESPONSE_INFO_HTTP_CODE = 'http_code';

    const RESPONSE_INFO_HEADER_SIZE = 'header_size';

    const RESPONSE_INFO_REQUEST_SIZE = 'request_size';

    const RESPONSE_INFO_REDIRECT_COUNT = 'redirect_count';

    const RESPONSE_INFO_REDIRECT_TIME = 'redirect_time';

    const RESPONSE_INFO_REDIRECT_URL = 'redirect_url';

    /**
     * @var resource
     */
    private $handle;

    /**
     * @var array
     */
    private $responseInfo;

    /**
     * Curl constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        if(! extension_loaded('curl')) {
            throw new HttpRequestException("cURL extension not available");
        }
        
        $this->handle = curl_init();
        
        $this->setOptions([
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_MAXREDIRS       => 20,
            CURLOPT_PROTOCOLS       => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_USERAGENT       => sprintf(static::USER_AGENT, static::VERSION, 'cURL'),
            CURLOPT_CONNECTTIMEOUT  => 10,
            CURLOPT_TIMEOUT         => 15
        ]);
    }

    /**
     * Curl destructor.
     */
    public function __destruct()
    {
        curl_close($this->handle);
    }

    /**
     * @param integer $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->setOption(CURLOPT_TIMEOUT, $timeout);

        return $this;
    }

    /**
     * @param integer $timeout
     * @return $this
     */
    public function setConnectTimeout($timeout)
    {
        $this->setOption(CURLOPT_CONNECTTIMEOUT, $timeout);

        return $this;
    }

    /**
     * @param bool $mode
     * @return $this
     */
    public function setFollowLocation($mode = true)
    {
        $this->setOption(CURLOPT_FOLLOWLOCATION, $mode);

        return $this;
    }

    /**
     * @param $option
     * @param $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        curl_setopt($this->handle, $option, $value);
        
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options = [])
    {
        curl_setopt_array($this->handle, $options);

        return $this;
    }

    /**
     * @param null $key
     * @return null|mixed
     */
    public function getResponseInfo($key = null)
    {
        return null !== $key && isset($this->responseInfo[$key]) ? $this->responseInfo[$key] : null;
    }

    /**
     * @return mixed|null
     */
    public function getRedirectURL()
    {
        return $this->getResponseInfo(static::RESPONSE_INFO_REDIRECT_URL);
    }

    /**
     * @param string $method
     * @return Response
     * @throws HttpRequestException
     */
    public function send($method = HttpRequest::METHOD_GET)
    {
        $this->setOption(CURLOPT_CUSTOMREQUEST, $method);
        $this->setOption(CURLOPT_URL, $this->uri->full());
        $this->setOption(CURLOPT_HTTPHEADER, ["X-cURL-Request: ". time()]);

        $responseContent = curl_exec($this->handle);
        $this->responseInfo = curl_getinfo($this->handle);

        if(curl_errno($this->handle) !== 0) {
            throw new HttpRequestException("cURL failed with error: ". curl_error($this->handle));
        }

        $contentType = $this->getResponseInfo(static::RESPONSE_INFO_CONTENT_TYPE);
        $statusCode = $this->getResponseInfo(static::RESPONSE_INFO_HTTP_CODE);
        
        return new Response($responseContent, $statusCode, $contentType);
    }

    /**
     * @param array $params
     * @return Response
     * @throws HttpRequestException
     */
    public function get(array $params = [])
    {
        $this->setOption(CURLOPT_HTTPGET, true);

        foreach ($params as $name => $value) {
            $this->uri->setQuery($name, $value);
        }

        return $this->send(HttpRequest::METHOD_GET);
    }

    /**
     * @param array $params
     * @return Response
     * @throws HttpRequestException
     */
    public function post(array $params = [])
    {
        $this->setOption(CURLOPT_POST, true);
        $this->setOption(CURLOPT_POSTFIELDS, $params);

        return $this->send(HttpRequest::METHOD_POST);
    }

    /**
     * @param array $params
     * @return Response
     * @throws HttpRequestException
     */
    public function put(array $params = [])
    {
        $this->setOption(CURLOPT_POST, true);
        $this->setOption(CURLOPT_POSTFIELDS, $params);

        return $this->send(HttpRequest::METHOD_PUT);
    }

    /**
     * @param array $params
     * @return Response
     * @throws HttpRequestException
     */
    public function delete(array $params = [])
    {
        $this->setOption(CURLOPT_POST, true);
        $this->setOption(CURLOPT_POSTFIELDS, $params);

        return $this->send(HttpRequest::METHOD_DELETE);
    }

    /**
     * @param string $filepath
     * @param string $mimetype
     * @param string $name
     * @return \CURLFile
     * @throws HttpRequestException
     */
    public static function file($filepath = null, $mimetype = null, $name = null)
    {
        if(! file_exists($filepath)) {
            throw new HttpRequestException("File could not be found");
        }

        if(! is_readable($filepath)) {
            throw new HttpRequestException("File '{$filepath}' is not readable. Please change permissions");
        }

        return curl_file_create($filepath, $mimetype, $name);
    }

}