<?php

namespace Colibri\Http\Response\Format;

use Colibri\Http\Response\Format;

/**
 * Class Html
 * @package Colibri\Http\Response\Format
 */
class Html extends Format
{

  public function process()
  {
    $this->response->setHeader('Content-type', 'text/html');
  }
  
}