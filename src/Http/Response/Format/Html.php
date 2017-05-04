<?php

namespace Dez\Http\Response\Format;

use Dez\Http\Response\Format;

class Html extends Format
{

    public function process()
    {
        $this->response->setHeader('Content-type', 'text/html');
    }

}