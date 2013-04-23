<?php


  /**
   * Http_Exception_Handler
   *
   * @package net.evalcode.components
   * @subpackage http.exception
   *
   * @author evalcode.net
   */
  class Http_Exception_Handler implements Runtime_Exception_Handler
  {
    // OVERRIDES/IMPLEMENTS
    public function onException(Exception $e_)
    {
      if(!$e_ instanceof Http_Exception)
        $e_=new Http_Exception_Wrapper($e_);

      Http_Scriptlet_Response::setException($e_);

      if(Runtime::isManagementAccess() && Debug::enabled() && Debug::appendToHeaders())
        header('Component-Exception-'.self::$m_count++.': '.$e_->getMessage());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_count=0;
    //--------------------------------------------------------------------------
  }
?>
