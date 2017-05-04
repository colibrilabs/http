<?php

namespace Colibri\Http\Response\Format;

use Colibri\Http\Response\Format;

/**
 * Class Json
 * @package Colibri\Http\Response\Format
 */
class Json extends Format
{

  public function process()
  {
    $this->response->setHeader('Content-type', 'application/json');
    $this->response->setContent(json_encode($this->response->getContent(), true));
  }
  
}