<?php

namespace Codeages\PhalconBiz;

use Symfony\Component\Finder\Finder;
use Phalcon\Annotations\Adapter\Memory as AnnotationReader;
use Phalcon\Mvc\Router\Annotations as AnnotationRouter;

class ApiDiscovery
{
    /**
     * @var AnnotationRouter
     */
    protected $router;

    protected $routePrefixs = [];

    public function __construct(AnnotationRouter $router)
    {
        $this->router = $router;
    }

    public function discovery($namespace, $directory)
    {
        $finder = new Finder();
        $finder->files()->in($directory)->name('*.php')->sortByName();

        $reader = new AnnotationReader();

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

            array_push($this->routePrefixs, $anno->getArgument(0));
            $this->router->addResource($class, $anno->getArgument(0));
        }
    }

    public function getRoutePrefixs()
    {
        return $this->routePrefixs;
    }
}
