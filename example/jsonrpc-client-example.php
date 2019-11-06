<?php

define('ROOT_DIR', dirname(__DIR__));

require dirname(__DIR__).'/vendor/autoload.php';

$biz = require ROOT_DIR.'/bootstrap/biz.php';

class ArticleService
{
    use Codeages\PhalconBiz\JsonRpcClient\JsonRpcClientTrait;

    protected $endpoint;

    protected $biz;

    public function __construct($biz)
    {
        $this->biz = $biz;
        $this->endpoint = getenv('JSONRPC_EXAMPLE');
    }

    public function create($article)
    {
        return $this->call('ArticleService.create', $article);
    }
}

$service = new ArticleService($biz);

$article = $service->create([
    'title' => 'test title',
    'content' => 'test content',
]);

var_dump($article);
