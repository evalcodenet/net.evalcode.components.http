<?php


namespace Components;


  /**
   * Http_Scriptlet_Response
   *
   * @package net.evalcode.components
   * @subpackage http.scriptlet
   *
   * @author evalcode.net
   */
  class Http_Scriptlet_Response implements Object
  {
    // CONSTRUCTION
    public function __construct(Io_MimeType $mimeType_)
    {
      $this->m_mimeType=$mimeType_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Io_MimeType
     */
    public function getMimeType()
    {
      return $this->m_mimeType;
    }

    /**
     * @param \Components\Io_MimeType $mimeType_
     */
    public function setMimeType(Io_MimeType $mimeType_)
    {
      $this->m_mimeType=$mimeType_;
    }

    public function getParameters()
    {
      return $this->m_parameters;
    }

    public function addParameter($name_, $value_)
    {
      $this->m_parameters[$name_]=$value_;
    }

    /**
     * @return boolean
     */
    public function hasException()
    {
      return null!==$this->m_exception;
    }

    /**
     * @return \Components\Http_Exception
     */
    public function getException()
    {
      return $this->m_exception;
    }

    /**
     * @param \Components\Http_Exception $exception_
     */
    public function setException(Http_Exception $exception_)
    {
      $this->m_exception=$exception_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function hashCode()
    {
      return object_hash($this);
    }

    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

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
    private $m_parameters=array();
    /**
     * @var \Components\Http_Exception
     */
    private $m_exception;
    /**
     * @var \Components\Io_MimeType
     */
    private $m_mimeType;
    //--------------------------------------------------------------------------
  }
?>
