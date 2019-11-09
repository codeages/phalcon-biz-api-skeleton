<?php
define('ROOT_DIR', dirname(__DIR__));

require ROOT_DIR.'/vendor/autoload.php';

$biz = require ROOT_DIR.'/bootstrap/biz.php';


//$context = 'a bed content.';
//
//header('HTTP/1.1 400 Bad request');
//header('Content-Type: application/json');
//header('Content-Length: ' . strlen($context));
//
//
//
//exit();



$biz['user'] =  ['id' => 1];

//sleep(3);

class BizJsonRpcEvaluator implements \Datto\JsonRpc\Evaluator
{
    private $biz;

    public function __construct($biz)
    {
        $this->biz = $biz;
    }

    public function evaluate($method, $arguments)
    {
        if (strpos($method, '.') === false) {
            throw new \Datto\JsonRpc\Exceptions\MethodException();
        }

        list($serviceId, $method) = explode('.', $method);
        try {
            $service = $this->biz->service($serviceId);
        } catch (\Exception $e) {
            throw new \Datto\JsonRpc\Exceptions\MethodException();
        }

        if (!is_callable([$service, $method])) {
            throw new \Datto\JsonRpc\Exceptions\MethodException();
        }

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
}

$server = new \Datto\JsonRpc\Http\Server(new BizJsonRpcEvaluator($biz));
$server->reply();
