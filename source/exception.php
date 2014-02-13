<?php


namespace Components;


  /**
   * Http_Exception
   *
   * @api
   * @package net.evalcode.components.http
   *
   * @author evalcode.net
   */
  class Http_Exception extends Runtime_Exception_Abstract
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

    const DEFAULT_NAMESPACE='http/exception';
    const DEFAULT_ERROR_CODE=self::INTERNAL_SERVER_ERROR;
    //--------------------------------------------------------------------------


    // PROPERTIES
    public $code;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($namespace_=self::DEFAULT_NAMESPACE,
      $message_=null, $code_=self::DEFAULT_ERROR_CODE, $cause_=null, $logEnabled_=true)
    {
      if(null===$message_ && isset(self::$m_mapHttpErrorCodes[$code_]))
        $message_=self::$m_mapHttpErrorCodes[$code_];

      parent::__construct($namespace_, $message_, $cause_, $logEnabled_);

      $this->code=$code_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * Sends header: HTTP/1.1 404 Not Found
     */
    public static function sendHeaderNotFound()
    {
      header(self::MESSAGE_NOT_FOUND, true, self::NOT_FOUND);
    }

    /**
     * Sends header: HTTP/1.1 403 Forbidden
     */
    public static function sendHeaderForbidden()
    {
      header(self::MESSAGE_FORBIDDEN, true, self::FORBIDDEN);
    }

    /**
     * Sends header: HTTP/1.1 500 Internal Server Error
     */
    public static function sendHeaderInternalError()
    {
      header(self::MESSAGE_INTERNAL_SERVER_ERROR, true, self::INTERNAL_SERVER_ERROR);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    public function sendHeader()
    {
      header($this->message, true, $this->code);

      $verbose=Runtime::isManagementAccess() && Debug::active();

      if($previous=$this->getPrevious())
        exception_header($previous, $verbose, $verbose);
    }

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
    public function toHtml()
    {
      return sprintf('<?xml encoding="utf-8" version="1.0"?>%4$s
        <!DOCTYPE HTML>%4$s
        <html>
          <head>
            <meta charset="utf-8"/>
            <title>%1$s</title>
          </head>
          <body>
            <h1>%1$s</h1>
            <h2>[%2$s] %3$s</h2>
          </body>
        </html>',
          self::$m_mapHttpErrorCodes[$this->code],
          $this->getNamespace(),
          $this->getMessage(),
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
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Runtime_Exception::toArray() toArray
     */
    public function toArray($includeStackTrace_=false, $stackTraceAsArray_=false)
    {
      $asArray=[
        'code'=>$this->code,
        'namespace'=>$this->getNamespace(),
        'message'=>$this->getMessage()
      ];

      if($includeStackTrace_)
        $asArray['stack']=$stackTraceAsArray_?[]:'';

      return $asArray;
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      if(!$file=$this->getFile())
        $file='internal';
      if(!$line=$this->getLine())
        $line=0;

      return sprintf(
        "\n\n#  msg : %s\n#  uri : %s\n%s%s# \n#  %s(%d)\n%s\n",
          $this->message,
          Uri::currentHttpRequestUri(),
          isset($_SERVER['HTTP_REFERER'])?"#  host: $_SERVER[REMOTE_ADDR]\n":'',
          isset($_SERVER['HTTP_REFERER'])?"#  ref : $_SERVER[HTTP_REFERER]\n":'',
          $file,
          $line,
          $this->getTraceAsString()
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_mapMimetypeSerializers=[
      'application/xml'=>'toXml',
      'application/json'=>'toJson',
      'text/html'=>'toHtml'
    ];

    private static $m_mapHttpErrorCodes=[
      self::INTERNAL_SERVER_ERROR=>self::MESSAGE_INTERNAL_SERVER_ERROR,
      self::FORBIDDEN=>self::MESSAGE_FORBIDDEN,
      self::NOT_FOUND=>self::MESSAGE_NOT_FOUND
    ];
    //--------------------------------------------------------------------------
  }
?>
