<?php


namespace Components;


  /**
   * Http_Exception_Wrapper
   *
   * @package net.evalcode.components
   * @subpackage http.exception
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

      parent::__construct($namespace, Http_Exception::INTERNAL_SERVER_ERROR, $e_->getMessage(), array(), $e_, $logEnabled_);

      $this->m_exception=$e_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components.Http_Exception::getStackTrace()
     */
    public function getStackTrace($asString_=false)
    {
      if($asString_)
        return $this->m_exception->getTraceAsString();

      return $this->m_exception->getTrace();
    }

    /**
     * (non-PHPdoc)
     * @see Components.Runtime_Exception::log()
     */
    public function log()
    {
      if($this->m_logEnabled)
        Log::error($this->m_namespace, $this->message);
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%1$s@%2$s{namespace: %3$s, message: %4$s, code: %5$s}',
        __CLASS__,
        $this->hashCode(),
        $this->getNamespace(),
        $this->getMessage(),
        $this->code
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
