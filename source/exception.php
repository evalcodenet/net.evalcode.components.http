<?php


namespace Components;


  /**
   * Http_Exception
   *
   * @package net.evalcode.components
   * @subpackage http
   *
   * @author evalcode.net
   */
  class Http_Exception extends Runtime_Exception
  {
    // HTTP ERROR CODES
    const FORBIDDEN=403;
    const NOT_FOUND=404;
    const INTERNAL_SERVER_ERROR=500;
    //--------------------------------------------------------------------------


    // PREDEFINED PROPERTIES
    const MESSAGE_FORBIDDEN='HTTP/1.1 403 Forbidden';
    const MESSAGE_NOT_FOUND='HTTP/1.1 404 Not Found';
    const MESSAGE_INTERNAL_SERVER_ERROR='HTTP/1.1 500 Internal Server Error';

    const DEFAULT_NAMESPACE='components/http/exception';
    const DEFAULT_ERROR_CODE=self::INTERNAL_SERVER_ERROR;
    //--------------------------------------------------------------------------


    // PROPERTIES
    public $code;
    public $params=array();
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($namespace_=self::DEFAULT_NAMESPACE,
      $message_=null, $code_=self::DEFAULT_ERROR_CODE, array $params_=array(),
      $cause_=null, $logEnabled_=true)
    {
      if(null===$message_ && isset(self::$m_mapHttpErrorCodes[$code_]))
        $message_=self::$m_mapHttpErrorCodes[$code_];

      parent::__construct($namespace_, $message_, $cause_, $logEnabled_);

      $this->code=$code_;
      $this->params=$params_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $namespace_
     * @param string $message_
     *
     * @return \Components\Http_Exception
     */
    public static function notFound($namespace_, $message_=null)
    {
      return new static($namespace_, $message_, self::NOT_FOUND);
    }

    /**
     * @param string $namespace_
     * @param string $message_
     *
     * @return \Components\Http_Exception
     */
    public static function forbidden($namespace_, $message_=null)
    {
      return new static($namespace_, $message_, self::FORBIDDEN);
    }

    /**
     * @param string $namespace_
     * @param string $message_
     *
     * @return \Components\Http_Exception
     */
    public static function internalError($namespace_, $message_=null)
    {
      return new static($namespace_, $message_, self::INTERNAL_SERVER_ERROR);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param \Components\Io_Mimetype $mimeType_
     *
     * @return string
     */
    public function to(Io_Mimetype $mimeType_)
    {
      if(isset(self::$m_mapMimetypeSerializers[$mimeType_->name()]))
        return $this->{self::$m_mapMimetypeSerializers[$mimeType_->name()]}();

      return $this->__toString();
    }

    /**
     * @return string
     */
    public function toJson()
    {
      return json_encode(array(
        'type'=>get_class($this),
        'code'=>$this->code,
        'namespace'=>$this->getNamespace(),
        'message'=>$this->getMessage(),
        'stack'=>$this->getStackTrace(true),
        'params'=>$this->params
      ));
    }

    /**
     * @return string
     */
    public function toXml()
    {
      // TODO Embed stack trace.
      return sprintf('<?xml version="1.0" encoding="utf-8"?>%6$s<exception>
          <type>%1$s</type>
          <code>%2$s</code>
          <namespace>%3$s</namespace>
          <message>%4$s</message>
          <source>%5$s</source>
        </exception>',
          get_class($this),
          $this->code,
          $this->getNamespace(),
          $this->getMessage(),
          implode(':', array($this->getFile(), $this->getLine())),
          PHP_EOL
      );
    }

    /**
     * @return string
     */
    public function toHtml()
    {
      return sprintf('<?xml encoding="utf-8" version="1.0"?>%6$s<!DOCTYPE HTML>%6$s<html>
          <head>
            <meta charset="utf-8"/>
            <title>[%2$s] %3$s</title>
          </head>
          <body>
            <h1>[%2$s] %3$s</h1>
            <h2>%4$s</h2>
            <pre>%5$s</pre>
          </body>
        </html>',
          self::$m_mapHttpErrorCodes[$this->code],
          $this->getNamespace(),
          $this->getMessage(),
          implode(':', array($this->getFile(), $this->getLine())),
          $this->getStackTrace(true),
          PHP_EOL
      );
    }

    /**
     * @param boolean $asString_
     *
     * @return mixed
     */
    public function getStackTrace($asString_=false)
    {
      if($asString_)
        return $this->getTraceAsString();

      return $this->getTrace();
    }

    public function sendHeader()
    {
      header($this->message, true, $this->code);
      if(Runtime::isManagementAccess() && Debug::active() && Debug::appendToHeaders())
        header('Component-Exception: '.$this->message);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf("%s\n\n%s\n",
        $this->message,
        $this->getTraceAsString()
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_mapMimetypeSerializers=array(
      'application/xml'=>'toXml',
      'application/json'=>'toJson',
      'text/html'=>'toHtml'
    );

    private static $m_mapHttpErrorCodes=array(
      self::INTERNAL_SERVER_ERROR=>self::MESSAGE_INTERNAL_SERVER_ERROR,
      self::FORBIDDEN=>self::MESSAGE_FORBIDDEN,
      self::NOT_FOUND=>self::MESSAGE_NOT_FOUND
    );
    //--------------------------------------------------------------------------
  }
?>
