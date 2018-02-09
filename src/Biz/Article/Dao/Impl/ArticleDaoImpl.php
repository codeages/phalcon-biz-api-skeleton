<?php

namespace App\Biz\Article\Dao\Impl;

use App\Biz\Article\Dao\ArticleDao;
use Codeages\Biz\Framework\Dao\GeneralDaoImpl;

class ArticleDaoImpl extends GeneralDaoImpl implements ArticleDao
{
    protected $table = 'article';

    public function findLatest($start, $limit)
    {
        return $this->search([], ['created_at' => 'desc'], $start, $limit);
    }

    public function findLatestByUserId($userId, $start, $limit)
    {
        return $this->search(['user_id' => $userId], ['created_at' => 'desc'], $start, $limit);
    }

    public function declares()
    {
        return [
            'timestamps' => ['created_at', 'updated_at'],
            'orderbys' => ['created_at', 'updated_at'],
            'conditions' => [
                'user_id = :user_id',
            ],
            'serializes' => [
                'wechat_tag_ids' => 'delimiter',
                'nickname' => 'utf8',
            ],
        ];
    }
}
