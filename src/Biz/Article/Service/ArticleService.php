<?php

namespace App\Biz\Article\Service;

interface ArticleService
{
    /**
     * 获取某一文章
     *
     * @param int $articleId 文章ID
     */
    public function get($articleId);

    /**
     * 获取最新发布的文章
     *
     * @param int $start
     * @param int $limit
     */
    public function findLatest($start, $limit);

    /**
     * 获取某一用户最新发布的文章
     *
     * @param int $userId 用户ID
     * @param int $start  获取起始偏移量
     * @param int $limit  获取条数
     */
    public function findLatestByUserId($userId, $start, $limit);

    /**
     * 根据条件获取文章数量
     *
     * @param [type] $conditions 获取条件
     */
    public function count($conditions);

    /**
     * 根据条件获取文章
     *
     * @param array $conditions 获取条件
     * @param array $sorts      获取排序方式
     * @param int   $start      获取起始偏移量
     * @param int   $limit      获取条数
     */
    public function search($conditions, $sorts, $start, $limit);

    /**
     * 发表一篇文章
     *
     * @param array $article 文章
     *
     * @return array 文章
     */
    public function create($article);

    /**
     * 设置为推荐文章
     *
     * @param int $articleId 文章ID
     */
    public function setRecommended($articleId);

    /**
     * 取消推荐文章
     *
     * @param int $articleId 文章ID
     */
    public function cancelRecommended($articleId);

    /**
     * 对文章发表评论
     *
     * @param int   $articleId 文章ID
     * @param array $comment   评论
     *
     * @return array 评论
     */
    public function createComment($articleId, $comment);

    /**
     * 获取文章的最新评论
     *
     * @param int $articleId 文章ID
     * @param int $start     获取起始偏移量
     * @param int $limit     获取条数
     */
    public function findLatestCommentsByArticleId($articleId, $start, $limit);
}
