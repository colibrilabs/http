<?php

namespace Dez\Http\Response\Format;

use Dez\Http\Response\Format;

/**
 * Class ApiJson
 * @package Dez\Http\Response\Format
 */
class ApiJson extends Format
{

    /**
     * @return \Dez\Http\Response
     */
    public function process()
    {
        $this->response->setHeader('Content-type', 'application/json');
        $this->response->setContent(json_encode($this->createResponseBody(), JSON_PRETTY_PRINT));

        return $this->response;
    }

    /**
     * @return array|null
     * @throws \Dez\Http\Exception
     */
    private function createResponseBody()
    {
        $response = $this->response->getContent();
        $statusCode = $this->response->getStatusCode();

        $response = !is_array($response) ? [$response] : $response;

        $response['response_info'] = [
            'code' => $statusCode,
            'status' => $this->response->getStatusMessage($statusCode),
            'memory' => $this->memoryUsage(),
        ];

        return $response;
    }

    /**
     * @return string
     */
    private function memoryUsage()
    {
        $names = ['B', 'K', 'M', 'G', 'T'];
        $bytes = memory_get_usage();
        $scale = (integer) log($bytes, 1024);

        return round($bytes / pow(1024, $scale), 2) . $names[$scale];
    }

}