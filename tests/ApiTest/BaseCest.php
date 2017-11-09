<?php

namespace Test\ApiTest;

use Test\ApiTester;
use Codeages\Biz\Framework\Testing\DbTestHelper;

abstract class BaseCest
{
    const RESOURCE_NOT_FOUND_ERROR = 10;

    public function _before(ApiTester $I)
    {
        $helper = new DbTestHelper($I->biz()['db']);
        $helper->truncateAllTables();
    }
}
