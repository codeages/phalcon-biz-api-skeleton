<?php

namespace App;

use Codeages\Biz\Framework\Context\BizAwareInterface;
use Codeages\Biz\Framework\Context\BizAwareTrait;
use Codeages\PhalconBiz\Authentication\ApiUser;
use Codeages\PhalconBiz\Authentication\UserProvider;
use Phalcon\Http\RequestInterface;

class ApiUserProvider implements UserProvider, BizAwareInterface
{
    use BizAwareTrait;

    public function loadUser($identifier, RequestInterface $request)
    {
        $user = $this->getUser($identifier);
        if (empty($user)) {
            return null;
        }

        $user['login_ip'] = $request->getClientAddress(true);
        $user['login_client'] = $request->getUserAgent();

        return new ApiUser($user);
    }

    protected function getUser()
    {
        // 此处应调用 service 获取 user，为举例这里 fake 了一个 user 。
        // $user = $this->biz->service('User:UserService')->getUserByAccessKey($identifier);
        return [
            'id' => 1,
            'username' => 'testuser',
            'access_key' => 'test_access_key',
            'secret_key' => 'test_secret_key',
            'created_at' => time(),
            'updated_at' => time(),
        ];
    }
}
