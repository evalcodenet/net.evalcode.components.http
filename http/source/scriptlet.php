<?php


  /**
   * Http_Scriptlet
   *
   * @package net.evalcode.components
   * @subpackage http
   *
   * @author evalcode.net
   */
  abstract class Http_Scriptlet
  {
    // ACCESSORS/MUTATORS
    public function dispatch(array $parameters_)
    {
      $method=isset($_SERVER['REQUEST_METHOD'])?strtolower($_SERVER['REQUEST_METHOD']):'get';

      if(method_exists($this, $method))
      {
        if(count($parameters_))
          return call_user_func_array(array($this, $method), $parameters_);

        return $this->{$method}();
      }

      throw new Http_Exception('http/scriptlet', 'Illegal request - method not implemented.');
    }
    //--------------------------------------------------------------------------
  }
?>
