<?php

namespace Subapp\Http\Response\Format;

use Subapp\Http\Response\Format;

/**
 * Class Html
 * @package Subapp\Http\Response\Format
 */
class Html extends Format
{

  public function process()
  {
    $this->response->setHeader('Content-type', 'text/html');
  }
  
}