<?php

namespace Biz\User\Service\Impl;

use Biz\User\Dao\UserDao;
use Biz\User\Service\UserService;
use Codeages\Biz\Framework\Service\BaseService;

class UserServiceImpl extends BaseService implements UserService
{

    public function getUser($id)
    {
        return $this->getUserDao()->get($id);
    }

    public function searchUsers($conditions, $sorts, $start, $limit)
    {
        return $this->getUserDao()->search($conditions, $sorts, $start, $limit);
    }

    public function countUsers($conditions)
    {
        return $this->getUserDao()->count($conditions);
    }

    public function createUser($user)
    {
        $user['password'] = md5('password');
        $user['salt'] = md5(time());
        return $this->getUserDao()->create($user);
    }

    public function updateUser($id, array $fields)
    {
        return $this->getUserDao()->update($id, $fields);
    }

    public function banUser($id)
    {

    }

    protected function getUserDao()
    {
        return $this->biz->dao('User:UserDao');
    }
}
