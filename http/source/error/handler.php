<?php


namespace Components;


  /**
   * Http_Error_Handler
   *
   * @package net.evalcode.components
   * @subpackage http.error
   *
   * @author evalcode.net
   */
  class Http_Error_Handler implements Runtime_Error_Handler
  {
    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components\Runtime_Error_Handler::onError()
     */
    public function onError(Runtime_ErrorException $e_)
    {
      if($response=Http_Scriptlet_Context::current()->getResponse())
      {
        $response->setException(new Http_Exception_Wrapper($e_));

        return true;
      }

      return false;
    }

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
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------
  }
?>
