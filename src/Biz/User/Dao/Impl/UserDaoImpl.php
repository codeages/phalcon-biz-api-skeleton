<?php

namespace Biz\User\Dao\Impl;

use Biz\User\Dao\UserDao;
use Codeages\Biz\Framework\Dao\GeneralDaoImpl;

class UserDaoImpl extends GeneralDaoImpl implements UserDao
{
    protected $table = 'user';

    public function getByUsername($username)
    {
        return $this->getByFields(['username' => $username]);
    }
    
    public function getByEmail($email)
    {
        return $this->getByFields(['email' => $email]);
    }

    public function declares()
    {
        return [
            'timestamps' => ['created_at', 'updated_at'],
            'orderbys' => ['created_at', 'updated_at'],
            'serializes' => [
                'wechat_tag_ids' => 'delimiter',
                'nickname' => 'utf8',
            ],
        ];
    }
}
