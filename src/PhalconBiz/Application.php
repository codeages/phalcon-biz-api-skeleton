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
use Codeages\PhalconBiz\Event\GetResponseForControllerResultEvent;
use Codeages\PhalconBiz\Event\WebEvents;
use Codeages\Biz\Framework\Context\BizAwareInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem;

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

    /**
     * @var ConfigCache
     */
    protected $cache;

    public function __construct(Biz $biz, $config = [])
    {
        $this->biz = $biz;
        $this->config = $config;
        $this->debug = isset($biz['debug']) ? $biz['debug'] : false;
        $this->di = $this->initializeContainer();
        $this->di['biz'] = $biz;
        $cacheFile = $this->biz['cache_directory'].DIRECTORY_SEPARATOR.'route_map.php';
        $this->cache = new ConfigCache($cacheFile, $this->isDebug());
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
        $config = $this->config;
        $biz = $this->biz;

        $di->setShared('annotations', function () {
            return new \Phalcon\Annotations\Adapter\Memory();
        });

        $di->setShared('mvc_dispatcher', function () {
            return new \Phalcon\Mvc\Dispatcher();
        });

        $di->setShared('filter', function () {
            return new \Phalcon\Filter();
        });

        $di->setShared('router', function () {
            // Use the annotations router. We're passing false as we don't want the router to add its default patterns
            $router = new \Phalcon\Mvc\Router\Annotations(false);
            $router->setControllerSuffix('');
            $router->setActionSuffix('');

            return $router;
        });

        $di->setShared('request', function () {
            return new \Phalcon\Http\Request();
        });

        $di->set('response', function () {
            return new \Phalcon\Http\Response();
        });

        $subscribers = $config['subscribers'] ?? [];
        $di->setShared('event_dispatcher', function () use ($subscribers) {
            $dispatcher = new EventDispatcher();

            foreach ($subscribers as $subscriber) {
                $dispatcher->addSubscriber(new $subscriber());
            }

            return $dispatcher;
        });

        if (isset($config['user_provider'])) {
            $di->setShared('user_provider', function () use ($config, $biz) {
                $provider = new $config['user_provider']();
                if ($provider instanceof BizAwareInterface) {
                    $provider->setBiz($biz);
                }

                return $provider;
            });
        }

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

        $response->send();
    }

    private function handleException(\Exception $e, $request)
    {
        $event = new GetResponseForExceptionEvent($this, $request, $e);
        $this->di['event_dispatcher']->dispatch(WebEvents::EXCEPTION, $event);

        // Listener 中可能会重设 Exception，所以这里重新获取了 Exception。
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

    public function doHandle()
    {
        $request = $this->di['request'];
        $event = new GetResponseEvent($this, $request);
        $this->di['event_dispatcher']->dispatch(WebEvents::REQUEST, $event);

        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request, $type);
        }

        if (!$this->isDebug()) {
            $routes = $this->getRouterCache();
            $matchRoute = [];
            if (isset($routes[$request->getURI()][$request->getMethod()])) {
                $matchRoute = $routes[$request->getURI()][$request->getMethod()];
            } else {
                foreach ($routes as $key => $route) {
                    if (preg_match($key, $request->getURI())) {
                        $matchRoute = isset($route[$request->getMethod()]) ? $route[$request->getMethod()] : [];
                        break;
                    }
                }
            }

            if (empty($matchRoute)) {
                throw new NotFoundException();
            }
            $dispatcher = $this->di['mvc_dispatcher'];

            $dispatcher->setControllerSuffix('');
            $dispatcher->setActionSuffix('');

            $dispatcher->setNamespaceName($matchRoute['namespace']);
            $dispatcher->setControllerName($matchRoute['controller']);

            $dispatcher->setActionName(
                $matchRoute['action']
            );

            $dispatcher->setParams(
                $matchRoute['params'] ?: []
            );
        } else {
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
        }
        $dispatcher->dispatch();
        $response = $dispatcher->getReturnedValue();

        // view
        if (!$response instanceof ResponseInterface) {
            $event = new GetResponseForControllerResultEvent($this, $request, $response);
            $this->di['event_dispatcher']->dispatch(WebEvents::VIEW, $event);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            }

            if (!$response instanceof ResponseInterface) {
                $msg = 'The controller must return a response.';
                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }
                throw new \LogicException($msg);
            }
        }

        return $response;
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

    /**
     * 获取路由缓存
     */
    protected function getRouterCache()
    {
        $this->generateRouterCache();

        return require $this->cache->getPath();
    }

    protected function generateRouterCache()
    {
        if ($this->cache->isFresh()) {
            return;
        }

        if (!realpath($this->cache->getPath())) {
            $fs = new Filesystem();

            $fs->touch($this->cache->getPath());
        }

        $file = new FileResource($this->cache->getPath());

        $routeMap = array();

        $router = $this->di['router'];

        $discovery = new ApiDiscovery($router);
        $discovery->discovery('Controller', dirname(__DIR__).'/Controller');
        $routePrefixs = $discovery->getRoutePrefixs();

        foreach($routePrefixs as $routePrefix) {
            $router->handle($routePrefix);
            $routes = $router->getRoutes();
            foreach($routes as $route)
            {
                $part = ['pattern' => $route->getPattern()];
                $paths = $route->getPaths();
                $part['namespace'] = $paths['namespace'];
                $part['controller'] = $paths['controller'];
                $part['action'] = $paths['action'];
                unset($paths['namespace']);
                unset($paths['controller']);
                unset($paths['action']);
                if (count($paths) > 0) {
                    $part['params'] = $paths;
                }

                $routeMap[$route->getCompiledPattern()][$route->getHttpMethods()] = $part;
            }
        }

        $this->cache->write(sprintf('<?php return %s;', var_export($routeMap, true)), array($file));
    }
}
