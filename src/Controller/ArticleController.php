<?php

namespace App\Controller;

use Codeages\PhalconBiz\ControllerTrait;
use Phalcon\Mvc\Controller;

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
            $this->getArticleService()->count($conditions)
        );

        $articles = $this->getArticleService()->search($conditions, $sorts, $pagination->offset, $pagination->limit);

        return $this->items('Article', $articles, $pagination);
    }

    /**
     * 获取一篇文章
     *
     * @Get('/{articleId}')
     */
    public function get($articleId)
    {
        $article = $this->getArticleService()->get($articleId);
        if (empty($article)) {
            $this->throwNotFoundException('User is not exist.');
        }

        return $this->item('Article', $article);
    }

    /**
     * 创建一篇文章
     *
     * @Post('/')
     */
    public function create()
    {
        $article = $this->request->getPost();
        $article = $this->getArticleService()->create($article);

        return $this->item('Article', $article);
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
     * 设置推荐文章
     *
     * @Post('/{articleId}/actions/set_recommended')
     */
    public function setRecommended($articleId)
    {
        $this->getArticleService()->setRecommended($articleId);

        return $this->success();
    }

    /**
     * 取消推荐文章
     *
     * @Post('/{articleId}/actions/cancel_recommended')
     */
    public function cancelRecommended($articleId)
    {
        $this->getArticleService()->cancelRecomended($articleId);

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
