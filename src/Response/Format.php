<?php

namespace Subapp\Http\Response;

use Subapp\Http\Response;

/**
 * Class Format
 * @package Subapp\Http\Response
 */
abstract class Format implements FormatInterface
{
  
  /**
   * @var Response
   */
  protected $response = null;
  
  /**
   * @param Response $response
   */
  public function __construct(Response $response = null)
  {
    $this->response = $response;
  }
  
}