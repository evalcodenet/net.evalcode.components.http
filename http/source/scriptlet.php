<?php


namespace Components;


  /**
   * Http_Scriptlet
   *
   * @package net.evalcode.components
   * @subpackage http
   *
   * @author evalcode.net
   */
  abstract class Http_Scriptlet implements Object
  {
    // ACCESSORS
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


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components.Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------
  }
?>
