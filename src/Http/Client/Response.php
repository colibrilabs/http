<?php

namespace Dez\Http\Client;

/**
 * Class Response
 * @package Dez\Http\Client
 */
class Response {

    /**
     * @var integer
     */
    protected $httpCode = 0;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var string
     */
    protected $body;

    /**
     * Response constructor.
     * @param string $body
     * @param $httpCode
     * @param $contentType
     */
    public function __construct($body, $httpCode = 0, $contentType = null)
    {
        $this->body = $body;
        $this->httpCode = $httpCode;
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return array|object
     * @throws HttpRequestException
     */
    public function getJsonBody()
    {
        $json = json_decode($this->getBody());

        if(($error = json_last_error()) !== JSON_ERROR_NONE) {
            $errors = [
                JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
                JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
                JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
                JSON_ERROR_SYNTAX => 'Syntax error',
                JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
                JSON_ERROR_RECURSION => 'One or more recursive references in the value to be encoded',
                JSON_ERROR_INF_OR_NAN => 'One or more NAN or INF values in the value to be encoded',
                JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given',
            ];

            throw new HttpRequestException("Can not parse JSON by reason: {$errors[$error]}");
        }

        return $json;
    }

    /**
     * @return integer
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return ($this->getHttpCode() == 301 || $this->getHttpCode() == 302);
    }

}