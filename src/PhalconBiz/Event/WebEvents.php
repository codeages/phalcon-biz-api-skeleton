<?php

namespace Codeages\PhalconBiz\Event;

final class WebEvents
{
    /**
     * The REQUEST event occurs at the very beginning of request
     * dispatching.
     *
     * This event allows you to create a response for a request before any
     * other code in the framework is executed.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseEvent")
     *
     * @var string
     */
    const REQUEST = 'web.request';

    /**
     * The EXCEPTION event occurs when an uncaught exception appears.
     *
     * This event allows you to create a response for a thrown exception or
     * to modify the thrown exception.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent")
     *
     * @var string
     */
    const EXCEPTION = 'web.exception';

    /**
     * The VIEW event occurs when the return value of a controller
     * is not a Response instance.
     *
     * This event allows you to create a response for the return value of the
     * controller.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent")
     *
     * @var string
     */
    const VIEW = 'web.view';

    /**
     * The RESPONSE event occurs once a response was created for
     * replying to a request.
     *
     * This event allows you to modify or replace the response that will be
     * replied.
     *
     * @Event("Symfony\Component\HttpKernel\Event\FilterResponseEvent")
     *
     * @var string
     */
    const RESPONSE = 'web.response';

    /**
     * The TERMINATE event occurs once a response was sent.
     *
     * This event allows you to run expensive post-response jobs.
     *
     * @Event("Symfony\Component\HttpKernel\Event\PostResponseEvent")
     *
     * @var string
     */
    const TERMINATE = 'web.terminate';

    /**
     * The FINISH_REQUEST event occurs when a response was generated for a request.
     *
     * This event allows you to reset the global and environmental state of
     * the application, when it was changed during the request.
     *
     * @Event("Symfony\Component\HttpKernel\Event\FinishRequestEvent")
     *
     * @var string
     */
    const FINISH_REQUEST = 'web.finish_request';
}
