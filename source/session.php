<?php


namespace Components;


  /**
   * Http_Session
   *
   * @api
   * @package net.evalcode.components.http
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Http_Session extends Properties implements Value_String
  {
    // PREDEFINED PROPERTIES
    const NAMESPACE_ROOT='components/http/session';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct(array &$data_, $namespace_=null)
    {
      parent::__construct($data_);

      $this->m_id=session_id();
      $this->m_name=session_name();
      $this->m_namespace=$namespace_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Http_Session
     */
    public static function current()
    {
      if(null===self::$m_instance)
      {
        if(false===isset($_SESSION[self::NAMESPACE_ROOT]))
        {
          if(false===isset($_SESSION))
            session_start();

          if(false===isset($_SESSION[self::NAMESPACE_ROOT]))
            $_SESSION[self::NAMESPACE_ROOT]=[];
        }

        self::$m_instance=new static($_SESSION[self::NAMESPACE_ROOT]);
      }

      return self::$m_instance;
    }

    /**
     * @param string $namespace_
     *
     * @return \Components\Http_Session
     */
    public static function forNamespace($namespace_)
    {
      if(false===isset($_SESSION[self::NAMESPACE_ROOT][$namespace_]))
      {
        if(isset($_SESSION))
        {
          $_SESSION[self::NAMESPACE_ROOT][$namespace_]=[];
        }
        else
        {
          session_start();

          if(false===isset($_SESSION[self::NAMESPACE_ROOT][$namespace_]))
            $_SESSION[self::NAMESPACE_ROOT][$namespace_]=[];
        }
      }

      return new static($_SESSION[self::NAMESPACE_ROOT][$namespace_]);
    }

    /**
     * @param string $namespace_
     * @param string $key_
     *
     * @return boolean
     */
    public static function has($namespace_, $key_)
    {
      if(false===isset($_SESSION[self::NAMESPACE_ROOT][$namespace_][$key_]))
      {
        if(isset($_SESSION))
          return false;

        session_start();

        return isset($_SESSION[self::NAMESPACE_ROOT][$namespace_][$key_]);
      }

      return true;
    }

    /**
     * @param string $namespace_
     * @param string $key_
     *
     * @return mixed
     */
    public static function get($namespace_, $key_)
    {
      if(false===isset($_SESSION[self::NAMESPACE_ROOT][$namespace_][$key_]))
      {
        if(isset($_SESSION))
          return null;

        session_start();

        if(false===isset($_SESSION[self::NAMESPACE_ROOT][$namespace_][$key_]))
          return null;
      }

      return $_SESSION[self::NAMESPACE_ROOT][$namespace_][$key_];
    }

    /**
     * @param string $namespace_
     * @param string $key_
     * @param mixed $value_
     *
     * @return mixed
     */
    public static function set($namespace_, $key_, $value_)
    {
      if(false===isset($_SESSION))
        session_start();

      $_SESSION[self::NAMESPACE_ROOT][$namespace_][$key_]=$value_;
    }

    /**
     * @see \Components\Value_String::valueOf() valueOf
     */
    public static function valueOf($value_)
    {
      return static::forNamespace($value_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return string
     */
    public function getId()
    {
      return session_id();
    }

    /**
     * @return string
     */
    public function getName()
    {
      return session_name();
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
      return $this->m_namespace;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::equals() equals)
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_namespace===$object_->m_namespace;

      return false;
    }

    /**
     * @see \Components\Value_String::value() value
     */
    public function value()
    {
      return $this->m_namespace;
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      if(null===$this->m_namespace)
      {
        return sprintf('%s@%s{id: %s, name: %s}',
          __CLASS__, $this->hashCode(), $this->m_id, $this->m_name
        );
      }

      return sprintf('%s@%s{id: %s, name: %s, namespace: %s}',
        __CLASS__, $this->hashCode(), $this->m_id, $this->m_name, $this->m_namespace
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Http_Session
     */
    private static $m_instance;

    /**
     * @var string
     */
    private $m_id;
    /**
     * @var string
     */
    private $m_name;
    /**
     * @var string
     */
    private $m_namespace;
    //--------------------------------------------------------------------------
  }
?>
