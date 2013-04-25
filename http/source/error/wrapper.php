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
    public function __construct(\Exception $e_)
    {
      parent::__construct(parent::DEFAULT_NAMESPACE, $e_->getMessage(), parent::DEFAULT_ERROR_CODE, array(), $e_);

      $this->m_exception=$e_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function getStackTrace($asString_=false)
    {
      if($asString_)
        return $this->m_exception->getTraceAsString();

      return $this->m_exception->getTrace();
    }

    public function __toString()
    {
      return sprintf('%1$s@%2$s{namespace: %3$s, message: %4$s, code: %5$s}',
        get_class($this->m_exception),
        spl_object_hash($this->m_exception),
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
