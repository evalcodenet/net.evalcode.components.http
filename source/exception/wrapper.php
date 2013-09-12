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

      parent::__construct($namespace, $e_->getMessage(), Http_Exception::INTERNAL_SERVER_ERROR, array(), $e_, $logEnabled_);

      $this->m_exception=$e_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Http_Exception::getStackTrace() \Components\Http_Exception::getStackTrace()
     */
    public function getStackTrace($asString_=false)
    {
      if($asString_)
        return $this->m_exception->getTraceAsString();

      return $this->m_exception->getTrace();
    }

    /**
     * @see \Components\Http_Exception::sendHeader() \Components\Http_Exception::sendHeader()
     */
    public function sendHeader()
    {
      Runtime_Exception::sendHeader();
    }

    /**
     * @see \Components\Runtime_Exception::log() \Components\Runtime_Exception::log()
     */
    public function log()
    {
      if($this->m_logEnabled)
      {
        Log::error($this->m_namespace, $this->message);

        if(($cause=$this->m_exception->getPrevious()) instanceof Runtime_Exception)
          $cause->log();
        else if($cause instanceof \Exception)
          Log::error($this->m_namespace, $cause->getMessage());
      }
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
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
    /**
     * @var \Exception
     */
    private $m_exception;
    //--------------------------------------------------------------------------
  }
?>
