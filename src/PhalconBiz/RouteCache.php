<?php

namespace Codeages\PhalconBiz;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem;
use Phalcon\Mvc\Router\Annotations as AnnotationRouter;

class RouteCache
{
    /**
     * @var AnnotationRouter
     */
    protected $router;

    /**
     * @var ConfigCache
     */
    protected $cache;

    protected $debug = false;

    public function __construct(AnnotationRouter $router, $cacheDir, $debug)
    {
        $this->router = $router;
        $this->debug = $debug;
        $cacheFile = $cacheDir.DIRECTORY_SEPARATOR.'route_map.php';
        $this->cache = new ConfigCache($cacheFile, $this->debug);
    }

    public function discovery($uri, $method)
    {
        $route = $this->getMatchRoute($uri, $method);
 
        if (empty($route)) {
            throw new NotFoundException();
        }

        $this->router->add($route['pattern'], $route['paths'], $route['method']);
    }

    /**
     * 获取当前请求匹配到的路由
     */
    protected function getMatchRoute($uri, $method)
    {
        $routes = $this->getRouterCache();
        if (isset($routes[$uri][$method])) {
            return $routes[$uri][$method];
        }

        foreach ($routes as $key => $route) {
            if (preg_match($key, $uri)) {
                return isset($route[$method]) ? $route[$method] : [];
            }
        }

        return [];
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

        $routeMap = [];

        $router = $this->router;

        $discovery = new ApiDiscovery($router);
        $discovery->discovery('Controller', dirname(__DIR__).'/Controller');
        $routePrefixs = $discovery->getRoutePrefixs();

        foreach($routePrefixs as $routePrefix) {
            $router->handle($routePrefix);
            $routes = $router->getRoutes();
            foreach($routes as $route)
            {
                $part = [
                    'pattern' => $route->getPattern(),
                    'paths' => $route->getPaths(),
                    'method' => $route->getHttpMethods(),
                ];

                $routeMap[$route->getCompiledPattern()][$route->getHttpMethods()] = $part;
            }
        }

        $this->cache->write(sprintf('<?php return %s;', var_export($routeMap, true)), array($file));
    }
}
