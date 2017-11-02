<?php
namespace Biz\User\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface UserDao extends GeneralDaoInterface
{
    public function getByWechatId($wechatId);

    public function findByIds(array $ids);
}
