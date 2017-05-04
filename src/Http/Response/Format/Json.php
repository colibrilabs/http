<?php

namespace Dez\Http\Response\Format;

use Dez\Http\Response\Format;

class Json extends Format
{

    public function process()
    {
        $this->response->setHeader('Content-type', 'application/json');
        $this->response->setContent(json_encode($this->response->getContent(), true));
    }

}