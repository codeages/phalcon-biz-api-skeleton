<?php

namespace App\Tests\UnitTest\Biz;

use Codeages\PhalconBiz\Authentication\ApiUser;

class BaseTest extends \Codeception\Test\Unit
{
    public function _before()
    {
        parent::_before();

        $this->tester->biz()['user'] = new ApiUser([
            'id' => 1,
            'username' => 'testuser',
            'access_key' => 'test_access_key',
            'secret_key' => 'test_secret_key',
            'login_client' => 'codeception unit tester',
            'login_ip' => '127.0.0.1',
        ]);
    }
}
