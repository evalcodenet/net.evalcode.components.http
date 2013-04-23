<?php


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


    // ACCESSORS/MUTATORS
    public function to(Io_MimeType $mimeType_)
    {
      if(isset(self::$m_mapMimeTypeSerializers[$mimeType_->name()]))
        return $this->{self::$m_mapMimeTypeSerializers[$mimeType_->name()]}();

      return $this->__toString();
    }

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

    public function toString()
    {
      return (string)$this;
    }

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
    }

    public function getFriendlyMessage()
    {
      if(isset(self::$m_mapHttpErrorCodes[$this->code]))
        return self::$m_mapHttpErrorCodes[$this->code];

      return self::DEFAULT_MESSAGE;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function __toString()
    {
      return sprintf('%1$s@%2$s{namespace: %3$s, message: %4$s, code: %5$s}',
        get_class($this),
        spl_object_hash($this),
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
