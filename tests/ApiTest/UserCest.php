<?php

namespace Test\ApiTest;

use Test\ApiTester;
use Codeception\Util\HttpCode;

class UserCest extends BaseCest
{
    public function getUserWithExistThenResponseUser(ApiTester $I)
    {
        $I->sendGET("/users/{$this->user['id']}");

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->assertEquals($this->user['id'], $I->grabDataFromResponseByJsonPath('$.id')[0]);
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
            'username' => 'new_username',
            'access_key' => 'new_access_key',
            'secret_key' => 'new_secret_key',
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
        $I->sendPOST("/users/{$this->user['id']}/actions/ban");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->assertTrue($I->grabDataFromResponseByJsonPath('$.success')[0]);
    }

    // more test...
}
