<?php


namespace Components;


  /**
   * Http_Scriptlet
   *
   * @api
   * @package net.evalcode.components.http
   *
   * @author evalcode.net
   */
  class Http_Scriptlet implements Object
  {
    // PROPERTIES
    /**
     * @var \Components\Http_Scriptlet_Request
     */
    public $request;
    /**
     * @var \Components\Http_Scriptlet_Response
     */
    public $response;
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    public static function serve($pattern_=null, $scriptlet_=null)
    {
      if(null===$scriptlet_)
        $scriptlet_=get_called_class();

      if(null===$pattern_)
        self::$m_default=$scriptlet_;
      else
        self::$m_routes['/^'.str_replace('/', '\\/', $pattern_).'$/i']=$scriptlet_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param \Components\Http_Scriptlet_Context $context_
     * @param \Components\Uri $uri_
     */
    public static function dispatch(Http_Scriptlet_Context $context_, Uri $uri_)
    {
      if(__CLASS__===get_called_class())
      {
        $segments=$uri_->getPathParams(true);
        $count=count($segments);

        $params=array();
        for($i=$count; 0<$i; $i--)
        {
          $path=implode('/', $segments);
          foreach(self::$m_routes as $pattern=>$scriptlet)
          {
            $matches=array();
            if(1===preg_match($pattern, $path, $matches))
            {
              $uri_->setPathParams($params);
              foreach($segments as $segment)
                $context_->getContextUri()->pushPathParam($segment);

              $scriptlet::dispatch($context_, $uri_);

              return;
            }
          }

          array_unshift($params, array_pop($segments));
        }

        if(null!==($scriptlet=self::$m_default))
        {
          $scriptlet::dispatch($context_, $uri_);

          return;
        }
      }
      else
      {
        $scriptlet=new static();
        $scriptlet->request=$context_->getRequest();
        $scriptlet->response=$context_->getResponse();

        $method=$scriptlet->request->getMethod();

        if(method_exists($scriptlet, strtolower($method)))
          return $scriptlet->$method();
      }

      throw Http_Exception::notFound('http/scriptlet');
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
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
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_default;
    private static $m_routes=array();
    //--------------------------------------------------------------------------
  }
?>
