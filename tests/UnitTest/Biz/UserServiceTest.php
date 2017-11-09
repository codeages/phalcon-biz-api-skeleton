<?php

namespace Test\UnitTest\Biz;

class UserServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \Test\UnitTester
     */
    protected $tester;

    public function testGetUser()
    {
        $fakeUser = $this->fakeUser();
        $user = $this->getUserService()->getUser($fakeUser['id']);
        $this->tester->assertEquals($fakeUser['id'], $user['id']);
    }

    public function testCreateUserWithValidParams()
    {
        $user = [
            'username' => 'test',
            'password' => 'test',
        ];
        $createdUser = $this->getUserService()->createUser($user);

        $this->tester->assertEquals($user['username'], $createdUser['username']);
    }

    /**
     * @expectedException \Codeages\Biz\Framework\Service\Exception\InvalidArgumentException
     * @expectedExceptionMessageRegExp /用户名已存在/
     */
    public function testCreateUserWithExistUsernameThanThrowException()
    {
        $user = [
            'username' => 'test',
            'password' => 'test',
        ];
        $this->getUserService()->createUser($user);
        $this->getUserService()->createUser($user);
    }

    /**
     * @expectedException \Codeages\Biz\Framework\Validation\ValidationException
     */
    public function testCreateUserWithUsernameHasInvalidLengthThanThrowException()
    {
        $user = [
            'id' => 1,
            'username' => 'testtest1234567890123456789',
            'password' => 'test_password',
            'salt' => 'test_salt',
            'is_banned' => 0,
        ];
        $this->getUserService()->createUser($user);
    }

    public function testBanUser()
    {
        $user = $this->fakeUser();

        $this->getUserService()->banUser($user['id']);

        $this->tester->seeInDatabase($this->getUserTable(), ['id' => $user['id'], 'is_banned' => 1]);
    }

    public function testUnbanUser()
    {
        $user = $this->fakeUser([
            'is_banned' => 1,
        ]);

        $this->getUserService()->unbanUser($user['id']);

        $this->tester->seeInDatabase($this->getUserTable(), ['id' => $user['id'], 'is_banned' => 0]);
    }

    protected function fakeUser($user = [])
    {
        $user = array_merge([
            'id' => 1,
            'username' => 'test',
            'access_key' => 'test_access_key',
            'secret_key' => 'test_secret_key',
            'created_at' => time(),
            'updated_at' => time(),
        ], $user);

        $this->tester->haveInDatabase($this->getUserTable(), $user);

        return $user;
    }

    protected function getUserTable()
    {
        return $this->tester->createDao('User:UserDao')->table();
    }

    /**
     * @return \Biz\User\Service\UserService
     */
    protected function getUserService()
    {
        return $this->tester->createService('User:UserService');
    }
}
