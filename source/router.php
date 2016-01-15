<?php


namespace Components;


  /**
   * Http_Router
   *
   * @api
   * @package net.evalcode.components.http
   *
   * @author evalcode.net
   */
  // FIXME Temporary solution - implement real routing.
  class Http_Router implements Object
  {
    // STATIC ACCESSORS
    /**
     * @param string $name_
     * @param string $path_
     * @param string $uri_
     */
    public static function register($name_, $path_, $uri_)
    {
      self::$m_routes[$name_]=[$path_, $uri_];
    }

    /**
     * @param string $name_
     *
     * @return string
     */
    public static function path($name_)
    {
      if(false===isset(self::$m_routes[$name_]))
      {
        Config::http();

        if(false===isset(self::$m_routes[$name_]))
          return null;
      }

      return self::$m_routes[$name_][0];
    }

    /**
     * @param string $name_
     *
     * @return string
     */
    public static function uri($name_)
    {
      if(false===isset(self::$m_routes[$name_]))
      {
        Config::http();

        if(false===isset(self::$m_routes[$name_]))
          return null;
      }

      return self::$m_routes[$name_][1];
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string[]
     */
    private static $m_routes=[];
    //--------------------------------------------------------------------------
  }
?>
