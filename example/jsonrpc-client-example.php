<?php

define('ROOT_DIR', dirname(__DIR__));

require dirname(__DIR__).'/vendor/autoload.php';

$biz = require ROOT_DIR.'/bootstrap/biz.php';

$context = new \App\Biz\ServiceContext();

$context->setUserId(1);
$context->setUsername('test_user');
$context->setIp('127.0.0.1');
$context->setTraceId('test_trace_id');

$biz['service_context'] = $context;


class ArticleService extends \App\Biz\Service\BaseJsonRpcService
{
    protected $endpoint = 'example';

    public function create($article)
    {
        return $this->call('ArticleService.create', $article);
    }
}

$service = new ArticleService($biz);

try {
    $article = $service->create([
        'title' => 'test title',
        'content' => 'test content',
    ]);
    echo "Rpc result: \n";
    print_r($article);

} catch (\Codeages\Biz\Framework\Service\Exception\ServiceException $e) {
    echo "Rpc Exception: \n";
    echo "  Code: {$e->getCode()}\n";
    echo "  Messge: {$e->getMessage()}\n";
}

