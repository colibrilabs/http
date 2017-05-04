<?php

namespace Colibri\Http\Response;

use Colibri\Http\Response;

/**
 * Class Format
 * @package Colibri\Http\Response
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