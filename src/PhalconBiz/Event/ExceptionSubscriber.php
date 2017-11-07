<?php
namespace Codeages\PhalconBiz\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Codeages\PhalconBiz\Event\GetResponseForExceptionEvent;
use Codeages\Biz\Framework\Service\Exception\ServiceException;
use Codeages\Biz\Framework\Service\Exception\NotFoundException as ServiceNotFoundException;
use Codeages\Biz\Framework\Service\Exception\InvalidArgumentException as ServiceInvalidArgumentException;
use Codeages\Biz\Framework\Service\Exception\AccessDeniedException as ServiceAccessDeniedException;
use Codeages\PhalconBiz\ErrorCode;
use Codeages\PhalconBiz\NotFoundException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();
        $debug = $event->getApp()->isDebug();

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
                'message' => $debug ? $e->getMessage() : 'Service unavailable.'
            ];
            $statusCode = 500;
        }

        $error['trace_id'] = time().'_'.substr(hash('md5', uniqid('', true)), 0, 10);
        
        if ($debug) {
            $error['detail'] = $this->formatExceptionDetail($e);
        }

        $response = $event->getDI()->get('response');
        $response->setStatusCode($statusCode);
        $response->setContent(json_encode([
            'error' => $error,
        ]));

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            WebEvents::EXCEPTION => 'onException'
        ];
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
}