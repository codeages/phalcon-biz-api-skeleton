<?php
namespace Codeages\PhalconBiz;

use Codeages\Biz\Framework\Context\Biz;
use Phalcon\DiInterface;
use Phalcon\Di;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Codeages\PhalconBiz\Event\GetResponseEvent;
use Codeages\PhalconBiz\Event\FinishRequestEvent;
use Codeages\PhalconBiz\Event\FilterResponseEvent;
use Codeages\PhalconBiz\Event\GetResponseForExceptionEvent;
use Codeages\PhalconBiz\Event\WebEvents;


class Application
{
    /**
     * @var DiInterface
     */
    protected $di;

    /**
     * @var Biz
     */
    protected $biz;

    protected $debug = false;

    protected $config;

    public function __construct(Biz $biz, $config = [])
    {
        $this->biz = $biz;
        $this->config = $config;
        $this->debug = isset($biz['debug']) ? $biz['debug'] : false;
        $this->di = $this->initializeContainer();
        $this->di['biz'] = $biz; 
    }

    /**
     * Get Phalcon Application DI
     *
     * @return DiInterface
     */
    public function getDI()
    {
        return $this->di;
    }

    public function isDebug()
    {
        return $this->debug;
    }

    protected function initializeContainer()
    {
        $di = new Di();

        $di->setShared('annotations', function () {
            return new \Phalcon\Annotations\Adapter\Memory();
        });

        $di->setShared('mvc_dispatcher', function () {
            return new \Phalcon\Mvc\Dispatcher();
        });

        $di->setShared('filter', function() {
            return new \Phalcon\Filter();
        });

        $di->setShared('router', function () {
            // Use the annotations router. We're passing false as we don't want the router to add its default patterns
            $router = new \Phalcon\Mvc\Router\Annotations(false);
            $router->setControllerSuffix('');
            $router->setActionSuffix('');

            return $router;
        });

        $di->setShared('request', function() {
            return new \Phalcon\Http\Request();
        });

        $di->set('response', function() {
            return new \Phalcon\Http\Response();
        });
        
        $subscribers = $this->config['subscribers'] ?? [];
        $di->set('event_dispatcher', function() use ($subscribers) {
            $dispatcher = new EventDispatcher();

            foreach ($subscribers as $subscriber) {
                $dispatcher->addSubscriber(new $subscriber());
            }
            return $dispatcher;
        });

        return $di;
    }

    public function handle()
    {
        $request = $this->di['request'];
        try {
            $response = $this->doHandle();
        } catch (\Exception $e) {
            $response = $this->handleException($e, $request);
        }

        if ($response instanceof ResponseInterface) {
            $response->send();
            return ;
        }

        if (is_array($response)) {
            $content = $response;
            $response = $this->di['response'];
            $response->setStatusCode(200);
            $response->setContent(json_encode($content));
            $response->send();
            return ;
        }
    }

    private function handleException(\Exception $e, $request)
    {
        $event = new GetResponseForExceptionEvent($this, $request, $e);
        $this->di['event_dispatcher']->dispatch(WebEvents::EXCEPTION, $event);

        // a listener might have replaced the exception
        $e = $event->getException();

        if (!$event->hasResponse()) {
            $this->finishRequest($request);
            throw $e;
        }

        $response = $event->getResponse();

        try {
            return $this->filterResponse($response, $request);
        } catch (\Exception $e) {
            return $response;
        }
    }

    public function handle2()
    {
        $error = null;
        $statusCode = 0;
        try {
            $returned = $this->doHandle();
        } catch (\Exception $e) {

        } catch (\Throwable $e) {
            $error = [
                'code' => ErrorCode::SERVICE_UNAVAILABLE,
                'message' => $this->debug ? $e->getMessage() : 'Service unavailable.'
            ];
            $statusCode = 500;
        }

        if ($returned instanceof ResponseInterface) {
            $returned->send();
            return ;
        }

        if (is_array($returned)) {
            $response = $this->di['response'];
            $response->setStatusCode($statusCode ? : 200);
            $response->setContent(json_encode($returned));
            $response->send();
            return ;
        }

        $response = $this->di['response'];
        $response->setStatusCode(500);
        $response->setContent(json_encode([
            'error' => [
                'code' => ErrorCode::SERVICE_UNAVAILABLE,
                'mesage' => 'Controller action must be return response object or array.',
            ]
        ]));
        $response->send();
    }

    public function doHandle()
    {
        $request = $this->di['request'];
        $event = new GetResponseEvent($this, $request);
        $this->di['event_dispatcher']->dispatch(WebEvents::REQUEST, $event);

        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request, $type);
        }

        $router = $this->di['router'];

        $discovery = new ApiDiscovery($router);
        $discovery->discovery('Controller', dirname(__DIR__).'/Controller');

        $router->handle();

        if (!$router->getMatchedRoute()) {
            throw new NotFoundException();
        }

        $dispatcher = $this->di['mvc_dispatcher'];

        $dispatcher->setControllerSuffix('');
        $dispatcher->setActionSuffix('');

        $dispatcher->setNamespaceName($router->getNamespaceName());
        $dispatcher->setControllerName($router->getControllerName());

        $dispatcher->setActionName(
            $router->getActionName()
        );

        $dispatcher->setParams(
            $router->getParams()
        );
        
        $dispatcher->dispatch();
        return $dispatcher->getReturnedValue();
    }

    /**
     * 过滤 Response
     *
     * @param ResponseInterface $response 
     * @param RequestInterface  $request  
     *
     * @return Response 过滤后的 Response 实例
     */
    private function filterResponse(ResponseInterface $response, RequestInterface $request)
    {
        $event = new FilterResponseEvent($this, $request, $response);

        $this->di['event_dispatcher']->dispatch(WebEvents::RESPONSE, $event);

        $this->finishRequest($request);

        return $event->getResponse();
    }

    /**
     * 派发完成请求的事件
     *
     * @param RequestInterface $request
     */
    private function finishRequest(RequestInterface $request)
    {
        $this->di['event_dispatcher']->dispatch(WebEvents::FINISH_REQUEST, new FinishRequestEvent($this, $request));
    }
}
