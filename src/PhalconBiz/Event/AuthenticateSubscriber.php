<?php

namespace Codeages\PhalconBiz\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthenticateSubscriber implements EventSubscriberInterface
{
    public function onRequest(GetResponseEvent $event)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            WebEvents::REQUEST => 'onRequest',
        ];
    }
}
