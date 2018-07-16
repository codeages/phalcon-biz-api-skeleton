<?php

namespace Tests;

use App\Biz\AppBiz;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    /**
     * @var \App\Biz\AppBiz;
     */
    protected $biz;
    protected $prophet;

    protected function setUp()
    {
        $configFile = dirname(__DIR__).'/config/biz.php';
        if (!file_exists($configFile)) {
            throw new \RuntimeException('Biz config file not found.');
        }

        $this->biz = new AppBiz(require $configFile);
        $this->biz->boot();

        $this->biz['db']->beginTransaction();
    }

    protected function tearDown()
    {
        $this->biz['db']->rollBack();
    }
}
