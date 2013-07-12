<?php


namespace Components;


  /**
   * Http_Cli
   *
   * @package net.evalcode.components
   * @subpackage http.cli
   *
   * @author evalcode.net
   */
  class Http_Cli extends Io_Console
  {
    // STATIC ACCESSORS
    /**
     * @return \Components\Http_Cli
     */
    public static function get()
    {
      $instance=new static();

      $instance->addOption('u', true, null, 'dispatch uri', 'uri');
      $instance->addOption('e', true, 'live', 'dispatch environment [dev|alpha|beta..|live]', 'environment');

      $instance->addEmptyOption();
      $instance->addOption('h', false, null, 'print command line instructions', 'help');
      $instance->addOption('v', false, null, 'print program version & license', 'version');

      $instance->setInfo(sprintf('%1$s%3$s%2$s%3$s',
        'Components Runtime HTTP Dispatcher 0.1, net.evalcode.components',
        'Copyright (C) evalcode.net',
        Io::LINE_SEPARATOR_DEFAULT
      ));

      return $instance;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function run()
    {
      if(false===$this->isAttached())
        $this->attach(new Io_Pipe_Stdin(), new Io_Pipe_Stdout(), new Io_Pipe_Stderr());

      $this->open();

      if($this->hasArgument('help') || $this->hasArgument('version') || false===$this->hasArgument('uri'))
      {
        $this->appendInfo();

        if(false===$this->hasArgument('version'))
          $this->appendOptions();

        $this->flush();
        $this->close();

        return;
      }

      if($this->hasArgument('uri'))
      {
        Runtime::addRuntimeErrorHandler(new Http_Error_Handler());

        Environment::push(Environment::valueOf(strtolower($this->getArgument('environment'))));

        $context=Http_Scriptlet_Context::push(new Http_Scriptlet_Context(Environment::uriComponents()));
        $context->dispatch(Uri::valueOf($this->getArgument('uri')));
      }

      $this->close();
    }
    //--------------------------------------------------------------------------
  }
?>
