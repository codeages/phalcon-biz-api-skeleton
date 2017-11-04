<?php
namespace Test\UnitTest\Biz;

use Test\UnitTest\BaseTest;

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

    // public function testCreateUserWithValidParams()
    // {
    //     $user = [
    //         'username' => 'test',
    //         'password' => 'test',
    //     ];
    //     $createdUser = $this->getUserService()->createUser($user);

    //     $this->tester->assertEquals($user['username'], $createdUser['username']);
    // }

    // /**
    //  * @expectedException Codeages\Biz\Framework\Service\Exception\InvalidArgumentException
    //  * @expectedExceptionMessageRegExp /用户名已存在/
    //  */
    // public function testCreateUserWithExistUsernameThanThrowException()
    // {
    //     $user = [
    //         'username' => 'test',
    //         'password' => 'test',
    //     ];
    //     $this->getUserService()->createUser($user);
    //     $this->getUserService()->createUser($user);
    // }

    // /**
    //  * @expectedException Codeages\Biz\Framework\Validation\ValidationException
    //  */
    // public function testCreateUserWithUsernameHasInvalidLengthThanThrowException()
    // {
    //     $user = [
    //         'id' => 1,
    //         'username' => 'testtest1234567890123456789',
    //         'password' => 'test_password',
    //         'salt' => 'test_salt',
    //         'is_banned' => 0,
    //         'created_at' => time(),
    //         'updated_at' => time(),
    //     ];
    //     $this->tester->haveInDatabase($this->getUserTable(), $user);
    // }

    // /**
    //  * @group current
    //  *
    //  * @return void
    //  */
    // public function testBanUser()
    // {
    //     $user = $this->fakeUser();

    //     $this->getUserService()->banUser($user['id']);

    //     $user = $this->getUserService()->getUser($user['id']);

    //     $this->tester->seeInDatabase($this->getUserTable(), ['id' => $user['id'], 'is_banned' => 1]);
    // }

    protected function fakeUser($user = [])
    {
        $user = array_merge([
            'id' => 1,
            'username' => 'test',
            'password' => 'test_password',
            'salt' => 'test_salt',
        ], $user);

        return $this->getUserDao()->create($user);
    }

    protected function getUserTable()
    {
        return $this->tester->createDao('User:UserDao')->table();
    }

    protected function getUserDao()
    {
        return $this->tester->createDao('User:UserDao');
    }

    /**
     * @return \Biz\User\Service\UserService
     */
    protected function getUserService()
    {
        return $this->tester->createService('User:UserService');
    }
}