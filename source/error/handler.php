<?php


namespace Components;


  /**
   * Http_Error_Handler
   *
   * @package net.evalcode.components.http
   * @subpackage error
   *
   * @author evalcode.net
   */
  class Http_Error_Handler implements Runtime_Error_Handler
  {
    // OVERRIDES
    /**
     * @see \Components\Runtime_Error_Handler::onError() \Components\Runtime_Error_Handler::onError()
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
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------
  }
?>
