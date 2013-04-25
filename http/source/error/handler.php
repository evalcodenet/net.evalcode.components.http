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
    // OVERRIDES/IMPLEMENTS
    public function onError(Runtime_ErrorException $e_)
    {
      if(Runtime::isManagementAccess() && Debug::enabled() && Debug::appendToHeaders())
        header('Component-Exception: '.$e_->getMessage());

      if(Environment::isLive())
        return false;

      $mimeType=Http_Scriptlet_Response::getMimeType();
      header('Content-Type: '.$mimeType->name().';charset='.$mimeType->charset()->name());

      Http_Scriptlet_Response::setException(new Http_Exception_Wrapper($e_));

      return true;
    }
    //--------------------------------------------------------------------------
  }
?>
