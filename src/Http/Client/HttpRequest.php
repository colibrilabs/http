<?php

namespace Dez\Http\Client;

use Dez\Url\Uri;

/**
 * Class HttpRequest
 * @package Dez\Http\Client
 */
abstract class HttpRequest
{

    const METHOD_GET = 'GET';

    const METHOD_POST = 'POST';

    const METHOD_PUT = 'PUT';

    const METHOD_DELETE = 'DELETE';

    const VERSION = '1.0.0';

    const USER_AGENT = 'DezHttpClient v%s Provider/%s';

    /**
     * @var Uri
     */
    protected $uri = null;
    
    public function __construct()
    {

    }

    public function uri($uri)
    {
        $this->setUri(new Uri($uri));

        return $this;
    }

    /**
     * @return Uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param Uri $uri
     * @return static
     */
    public function setUri(Uri $uri)
    {
        $this->uri = $uri;

        return $this;
    }

}