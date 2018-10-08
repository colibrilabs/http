<?php

namespace Subapp\Http\Response\Format;

use Subapp\Http\Response\Format;

/**
 * Class ApiJson
 * @package Subapp\Http\Response\Format
 */
class ApiJson extends Format
{
  
  /**
   * @return \Subapp\Http\Response
   */
  public function process()
  {
    $this->response->setHeader('Content-type', 'application/json');
    $this->response->setContent(json_encode($this->createResponseBody(), JSON_PRETTY_PRINT));
    
    return $this->response;
  }
  
  /**
   * @return array|null
   * @throws \Subapp\Http\Exception
   */
  private function createResponseBody()
  {
    $response = $this->response->getContent();
    $statusCode = $this->response->getStatusCode();
    
    $response = !is_array($response) ? [$response] : $response;
    
    $response['responseDebug'] = [
      'statusCode' => $statusCode,
      'statusMessage' => $this->response->getStatusMessage($statusCode),
      'memoryUsage' => $this->memoryUsage(),
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
    $scale = (integer)log($bytes, 1024);
    
    return round($bytes / pow(1024, $scale), 2) . $names[$scale];
  }
  
}
