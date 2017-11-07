<?php

namespace Codeages\PhalconBiz\Event;

use Symfony\Component\EventDispatcher\Event;

class WebEvent extends Event
{
    protected $app;

    protected $request;

    public function __construct($app, $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    public function getDI()
    {
        return $this->app->getDI();
    }

    public function getApp()
    {
        return $this->app;
    }
}
