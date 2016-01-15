<?php


namespace Components;


  /**
   * Http_Scriptlet_Response
   *
   * @api
   * @package net.evalcode.components.http
   * @subpackage scriptlet
   *
   * @author evalcode.net
   */
  class Http_Scriptlet_Response implements Object
  {
    // CONSTRUCTION
    public function __construct(Io_Mimetype $mimeType_)
    {
      $this->m_mimeType=$mimeType_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Io_Mimetype
     */
    public function getMimetype()
    {
      return $this->m_mimeType;
    }

    /**
     * @param \Components\Io_Mimetype $mimeType_
     */
    public function setMimetype(Io_Mimetype $mimeType_)
    {
      $this->m_mimeType=$mimeType_;
    }

    /**
     * @return mixed[][]
     */
    public function getParameters()
    {
      return $this->m_parameters;
    }

    /**
     * @return mixed[]
     */
    public function getParameter($name_)
    {
      if(false===isset($this->m_parameters[$name_]))
        return null;

      return $this->m_parameters[$name_];
    }

    /**
     * @param string $name_
     * @param string $value_
     */
    public function setParameter($name_, $value_)
    {
      $this->m_parameters[$name_]=$value_;
    }

    /**
     * @param string $name_
     * @param string $key_
     * @param string $value_
     */
    public function addParameter($name_, $key_, $value_)
    {
      if(false===isset($this->m_parameters[$name_]))
        $this->m_parameters[$name_]=[];

      $this->m_parameters[$name_][$key_]=$value_;
    }

    /**
     * @return boolean
     */
    public function hasExceptions()
    {
      return isset($this->m_parameters['e']);
    }

    /**
     * @return \Components\Http_Exception
     */
    public function getExceptions()
    {
      return $this->m_parameters['e'];
    }

    /**
     * @return \Components\Http_Exception
     */
    public function addException(\Exception $exception_)
    {
      $this->m_parameters['e'][\math\hasho_md5($exception_)]=$exception_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return \math\hasho($this);
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
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{mimeType: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_mimeType
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_parameters=[];
    /**
     * @var \Components\Io_Mimetype
     */
    private $m_mimeType;
    //--------------------------------------------------------------------------
  }
?>
