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
    const INTERNAL=500;
    //--------------------------------------------------------------------------


    // PREDEFINED PROPERTIES
    const DEFAULT_NAMESPACE='runtime/http';
    const DEFAULT_MESSAGE='Internal Error';
    const DEFAULT_ERROR_CODE=self::INTERNAL;
    //--------------------------------------------------------------------------


    // PROPERTIES
    public $code;
    public $params=array();
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($namespace_=self::DEFAULT_NAMESPACE,
      $message_=self::DEFAULT_MESSAGE, $code_=self::DEFAULT_ERROR_CODE,
      array $params_=array(), $cause_=null, $logEnabled_=true)
    {
      parent::__construct($namespace_, $message_, $cause_, $logEnabled_);

      $this->code=$code_;
      $this->params=$params_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param \Components\Io_MimeType $mimeType_
     *
     * @return string
     */
    public function to(Io_MimeType $mimeType_)
    {
      if(isset(self::$m_mapMimeTypeSerializers[$mimeType_->name()]))
        return $this->{self::$m_mapMimeTypeSerializers[$mimeType_->name()]}();

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
        'trace'=>$this->getStackTrace(true),
        'params'=>$this->params
      ));
    }

    /**
     * @return string
     */
    public function toXml()
    {
      // TODO Embed stack trace.
      return sprintf('<?xml encoding="utf-8" version="1.0"?>%6$s
        <exception>%6$s
          <type>%1$s</type>%6$s
          <code>%2$s</code>%6$s
          <namespace>%3$s</namespace>%6$s
          <message>%4$s</message>%6$s
          <source>%5$s</source>%6$s
        </exception>',
          get_class($this),
          self::$m_mapHttpErrorCodes[$this->code],
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
      return sprintf('<?xml encoding="utf-8" version="1.0"?>%6$s
        <!DOCTYPE HTML>%6$s
        <html>
          <head>
            <meta charset="utf-8"/>
            <title>[%2$s] %3$s</title>
          </head>
          <body>
            <h1>%1$s</h1>
            <h2>[%2$s] %3$s</h2>
            <h3>%4$s</h3>
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
     * @return string
     */
    public function toString()
    {
      return (string)$this;
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
      if(isset(self::$m_mapHttpErrorCodes[$this->code]))
        header(self::$m_mapHttpErrorCodes[$this->code], true, $this->code);
      else
        header(self::DEFAULT_MESSAGE, true, $this->code);

      if(Runtime::isManagementAccess() && Debug::enabled() && Debug::appendToHeaders())
        header('Component-Exception: '.$this->message);
    }

    /**
     * @return string
     */
    public function getFriendlyMessage()
    {
      if(isset(self::$m_mapHttpErrorCodes[$this->code]))
        return self::$m_mapHttpErrorCodes[$this->code];

      return self::DEFAULT_MESSAGE;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components.Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{namespace: %s, message: %s, code: %s}',
        __CLASS__,
        object_hash($this),
        $this->getNamespace(),
        $this->getMessage(),
        $this->code
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_mapMimeTypeSerializers=array(
      'application/json'=>'toJson',
      'text/html'=>'toHtml',
      'text/plain'=>'toString'
    );

    private static $m_mapHttpErrorCodes=array(
      self::INTERNAL=>'Internal Server Error',
      self::FORBIDDEN=>'Forbidden',
      self::NOT_FOUND=>'Not Found'
    );
    //--------------------------------------------------------------------------
  }
?>
