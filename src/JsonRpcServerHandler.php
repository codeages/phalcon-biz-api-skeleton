<?php

namespace App;

use Datto\JsonRpc\Evaluator;
use Datto\JsonRpc\Exceptions\MethodException;
use Datto\JsonRpc\Responses\ErrorResponse;
use App\Biz\ServiceContext;
use Psr\Log\LoggerInterface;

class JsonRpcServerHandler implements Evaluator
{
    private $biz;

    public function __construct($biz)
    {
        $this->biz = $biz;
    }

    public function evaluate($fullMethod, $arguments)
    {
        try {
            $this->auth();
            $this->initServiceContext();
            list($service, $method) = $this->validateMethod($fullMethod);
        } catch (\Exception $e) {
            $this->getLogger()->notice($e->getMessage(), ['rpcMethod' => $fullMethod, 'rpcArguments' => $arguments]);
            throw $e;
        }

        try {
            $result = call_user_func_array(array($service, $method), $arguments);
            if (true === $this->biz['debug']) {
                $this->getLogger()->info('RPC call.', ['rpcMethod' => $fullMethod, 'rpcArguments' => $arguments]);
            }
        } catch (\Throwable $e) {
            $data = [
                'type' => get_class($e),
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ];

            $this->getLogger()->notice($e->getMessage(), [
                'rpcMethod' => $method,
                'rpcArguments' => $arguments,
                'rpcContext' => $this->biz['service_context']->toArray(),
                'exception' => $data
            ]);

            throw new \Datto\JsonRpc\Exceptions\ApplicationException($e->getMessage(), $e->getCode(), $data);
        }

        return $result;
    }

    private function auth()
    {
        $allowedIps = env('JSONRPC_ALLOWED_IPS');
        $allowedIps  = str_replace(' ', '', $allowedIps);
        $allowedIps = !$allowedIps ? [] : explode(',', $allowedIps);

        if (empty($allowedIps)) {
            throw new InvalidRequestException("Access denied (Server is not set JSONRPC_ALLOWED_IPS env variate).");
        }

        if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIps)) {
            throw new InvalidRequestException("Access denied (your ip is not allowed).");
        }

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
            $context->setTraceId(isset($parsed['trace_id']) ? $parsed['trace_id'] : null);
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

    /**
     * @return LoggerInterface
     */
    private function getLogger()
    {
        return $this->biz['logger'];
    }
}

class InvalidRequestException extends \Datto\JsonRpc\Exceptions\Exception
{
    public function __construct($message, $data = null)
    {
        parent::__construct($message, ErrorResponse::INVALID_REQUEST, $data);
    }
}