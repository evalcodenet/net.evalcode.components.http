<?php


  /**
   * Http_Scriptlet_Response
   *
   * @package net.evalcode.components
   * @subpackage http
   *
   * @author evalcode.net
   */
  class Http_Scriptlet_Response
  {
    // STATIC ACCESSORS
    /**
     * @return Io_MimeType
     */
    public static function getMimeType()
    {
      return self::$m_mimeType;
    }

    /**
     * @param Io_MimeType $mimeType_
     */
    public static function setMimeType(Io_MimeType $mimeType_)
    {
      self::$m_mimeType=$mimeType_;
    }

    public static function getHeaders()
    {
      return self::$m_headers;
    }

    public static function setHeader($name_, $value_)
    {
      self::$m_headers[$name_]=$value_;
    }

    public static function getContent()
    {
      return self::$m_content;
    }

    public static function setContent($content_)
    {
      self::$m_content=$content_;
    }

    public static function addContent($content_)
    {
      self::$m_content.=$content_;
    }

    public static function getScripts()
    {
      return self::$m_scripts;
    }

    public static function addScript($script_)
    {
      self::$m_scripts[md5($script_)]=$script_;
    }

    public static function getParameters()
    {
      return self::$m_parameters;
    }

    public static function addParameter($name_, $value_)
    {
      self::$m_parameters[$name_]=$value_;
    }

    public static function getException()
    {
      return self::$m_exception;
    }

    public static function hasException()
    {
      return null!==self::$m_exception;
    }

    public static function setException(Exception $exception_)
    {
      self::$m_exception=$exception_;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_headers=array();
    private static $m_scripts=array();
    private static $m_parameters=array();
    private static $m_content='';
    /**
     * @var Http_Exception
     */
    private static $m_exception;
    /**
     * @var Io_MimeType
     */
    private static $m_mimeType;
    //--------------------------------------------------------------------------
  }
?>
