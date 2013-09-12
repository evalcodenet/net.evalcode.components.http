<?php


namespace Components;


  /**
   * Http_Scriptlet_Context
   *
   * @api
   * @package net.evalcode.components.http
   * @subpackage scriptlet
   *
   * @author evalcode.net
   */
  class Http_Scriptlet_Context implements Object
  {
    // CONSTRUCTION
    public function __construct($contextRoot_='/')
    {
      $this->m_contextRoot=$contextRoot_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Http_Scriptlet_Context
     */
    public static function current()
    {
      return self::$m_current;
    }

    /**
     * @param \Components\Http_Scriptlet_Context $context_
     *
     * @return \Components\Http_Scriptlet_Context
     */
    public static function push(Http_Scriptlet_Context $context_)
    {
      if(null!==self::$m_current)
        array_push(self::$m_stack, self::$m_current);

      self::$m_current=$context_;
      self::$m_count++;

      return $context_;
    }

    /**
     * @return \Components\Http_Scriptlet_Context
     */
    public static function pop()
    {
      $current=self::$m_current;

      if(0<self::$m_count)
      {
        self::$m_current=array_pop(self::$m_stack);
        self::$m_count--;
      }

      return $current;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param \Components\Uri $uri_
     */
    public function dispatch(Uri $uri_, $method_=null)
    {
      ob_start();

      $content=null;
      $exception=null;

      try
      {
        $this->dispatchImpl($uri_, $method_);

        $content=ob_get_clean();
        $exception=$this->m_response->getException();
      }
      catch(\Exception $e)
      {
        $exception=$e;
      }

      Debug::appendToHeaders();

      if(null===$exception)
      {
        echo $content;
      }
      else
      {
        exception_log($exception);
        exception_header($exception);

        if(Environment::isLive())
        {
          // TODO Custom 4xx, 5xx etc. error pages.
          exit;
        }

        echo $content;

        Debug::dumpException($exception);
      }

      if($this->m_response->getMimetype()->isTextHtml())
        Debug::appendToBody();

      if(session_id())
        session_write_close();
    }

    /**
     * @return \Components\Http_Scriptlet_Request
     */
    public function getRequest()
    {
      return $this->m_request;
    }

    /**
     * @return \Components\Http_Scriptlet_Response
     */
    public function getResponse()
    {
      return $this->m_response;
    }

    /**
     * @return \Components\Uri
     */
    public function getContextUri()
    {
      return $this->m_contextUri;
    }

    /**
     * @return string
     */
    public function getContextRoot()
    {
      return $this->m_contextRoot;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    public function hashCode()
    {
      return string_hash($this->m_contextRoot);
    }

    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_contextRoot===$object_->m_contextRoot;

      return false;
    }

    public function __toString()
    {
      return sprintf('%s@%s{contextRoot: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_contextRoot
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Http_Scriptlet_Context[]
     */
    private static $m_stack=array();
    /**
     * @var integer
     */
    private static $m_count=0;
    /**
     * @var \Components\Http_Scriptlet_Context
     */
    private static $m_current;
    /**
     * @var string
     */
    private $m_contextRoot;
    /**
     * @var \Components\Uri
     */
    private $m_contextUri;
    /**
     * @var \Components\Http_Scriptlet_Request
     */
    private $m_request;
    /**
     * @var \Components\Http_Scriptlet_Response
     */
    private $m_response;
    //-----


    private function dispatchImpl(Uri $uri_, $method_=null)
    {
      $this->m_contextUri=Uri::valueOf($this->m_contextRoot);
      $this->m_request=new Http_Scriptlet_Request(clone $uri_);

      if(null!==$method_)
        $this->m_request->setMethod($method_);

      $mimeType=$this->m_request->getMimetype();
      $this->m_response=new Http_Scriptlet_Response($mimeType);

      header('Content-Type: '.$mimeType->name().';charset='.$mimeType->charset()->name());

      if('/'!==$this->m_contextRoot)
      {
        $contextRoot=rtrim($this->m_contextRoot, '/');
        $contextRoot=ltrim($contextRoot, '/');
        $contextRootSegments=explode('/', $contextRoot);

        while(count($contextRootSegments))
        {
          if(array_shift($contextRootSegments)!==$uri_->shiftPathParam())
            throw Http_Exception::notFound('http/scriptlet/context');
        }
      }

      if(!$component=$uri_->shiftPathParam())
        throw Http_Exception::notFound('http/scriptlet/context');

      $this->m_contextUri->pushPathParam($component);

      if($this->m_contextUri->hasFileExtension())
        Config::get($this->m_contextUri->getFilename(true));
      else
        Config::get($component);

      Http_Scriptlet::dispatch($this, $uri_);
    }
    //--------------------------------------------------------------------------
  }
?>
