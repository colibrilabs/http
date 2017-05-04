<?php

    namespace Dez\Http;

    use Dez\DependencyInjection\ContainerInterface;
    use Dez\DependencyInjection\InjectableInterface;
    use Dez\Http\Cookies\Cookie;

    /**
     * Class Cookies
     * @package Dez\Http
     */
    class Cookies implements InjectableInterface, CookiesInterface {


        /**
         * @var ContainerInterface
         */
        protected $di;

        /**
         * @var \Dez\Http\Cookies\Cookie[]
         */
        protected $cookies  = [];

        /**
         * Constructor
        */
        public function __construct() { }

        /**
         * @return ContainerInterface
         */
        public function getDi() {
            return $this->di;
        }

        /**
         * @param ContainerInterface $di
         * @return static
         */
        public function setDi( ContainerInterface $di ) {
            $this->di = $di;
            return $this;
        }

        /**
         * @param $name
         * @param $value
         * @param int $expired
         * @param string $path
         * @param null|string $domain
         * @param null|bool $secure
         * @param null|bool $httpOnly
         * @return $this
         */
        public function set( $name, $value = '', $expired = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false ) {
            $this->cookies[ $name ] = new Cookie( $name, $value, $expired, $path, $domain, $secure, $httpOnly );
            return $this;
        }

        /**
         * @param $name string
         * @param $default string
         * @return Cookie
         */
        public function get( $name, $default = '' ) {

            if( $this->has( $name ) ) {
                return $this->cookies[ $name ];
            }

            $cookie     = new Cookie( $name );
            $this->cookies[ $name ] = $cookie;

            return $cookie;
        }

        /**
         * @param $name string
         * @return bool
         */
        public function has( $name ) {
            return isset( $this->cookies[ $name ], $_COOKIE[ $name ] );
        }

        /**
         * @param $name string
         * @return $this
         */
        public function delete( $name ) {

            if( $this->has( $name ) ) {
                $this->get( $name )->delete();
            }

            return $this;
        }


        /**
         * @return $this
         */
        public function send() {

            if( ! headers_sent() ) {
                foreach( $this->cookies as $cookie ) {
                    $cookie->send();
                }
            }

            return $this;

        }

    }