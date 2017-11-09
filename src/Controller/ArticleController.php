<?php

namespace Controller;

use Phalcon\Mvc\Controller;
use Codeages\PhalconBiz\ControllerTrait;

/**
 * 例子：文章API
 *
 * @RoutePrefix('/articles')
 */
class ArticleController extends Controller
{
    use ControllerTrait;

    /**
     * 检索文章
     *
     * @Get('/')
     */
    public function search()
    {
        $conditions = $this->conditions();
        $sorts = $this->sorts(['created_at' => 'desc']);

        $pagination = $this->pagination(
            $this->getArticleService()->countArticles($conditions)
        );

        $articles = $this->getArticleService()->searchArticles($conditions, $sorts, $pagination->offset, $pagination->limit);

        return $this->items($articles, 'Article', $pagination);
    }

    /**
     * 获取一篇文章
     *
     * @Get('/{articleId}')
     */
    public function get($articleId)
    {
        $article = $this->getArticleService()->getArticle($articleId);
        if (empty($article)) {
            $this->throwNotFoundException('User is not exist.');
        }

        return $this->item($article, 'Article');
    }

    /**
     * 创建一篇文章
     *
     * @Post('/')
     */
    public function create()
    {
        $article = $this->request->getPost();
        $article = $this->getArticleService()->createUser($article);

        return $this->item($article, 'Article');
    }

    /**
     * 更新一篇文章
     *
     * @Post('/{articleId}')
     */
    public function update()
    {
    }

    /**
     * 收藏一篇文章
     *
     * @Post('/{articleId}/actions/star')
     */
    public function star($articleId)
    {
        $this->getArticleService()->star($articleId);

        return $this->success();
    }

    /**
     * 取消收藏一篇文章
     *
     * @Post('/{articleId}/actions/unstar')
     */
    public function unstar($articleId)
    {
        $this->getArticleService()->unstar($articleId);

        return $this->success();
    }

    /**
     * 检索文章的评论
     *
     * @Get('/{articleId}/comments')
     */
    public function searchComments()
    {
    }

    /**
     * 获取文章的一条评论
     *
     * @Get('/{articleId}/comments/{commentId}')
     */
    public function getComment()
    {
    }

    protected function getArticleService()
    {
        return $this->biz->service('Article:ArticleService');
    }
}
