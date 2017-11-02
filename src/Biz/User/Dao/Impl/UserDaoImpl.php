<?php

namespace Biz\User\Dao\Impl;

use Biz\User\Dao\UserDao;
use Codeages\Biz\Framework\Dao\GeneralDaoImpl;

class UserDaoImpl extends GeneralDaoImpl implements UserDao
{
    protected $table = 'user';

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

    public function getByWechatId($wechatId)
    {
        return $this->getByFields(['wechat_id' => $wechatId]);
    }

    public function findByIds(array $ids)
    {
        return $this->findInField('id', $ids);
    }
}
