<?php
namespace Test\UnitTest\Biz;

use Test\UnitTest\BaseTest;

class UserServiceTest extends BaseTest
{
    public function testGetUser()
    {
        $fakeUser = $this->fakeUser();
        $user = $this->getUserService()->getUser($fakeUser['id']);
        $this->tester->assertEquals($fakeUser['id'], $user['id']);
    }

    protected function fakeUser($user = [])
    {
        $user = array_merge([
            'id' => 1,
            'username' => 'test',
            'email' => 'test@example.com',
            'password' => 'test_password',
            'salt' => 'test_salt',
            'updated_at' => time(),
            'created_at' => time(),
        ], $user);

        $this->tester->haveInDatabase($this->biz->dao('User:UserDao')->table(), $user);
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}