<?php

namespace App\Tests\ApiTest;

use Test\ApiTester;
use Codeages\Biz\Framework\Testing\DbTestHelper;
use Codeages\PhalconBiz\Authentication\ApiUser;

abstract class BaseCest
{
    const RESOURCE_NOT_FOUND_ERROR = 10;

    protected $user;

    /**
     * @var Test\ApiTester
     */
    protected $I;

    public function _before(ApiTester $I)
    {
        $helper = new DbTestHelper($I->biz()['db']);
        $helper->truncateAllTables();

        $user = $I->biz()['user'] = new ApiUser([
            'id' => 1,
            'username' => 'testuser',
            'access_key' => 'test_access_key',
            'secret_key' => 'test_secret_key',
            'login_client' => 'codeception unit tester',
            'login_ip' => '127.0.0.1',
        ]);

        $I->haveHttpHeader('Authorization', "Secret {$user['access_key']}:{$user['secret_key']}");

        $this->I = $I;
    }
}
