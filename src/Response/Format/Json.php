<?php

namespace Subapp\Http\Response\Format;

use Subapp\Http\Response\Format;

/**
 * Class Json
 * @package Subapp\Http\Response\Format
 */
class Json extends Format
{

  public function process()
  {
    $this->response->setHeader('Content-type', 'application/json');
    $this->response->setContent(json_encode($this->response->getContent(), true));
  }
  
}