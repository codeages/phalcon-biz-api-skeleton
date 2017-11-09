<?php

namespace Codeages\PhalconBiz\Authentication;

use Codeages\Biz\Framework\Context\CurrentUser;

class ApiUser extends CurrentUser
{
    public function __construct(array $user)
    {
        $this->requireds($user, ['access_key', 'secret_key']);

        $user['disabled'] = !empty($user['disabled']) ? true : false;
        $user['locked'] = !empty($user['locked']) ? true : false;
        $user['expired'] = !empty($user['expired_time']) && ($user['expired_time'] < time()) ? true : false;

        parent::__construct($user);
    }
}
