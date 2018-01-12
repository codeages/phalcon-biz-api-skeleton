<?php

namespace Codeages\PhalconBiz;

use Symfony\Component\Finder\Finder;
use Phalcon\Annotations\Adapter\Files as AnnotationReader;
use Phalcon\Mvc\Router\Annotations as AnnotationRouter;

class ApiDiscovery
{
    /**
     * @var AnnotationRouter
     */
    protected $router;

    protected $debug = false;

    protected $cacheDir;

    protected $cachePath;

    public function __construct(AnnotationRouter $router, $debug, $cacheDir)
    {
        $this->router = $router;
        $this->debug = $debug;
        $this->cacheDir = $cacheDir;
        $this->cachePath = $this->cacheDir.DIRECTORY_SEPARATOR.'router_map.php';
    }

    public function discovery($namespace, $directory)
    {
        if (!$this->debug) {
            $routerMap = $this->getRouterMapCache($namespace, $directory);
        } else {
            $routerMap = $this->getRouterMap($namespace, $directory);          
        }

        foreach ($routerMap as $router) {
            $this->router->addResource($router['class'], $router['routePrefix']);
        }
    }

    protected function getRouterMapCache($namespace, $directory)
    {
        $this->generateRouterMapCache($namespace, $directory);

        return require $this->cachePath;
    }

    protected function generateRouterMapCache($namespace, $directory)
    {
        if (file_exists($this->cachePath)) {
            return;
        }

        $routerMap = $this->getRouterMap($namespace, $directory);

        if (file_put_contents($this->cachePath, "<?php return " . var_export($routerMap, true) . "; ") === false) {
            throw new Exception("Cache directory cannot be written");
        }
    }

    protected function getRouterMap($namespace, $directory)
    {
        $routerMap = [];
        $finder = new Finder();
        $finder->files()->in($directory)->name('*.php')->sortByName();

        $reader = new AnnotationReader([
            "annotationsDir" => $this->cacheDir.'/',
        ]);

        foreach ($finder as $file) {
            $class = $namespace.'\\'.$file->getBasename('.php');

            if (!class_exists($class)) {
                continue;
            }

            $reflector = $reader->get($class);
            $annotations = $reflector->getClassAnnotations();

            if (!$annotations) {
                continue;
            }

            if (!$annotations->has('RoutePrefix')) {
                continue;
            }

            $anno = $annotations->get('RoutePrefix');

            array_push($routerMap, [
                'class' => $class,
                'routePrefix' => $anno->getArgument(0)
            ]);
        }

        return $routerMap;
    }
}
