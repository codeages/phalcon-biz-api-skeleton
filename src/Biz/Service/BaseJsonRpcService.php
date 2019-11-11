<?php

namespace App\Biz\Service;

use App\Biz\ServiceContext;
use Codeages\Biz\Framework\Service\BaseService;
use Codeages\Biz\Framework\Service\Exception\ServiceException;
use Codeages\PhalconBiz\JsonRpcClient\JsonRpcClient;
use Codeages\PhalconBiz\JsonRpcClient\JsonRpcException;

class BaseJsonRpcService extends BaseService
{
    protected $endpoint;

    protected function call()
    {
        if (!$this->endpoint)  {
            throw new ServiceException(sprintf('Please declare the $endpoint attribute in the %s class.', get_called_class()));
        }

        if (!isset($this->biz['jsonrpc_endpoints'])) {
            throw new ServiceException("Please declare the 'jsonrpc_endpoints' key in config/biz.php");
        }

        $endpoints = $this->biz['jsonrpc_endpoints'];
        if (empty($endpoints[$this->endpoint])) {
            throw new ServiceException(sprintf('Please declare the %s key under jsonrpc_endpoints in config/biz.php', $this->endpoint));
        }

        $endpoint = $endpoints[$this->endpoint];
        if (empty($endpoint['addr']) || empty($endpoint['auth_type']) || empty($endpoint['auth_credentials'])) {
            throw new ServiceException(sprintf('Json Rpc endpoint %s config must have \'addr\' and \'auth_type\' and \'auth_credentials\' key.', $this->endpoint));
        }

        /** @var ServiceContext $context */
        $context = $this->biz['service_context'];

        $endpoint['context'] = [
            'user_id' => $context->getUserId(),
            'username' => $context->getUsername(),
            'ip' => $context->getIp(),
            'trace_id' => $context->getTraceId(),
        ];

        $arguments = array_merge([$endpoint], func_get_args());

        try {
            $result = call_user_func_array([$this->rpc(), 'call'], $arguments);
        } catch (JsonRpcException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    /**
     * @return JsonRpcClient
     */
    protected function rpc()
    {
        return $this->biz['rpc'];
    }
}