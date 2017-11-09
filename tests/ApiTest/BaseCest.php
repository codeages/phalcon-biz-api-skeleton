<?php

namespace Test\ApiTest;

use Test\ApiTester;
use Codeages\Biz\Framework\Testing\DbTestHelper;

abstract class BaseCest
{
    const RESOURCE_NOT_FOUND_ERROR = 10;

    protected $user;

    public function _before(ApiTester $I)
    {
        $helper = new DbTestHelper($I->biz()['db']);
        $helper->truncateAllTables();

        $user = [
            'id' => 1,
            'username' => 'test',
            'access_key' => 'test_access_key',
            'secret_key' => 'test_secret_key',
            'created_at' => time(),
            'updated_at' => time(),
        ];
        $I->haveInDatabase('user', $user);
        $this->user = $user;

        $I->haveHttpHeader('Authorization', "Secret {$user['access_key']}:{$user['secret_key']}");
    }
}
