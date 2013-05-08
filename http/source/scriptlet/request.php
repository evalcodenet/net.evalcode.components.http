<?php


namespace Components;


  /**
   * Http_Scriptlet_Request
   *
   * @package net.evalcode.components
   * @subpackage http.scriptlet
   *
   * @author evalcode.net
   */
  class Http_Scriptlet_Request implements Object
  {
    // PREDEFINED PROPERTIES
    const METHOD_DELETE='DELETE';
    const METHOD_GET='GET';
    const METHOD_HEAD='HEAD';
    const METHOD_OPTIONS='OPTIONS';
    const METHOD_POST='POST';
    const METHOD_PUT='PUT';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct(Uri $uri_)
    {
      $this->m_uri=$uri_;
      $this->m_params=new HashMap($_REQUEST);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return \Components\Io_MimeType
     */
    public function getMimeType()
    {
      if(null===$this->m_mimeType)
      {
        if(!$this->m_mimeType=Io_MimeType::forFileName($this->m_uri->getPath()))
          $this->m_mimeType=Io_MimeType::TEXT_HTML(Io_Charset::UTF_8());
      }

      return $this->m_mimeType;
    }

    /**
     * @return \Components\Uri
     */
    public function getUri()
    {
      return $this->m_uri;
    }

    /**
     * @return \Components\HashMap
     */
    public function getParams()
    {
      return $this->m_params;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
      if(isset($_SERVER['REQUEST_METHOD']))
        return strtoupper($_SERVER['REQUEST_METHOD']);

      return self::METHOD_GET;
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
      return sprintf('%s@%s{uri: %s, mimeType: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_uri,
        $this->m_mimeType
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Io_MimeType
     */
    private $m_mimeType;
    /**
     * @var \Components\Uri
     */
    private $m_uri;
    /**
     * @var \Components\HashMap
     */
    private $m_params;
    //--------------------------------------------------------------------------
  }
?>
