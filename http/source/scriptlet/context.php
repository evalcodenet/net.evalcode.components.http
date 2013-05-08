<?php


namespace Components;


  /**
   * Http_Scriptlet_Context
   *
   * @package net.evalcode.components
   * @subpackage http.scriptlet
   *
   * @author evalcode.net
   */
  class Http_Scriptlet_Context extends Stack implements Object
  {
    // CONSTRUCTION
    public function __construct($contextRoot_='/')
    {
      $this->m_contextRoot=$contextRoot_;
      $this->m_contextUri=Uri::valueOf($contextRoot_);
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Http_Scriptlet_Context
     */
    public static function current()
    {
      return static::head();
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @param \Components\Uri $uri_
     */
    public function dispatch(Uri $uri_)
    {
      ob_start();

      $exception=null;

      try
      {
        $this->dispatchImpl($uri_);
      }
      catch(\Exception $e)
      {
        if(!$e instanceof Http_Exception)
          $e=new Http_Exception_Wrapper($e);

        $exception=$e;
      }

      if(null===$exception)
        $exception=$this->m_response->getException();

      if(null===$exception)
      {
        ob_flush();
      }
      else
      {
        $e->log();
        $e->sendHeader();

        ob_flush();

        echo $e->to($this->m_response->getMimeType());
      }

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


    // OVERRIDES/IMPLEMENTS
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


    private function dispatchImpl(Uri $uri_)
    {
      $this->m_request=new Http_Scriptlet_Request(clone $uri_);

      $mimeType=$this->m_request->getMimeType();
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
            throw new Http_Exception('components/http/scriptlet/context', Http_Exception::NOT_FOUND);
        }
      }

      if(!$component=$uri_->shiftPathParam())
        throw new Http_Exception('components/http/scriptlet/context', Http_Exception::NOT_FOUND);

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
