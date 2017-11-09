<?php

use Codeages\PhalconBiz\Authentication\UserProvider;
use Codeages\PhalconBiz\Authentication\ApiUser;
use Phalcon\Http\RequestInterface;
use Codeages\PhalconBiz\BizAwareInterface;
use Codeages\PhalconBiz\BizAwareTrait;

class ApiUserProvider implements UserProvider, BizAwareInterface
{
    use BizAwareTrait;

    public function loadUser($identifier, RequestInterface $request)
    {
        $user = $this->biz->service('User:UserService')->getUserByAccessKey($identifier);
        if (empty($user)) {
            return null;
        }

        $user['login_ip'] = $request->getClientAddress(true);
        $user['login_client'] = $request->getUserAgent();

        return new ApiUser($user);
    }
}