<?php

namespace App;

use Datto\JsonRpc\Evaluator;
use Datto\JsonRpc\Exceptions\MethodException;
use Datto\JsonRpc\Responses\ErrorResponse;
use App\Biz\ServiceContext;

class JsonRpcServerHandler implements Evaluator
{
    private $biz;

    public function __construct($biz)
    {
        $this->biz = $biz;
    }

    public function evaluate($method, $arguments)
    {
        $this->auth();
        $this->initServiceContext();

        list($service, $method) = $this->validateMethod($method);

        try {
            $result = call_user_func_array(array($service, $method), $arguments);
        } catch (\Throwable $e) {
            $data = [
                'type' => get_class($e),
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ];
            throw new \Datto\JsonRpc\Exceptions\ApplicationException($e->getMessage(), $e->getCode(), $data);
        }

        return $result;
    }

    private function auth()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            throw new InvalidRequestException('Access denied (authorization credentials is missing).');
        }

        $envUsername = getenv('JSONRPC_USERNAME');
        $envPassword = getenv('JSONRPC_PASSWORD');

        if (!$envUsername || !$envPassword) {
            throw new InvalidRequestException('Access denied (Server is not set JSONRPC_USERNAME or JSONRPC_PASSWORD env variate).');
        }

        $requestUsername = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
        $requestPassword = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;

        if (($envUsername != $requestUsername) || ($envPassword != $requestPassword))  {
            throw new InvalidRequestException('Access denied (Invalid credentials).');
        }
    }

    private function initServiceContext()
    {
        $context = new ServiceContext();
        if (isset($_SERVER['HTTP_JSONRPC_CONTEXT'])) {
            parse_str($_SERVER['HTTP_JSONRPC_CONTEXT'], $parsed);
            $context->setUserId(isset($parsed['user_id']) ? $parsed['user_id'] : null);
            $context->setUsername(isset($parsed['username']) ? $parsed['username'] : null);
            $context->setIp(isset($parsed['ip']) ? $parsed['ip'] : null);
            $context->setTraceId(isset($parsed['ip']) ? $parsed['ip'] : null);
        }

        $this->biz['service_context'] = $context;
    }

    private function validateMethod($method)
    {
        if (strpos($method, '.') === false) {
            throw new MethodException();
        }

        list($serviceId, $method) = explode('.', $method);

        try {
            $service = $this->biz->service($serviceId);
        } catch (\Throwable $e) {
            throw new MethodException();
        }

        if (!is_callable([$service, $method])) {
            throw new MethodException();
        }

        return [$service, $method];
    }
}

class InvalidRequestException extends \Datto\JsonRpc\Exceptions\Exception
{
    public function __construct($message, $data = null)
    {
        parent::__construct($message, ErrorResponse::INVALID_REQUEST, $data);
    }
}