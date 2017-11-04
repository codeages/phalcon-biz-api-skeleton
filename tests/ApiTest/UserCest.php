<?php
namespace Test\ApiTest;

use Test\ApiTester;
use Codeception\Util\HttpCode;

class UserCest extends BaseCest
{
    public function searchUser(ApiTester $I)
    {
        $I->sendGET('/users');
        $I->seeResponseCodeIs(HttpCode::OK); 
        $I->seeResponseIsJson();
    }

    public function createUser(ApiTester $I)
    {
        $I->sendPOST('/users', [
            'username' => 'test_username',
            'password' => 'test_password',
        ]);

        $I->seeResponseCodeIs(HttpCode::OK); 
        $I->seeResponseIsJson();
    }
}
