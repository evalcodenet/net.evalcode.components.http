<?php


namespace Components;


  /**
   * Http_Exception_Wrapper
   *
   * @package net.evalcode.components.http
   * @subpackage exception
   *
   * @author evalcode.net
   */
  class Http_Exception_Wrapper extends Http_Exception
  {
    // CONSTRUCTION
    public function __construct(\Exception $e_, $logEnabled_=true)
    {
      if($e_ instanceof Runtime_Exception || $e_ instanceof Runtime_ErrorException)
        $namespace=$e_->getNamespace();
      else
        $namespace=Http_Exception::DEFAULT_NAMESPACE;

      parent::__construct($namespace, $e_->getMessage(), Http_Exception::INTERNAL_SERVER_ERROR, [], $e_, $logEnabled_);

      $this->m_exception=$e_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Http_Exception::getStackTrace() getStackTrace
     */
    public function getStackTrace($asString_=false)
    {
      if($asString_)
        return $this->m_exception->getTraceAsString();

      return $this->m_exception->getTrace();
    }

    /**
     * @see \Components\Http_Exception::sendHeader() sendHeader
     */
    public function sendHeader()
    {
      header($this->message, true, $this->code);
      exception_header($this->m_exception);
    }

    /**
     * @see \Components\Runtime_Exception::log() log
     */
    public function log()
    {
      if($this->m_logEnabled)
      {
        Log::error($this->m_namespace, '[%s] %s%s',
          object_hash_md5($this->m_exception),
          get_class($this->m_exception),
          $this->m_exception
        );

        if($previous=$this->m_exception->getPrevious())
          exception_log($previous);
      }
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      if(!$file=$this->m_exception->getFile())
        $file='internal';
      if(!$line=$this->m_exception->getLine())
        $line=0;

      return sprintf("\n\n#0 %s\n#0 %s(%d)\n#0\n%s\n",
        $this->m_exception->getMessage(),
        $file,
        $line,
        $this->m_exception->getTraceAsString()
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Exception
     */
    private $m_exception;
    //--------------------------------------------------------------------------
  }
?>
