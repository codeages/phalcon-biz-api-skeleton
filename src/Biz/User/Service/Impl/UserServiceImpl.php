<?php
namespace Biz\User\Service\Impl;

use Biz\User\Dao\UserDao;
use Biz\User\Service\UserService;
use Codeages\Biz\Framework\Service\BaseService;
use Codeages\Biz\Framework\Service\Exception\InvalidArgumentException;
use Codeages\Biz\Framework\Service\Exception\NotFoundException;
use Webpatser\Uuid\Uuid;

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
        $user = $this->biz['validator']->validate($user, [
            'username' => 'required|string|length_between:3,18',
            'password' => 'required|string|length_between:3,32'
        ]);

        $existUser = $this->getUserDao()->getByUsername($user['username']);
        if ($existUser) {
            throw new InvalidArgumentException("用户名已存在，注册失败！");
        }

        $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
        
        return $this->getUserDao()->create($user);
    }

    public function banUser($id)
    {
        $user = $this->getUserDao()->get($id);
        if (empty($user)) {
            throw new NotFoundException("用户不存在#{$id}，封禁失败。");
        }

        $this->getUserDao()->update($id, [
            'is_banned' => 1,
        ]);
    }

    public function unbanUser($id)
    {
        $user = $this->getUserDao()->get($id);
        if (empty($user)) {
            throw new NotFoundException("用户不存在#{$id}，解禁失败。");
        }

        $this->getUserDao()->update($id, [
            'is_banned' => 0,
        ]);
    }

    /**
     * @return \Biz\User\Dao\UserDao
     */
    protected function getUserDao()
    {
        return $this->biz->dao('User:UserDao');
    }
}
