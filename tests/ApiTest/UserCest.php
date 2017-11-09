<?php

namespace Test\ApiTest;

use Test\ApiTester;
use Codeception\Util\HttpCode;

class UserCest extends BaseCest
{
    public function getUserWithExistThenResponseUser(ApiTester $I)
    {
        $user = [
            'id' => 1,
            'username' => 'test',
            'access_key' => 'test_access_key',
            'secret_key' => 'test_secret_key',
            'created_at' => time(),
            'updated_at' => time(),
        ];
        $I->haveInDatabase('user', $user);

        $I->sendGET("/users/{$user['id']}");

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->assertEquals($user['id'], $I->grabDataFromResponseByJsonPath('$.id')[0]);
        $I->dontSeeResponseJsonMatchesJsonPath('$.password');
    }

    public function getUserWithNotExistThenResopnseError(ApiTester $I)
    {
        $I->sendGET('/users/99999');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseJsonMatchesJsonPath('$.error');
        $I->assertEquals(self::RESOURCE_NOT_FOUND_ERROR, $I->grabDataFromResponseByJsonPath('$.error.code')[0]);
    }

    public function searchUser(ApiTester $I)
    {
        // happy pass
        $I->sendGET('/users');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function createUser(ApiTester $I)
    {
        $user = [
            'username' => 'test_username',
            'access_key' => 'test_access_key',
            'secret_key' => 'test_secret_key',
        ];

        $I->sendPOST('/users', $user);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer:>0', // multiple types
            'username' => 'string',
            'created_at' => 'string:date',
            'updated_at' => 'string:date',
       ]);

        $I->assertEquals($user['username'], $I->grabDataFromResponseByJsonPath('$.username')[0]);
        $I->dontSeeResponseJsonMatchesJsonPath('$.password');
    }

    public function banUser(ApiTester $I)
    {
        $user = [
            'id' => 1,
            'username' => 'test',
            'access_key' => 'test_access_key',
            'secret_key' => 'test_secret_key',
            'created_at' => time(),
            'updated_at' => time(),
        ];
        $I->haveInDatabase('user', $user);

        $I->sendPOST("/users/{$user['id']}/actions/ban");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->assertTrue($I->grabDataFromResponseByJsonPath('$.success')[0]);
    }

    // more test...
}
