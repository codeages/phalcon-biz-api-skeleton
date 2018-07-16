<?php
namespace Tests\Biz\Service;

use Tests\BaseTest;

class ArticleServiceTest extends BaseTest
{

    public function testGet()
    {
        $article = $this->getArticleService()->get(1);
        $this->assertNull($article);

    }

    protected function getArticleService()
    {
        return $this->biz->service('Article:ArticleService');
    }

    protected function getArticleDao()
    {
        return $this->biz->dao('Article:ArticleDao');
    }

}