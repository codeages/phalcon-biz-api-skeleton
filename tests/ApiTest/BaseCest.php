<?php
namespace Test\ApiTest;

use Test\ApiTester;
use Codeages\PhalconBiz\TestHelperTrait;
use Codeages\PhalconBiz\DbTestHelper;

abstract class BaseCest
{
    public function _before(ApiTester $I)
    {
        $helper = new DbTestHelper($I->biz()['db']);
        $helper->truncateAllTables();
    }
}