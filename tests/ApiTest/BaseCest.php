<?php
namespace Test\ApiTest;

use Test\ApiTester;
use Codeages\PhalconBiz\TestHelperTrait;
use Codeages\PhalconBiz\DbTestHelper;

abstract class BaseCest
{
    use TestHelperTrait;

    public function _before(ApiTester $I)
    {
        $this->configFilepath = dirname(dirname(__DIR__)).'/config/biz.php';
        $this->biz = $this->createBiz();
    }

    public function _after(ApiTester $I)
    {
        $helper = new DbTestHelper($this->biz['db']);
        $helper->truncateAllTables();
    }
}