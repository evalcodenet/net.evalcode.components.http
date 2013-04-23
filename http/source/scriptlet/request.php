<?php


  /**
   * Http_Scriptlet_Request
   *
   * @package net.evalcode.components
   * @subpackage http
   *
   * @author evalcode.net
   */
  class Http_Scriptlet_Request
  {
    // STATIC ACCESSORS
    /**
     * @return Io_MimeType
     */
    public static function getMimeType()
    {
      if(null===self::$m_mimeType)
      {
        if(isset($_SERVER['REQUEST_URI']))
        {
          $requestUri=@parse_url($_SERVER['REQUEST_URI']);
          $path=isset($requestUri['path'])?$requestUri['path']:'';

          self::$m_mimeType=Io_MimeType::forFileName($path);
        }

        if(!self::$m_mimeType)
          self::$m_mimeType=Io_MimeType::TEXT_HTML(Io_Charset::UTF_8());
      }

      return self::$m_mimeType;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var Io_MimeType
     */
    private static $m_mimeType;
    //--------------------------------------------------------------------------
  }
?>
