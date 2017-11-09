<?php

use PHPUnit\Framework\TestCase;
use Codeages\PhalconBiz\Authentication\CurrentUser;
use Codeages\PhalconBiz\Authentication\ApiUser;

class ApiUserTest extends TestCase
{
    public function testNewInstance()
    {
        $user = new ApiUser($this->fakeUser());

        $this->assertInstanceOf('\Codeages\Biz\Framework\Context\CurrentUser', $user);

        $this->assertFalse($user['disabled']);
        $this->assertFalse($user['locked']);
        $this->assertFalse($user['expired']);
    }

    public function testNewInstanceWithStatusAttrs()
    {
        $user = new ApiUser($this->fakeUser([
            'disabled' => 1,
            'locked' => 1,
            'expired_time' => time() -1,
        ]));

        $this->assertTrue($user['disabled']);
        $this->assertTrue($user['locked']);
        $this->assertTrue($user['expired']);
    }

    public function testNewInstanceWithNoExpiredTime()
    {
        $user = new ApiUser($this->fakeUser([
            'disabled' => 1,
            'locked' => 1,
            'expired_time' => time() + 100,
        ]));

        $this->assertFalse($user['expired']);
    }

    protected function fakeUser($fields = array())
    {
        return array_merge(array(
            'id' => 1,
            'username' => 'test_user',
            'access_key' => 'test_access_key',
            'secret_key' => 'test_secret_key',
            'login_client' => 'chrome',
            'login_ip' => '127.0.0.1',
        ), $fields);
    }
}