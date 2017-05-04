<?php

    namespace Dez\Http\Cookies;

    use Dez\DependencyInjection\ContainerInterface;
    use Dez\DependencyInjection\InjectableInterface;
    use Dez\Http\Response;

    /**
     * Class Cookie
     * @package Dez\Http\Cookies
     */
    class Cookie implements InjectableInterface, CookieInterface {

        /**
         * @var \Dez\DependencyInjection\ContainerInterface
         */
        protected $di;

        /**
         * @var bool
         */
        protected $sent     = false;

        /**
         * @var string
         */
        protected $name     = '';

        /**
         * @var string
         */
        protected $value    = '';

        /**
         * @var string
         */
        protected $expired  = '';

        /**
         * @var string
         */
        protected $path     = '/';

        /**
         * @var string
         */
        protected $domain   = '';

        /**
         * @var bool
         */
        protected $secure   = false;

        /**
         * @var bool
         */
        protected $httpOnly = false;

        /**
         * @param $name
         * @param string $value
         * @param int $expired
         * @param string $path
         * @param string $domain
         * @param bool|false $secure
         * @param bool|false $httpOnly
         */
        public function __construct( $name, $value = '', $expired = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false ) {
            $this->setName( $name )->setValue( $value )->setExpired( $expired )
                ->setPath( $path )->setDomain( $domain )
                ->setSecure( $secure )->setHttpOnly( $httpOnly );
        }

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
         * @return boolean
         */
        public function isSent() {
            return $this->sent;
        }

        /**
         * @param boolean $sent
         * @return static
         */
        public function setSent( $sent ) {
            $this->sent = $sent;
            return $this;
        }

        /**
         * @return string
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param string $name
         * @return static
         */
        public function setName( $name ) {
            $this->name = $name;
            return $this;
        }

        /**
         * @return string
         */
        public function getValue() {

            if( ! $this->value && isset( $_COOKIE[ $this->getName() ] ) ) {
                $this->setValue( $_COOKIE[ $this->getName() ] );
            }

            return $this->value;
        }

        /**
         * @param string $value
         * @return static
         */
        public function setValue( $value ) {
            $this->value = $value;
            return $this;
        }

        /**
         * @return string
         */
        public function getExpired() {
            return $this->expired;
        }

        /**
         * @param string $expired
         * @return static
         */
        public function setExpired( $expired ) {
            $this->expired = date( 'D, d-M-Y H:i:s', $expired ) . ' GMT';
            return $this;
        }

        /**
         * @return string
         */
        public function getPath() {
            return $this->path;
        }

        /**
         * @param string $path
         * @return static
         */
        public function setPath( $path ) {
            $this->path = $path;
            return $this;
        }

        /**
         * @return string
         */
        public function getDomain() {
            return $this->domain;
        }

        /**
         * @param string $domain
         * @return static
         */
        public function setDomain( $domain ) {
            $this->domain = $domain;
            return $this;
        }

        /**
         * @return boolean
         */
        public function isSecure() {
            return $this->secure;
        }

        /**
         * @param boolean $secure
         * @return static
         */
        public function setSecure( $secure ) {
            $this->secure = $secure;
            return $this;
        }

        /**
         * @return boolean
         */
        public function isHttpOnly() {
            return $this->httpOnly;
        }

        /**
         * @param boolean $httpOnly
         * @return static
         */
        public function setHttpOnly( $httpOnly ) {
            $this->httpOnly = $httpOnly;
            return $this;
        }

        /**
         * @return $this
         */
        public function delete() {

            setcookie(
                $this->getName(),
                null,
                time() - 86400,
                $this->getPath(),
                $this->getDomain(),
                $this->isSecure(),
                $this->isHttpOnly()
            );

            $this->setValue( null );

            return $this;

        }

        /**
         * @return $this
         */
        public function send() {

            $expire     = strtotime( $this->getExpired() );

            if( ! $this->isSent() && $expire > time() ) {

                setcookie(
                    $this->getName(),
                    $this->getValue(),
                    $expire,
                    $this->getPath(),
                    $this->getDomain(),
                    $this->isSecure(),
                    $this->isHttpOnly()
                );

                $this->setSent( true );

            }

            return $this;

        }

        /**
         * @return string
         */
        public function __toString() {
            return $this->getValue();
        }


    }