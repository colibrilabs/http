<?php

    namespace Dez\Http\Response;

    use Dez\Http\Response;

    /**
     * Class Format
     * @package Dez\Http\Response
     */
    abstract class Format implements FormatInterface {

        /**
         * @var Response
         */
        protected $response = null;

        /**
         * @param Response $response
         */
        public function __construct( Response $response = null ) {
            $this->response = $response;
        }

    }