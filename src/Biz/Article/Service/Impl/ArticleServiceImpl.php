<?php

namespace App\Biz\Article\Service\Impl;

use App\Biz\Article\Dao\ArticleDao;
use App\Biz\Article\Service\ArticleService;
use Codeages\Biz\Framework\Service\BaseService;
use Codeages\Biz\Framework\Service\Exception\NotFoundException;

/**
 * Example: 文章服务
 */
class ArticleServiceImpl extends BaseService implements ArticleService
{
    public function get($articleId)
    {
        return $this->getArticleDao()->get($articleId);
    }

    public function findLatest($start, $limit)
    {
        return $this->getArticleDao()->findLatest($start, $limit);
    }

    public function findLatestByUserId($userId, $start, $limit)
    {
        return $this->getArticleDao()->findLatestByUserId($userId, $start, $limit);
    }

    public function count($conditions)
    {
        return $this->getArticleDao()->count($conditions);
    }

    public function search($conditions, $sorts, $start, $limit)
    {
        return $this->getArticleDao()->search($conditions, $sorts, $start, $limit);
    }

    public function create($article)
    {
        $article = $this->biz['validator']->validate($article, [
            'title' => 'required|string|length_max:256',
            'content' => 'required|string',
        ]);

        $article['user_id'] = $this->biz['user']['id'];

        return $this->getArticleDao()->create($article);
    }

    public function setRecommended($articleId)
    {
        $article = $this->getArticleDao()->get($articleId);
        if (empty($article)) {
            throw new NotFoundException("文章不存在#{$articleId}，设置推荐失败。");
        }

        $this->getArticleDao()->update($articleId, [
            'is_recommended' => 1,
        ]);
    }

    public function cancelRecommended($articleId)
    {
        $article = $this->getArticleDao()->get($articleId);
        if (empty($article)) {
            throw new NotFoundException("文章不存在#{$articleId}，取消推荐失败。");
        }

        $this->getArticleDao()->update($articleId, [
            'is_recommended' => 0,
        ]);
    }

    public function createComment($articleId, $comment)
    {
    }

    public function findLatestCommentsByArticleId($articleId, $start, $limit)
    {
    }

    /**
     * @return \Biz\Article\Dao\ArticleDao
     */
    protected function getArticleDao()
    {
        return $this->biz->dao('Article:ArticleDao');
    }
}
