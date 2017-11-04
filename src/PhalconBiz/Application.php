<?php
namespace Codeages\PhalconBiz;

use Codeages\Biz\Framework\Context\Biz;
use Phalcon\DiInterface;
use Phalcon\Di;
use Phalcon\Http\ResponseInterface;
use Codeages\Biz\Framework\Service\Exception\ServiceException;
use Codeages\Biz\Framework\Service\Exception\NotFoundException as ServiceNotFoundException;
use Codeages\Biz\Framework\Service\Exception\InvalidArgumentException as ServiceInvalidArgumentException;
use Codeages\Biz\Framework\Service\Exception\AccessDeniedException as ServiceAccessDeniedException;

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

    public function __construct(Biz $biz)
    {
        $this->di = $this->initializeContainer();
        $this->di['biz'] = $this->biz = $biz;
        $this->debug = isset($biz['debug']) ? $biz['debug'] : false;
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

    protected function initializeContainer()
    {
        $di = new Di();

        $di->setShared('annotations', function () {
            return new \Phalcon\Annotations\Adapter\Memory();
        });

        $di->setShared('dispatcher', function () {
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

        return $di;
    }

    protected function formatExceptionDetail($e)
    {
        $error = [
            'type' => get_class($e),
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
        ];

        if ($e->getPrevious()) {
            $error = [$error];
            $newError = $this->formatExceptionDetail($e->getPrevious());
            array_unshift($error, $newError);
        }

        return $error;
    }

    public function handle()
    {
        $error = null;
        $statusCode = 0;
        try {
            $returned = $this->doHandle();
        } catch (\Exception $e) {
            if ($e instanceof ServiceException) {
                $error = ['code' => $e->getCode(), 'message' => $e->getMessage()];
                $statusCode = 400;
            } elseif ($e instanceof NotFoundException || $e instanceof ServiceNotFoundException) {
                $error = ['code' => ErrorCode::RESOURCE_NOT_FOUND, 'message' => $e->getMessage() ? : 'Resource Not Found.'];
                $statusCode = 404;
            } elseif ($e instanceof \InvalidArgumentException || $e instanceof ServiceInvalidArgumentException) {
                $error = ['code' => ErrorCode::INVALID_ARGUMENT, 'message' => $e->getMessage()];
                $statusCode = 422;
            } elseif ($e instanceof ServiceAccessDeniedException) {
                $error = ['code' => ErrorCode::ACCESS_DENIED, 'message' => $e->getMessage() ? : 'Access denied.'];
                $statusCode = 405;
            } else {
                $error = [
                    'code' => ErrorCode::SERVICE_UNAVAILABLE,
                    'message' => $this->debug ? $e->getMessage() : 'Service unavailable.'
                ];
                $statusCode = 500;
            }
        } catch (\Throwable $e) {
            $error = [
                'code' => ErrorCode::SERVICE_UNAVAILABLE,
                'message' => $this->debug ? $e->getMessage() : 'Service unavailable.'
            ];
            $statusCode = 500;
        }

        if ($error) {
            $error['trace_id'] = time().'_'.substr(hash('md5', uniqid('', true)), 0, 10);

            if ($this->debug) {
                $error['detail'] = $this->formatExceptionDetail($e);
            }

            $returned = ['error' => $error];
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
        $router = $this->di['router'];

        $discovery = new ApiDiscovery($router);
        $discovery->discovery('Controller', dirname(__DIR__).'/Controller');

        $router->handle();

        if (!$router->getMatchedRoute()) {
            throw new NotFoundHttpException();
        }

        $dispatcher = $this->di['dispatcher'];

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
}
