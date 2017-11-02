<?php
namespace Biz;

use Codeages\Beanstalk\Client;
use Codeages\Biz\Framework\Context\Biz;
use Codeages\Biz\Framework\Provider\MonologServiceProvider;
use Codeages\Biz\Framework\Provider\DoctrineServiceProvider;
use Codeages\Biz\Framework\Provider\TargetlogServiceProvider;

class AppBiz extends Biz
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this['migration.directories'][] = dirname(dirname(__DIR__)).'/migrations';
        $this->register(new DoctrineServiceProvider());
        $this->register(new MonologServiceProvider(), [
            'monolog.logfile' => $this['log_dir'].'/app.log',
        ]);
    }
}
