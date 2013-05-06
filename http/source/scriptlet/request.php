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
  class Http_Scriptlet_Request
  {
    // STATIC ACCESSORS
    /**
     * @return \Components\Io_MimeType
     */
    public static function getMimeType()
    {
      if(null===self::$m_mimeType)
      {
        if(isset($_SERVER['REQUEST_URI']))
          self::$m_mimeType=Io_MimeType::forFileName($_SERVER['REQUEST_URI']);

        if(!self::$m_mimeType)
          self::$m_mimeType=Io_MimeType::TEXT_HTML(Io_Charset::UTF_8());
      }

      return self::$m_mimeType;
    }

    /**
     * @return \Components\Uri
     */
    public static function getUri()
    {
      if(null===self::$m_uri)
      {
        if(isset($_SERVER['REQUEST_URI']))
          self::$m_uri=Uri::valueOf($_SERVER['REQUEST_URI']);
        else
          self::$m_uri=Uri::createEmpty();
      }

      return self::$m_uri;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Io_MimeType
     */
    private static $m_mimeType;
    /**
     * @var \Components\Uri
     */
    private static $m_uri;
    //--------------------------------------------------------------------------
  }
?>
