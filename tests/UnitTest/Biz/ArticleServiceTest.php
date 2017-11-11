<?php

namespace Test\UnitTest\Biz;

use Codeages\Biz\Framework\Validation\ValidationException;

class ArticleServiceTest extends BaseTest
{
    /**
     * @var \Test\UnitTester
     */
    protected $tester;

    public function testGetArticle()
    {
        $fakeArticle = $this->fakeArticle();
        $article = $this->getArticleService()->get($fakeArticle['id']);
        $this->tester->assertEquals($fakeArticle['id'], $article['id']);
    }

    public function testFindLatest()
    {
        $fakeArticle = $this->fakeArticle();
        $fakeArticle = $this->fakeArticle(['id' => 2]);

        $article = $this->getArticleService()->findLatest(0, 10);

        $this->tester->assertEquals(2, count($article));
    }

    public function testFindLatestByUserId()
    {
        $fakeArticle = $this->fakeArticle(['id' => 1, 'user_id' => 1]);
        $fakeArticle = $this->fakeArticle(['id' => 2, 'user_id' => 1]);
        $fakeArticle = $this->fakeArticle(['id' => 3, 'user_id' => 2]);

        $article = $this->getArticleService()->findLatestByUserId(1, 0, 10);

        $this->tester->assertEquals(2, count($article));
    }

    public function testCount()
    {
        $fakeArticle = $this->fakeArticle(['id' => 1, 'user_id' => 1]);
        $fakeArticle = $this->fakeArticle(['id' => 2, 'user_id' => 1]);
        $fakeArticle = $this->fakeArticle(['id' => 3, 'user_id' => 2]);

        $count = $this->getArticleService()->count([]);
        $this->tester->assertEquals(3, $count);

        $count = $this->getArticleService()->count(['user_id' => 1]);
        $this->tester->assertEquals(2, $count);
    }

    public function testSearch()
    {
        $fakeArticle = $this->fakeArticle(['id' => 1, 'user_id' => 1]);
        $fakeArticle = $this->fakeArticle(['id' => 2, 'user_id' => 1]);
        $fakeArticle = $this->fakeArticle(['id' => 3, 'user_id' => 2]);

        $articles = $this->getArticleService()->search([], ['created_at' => 'DESC'], 0, 10);
        $this->tester->assertEquals(3, count($articles));

        $articles = $this->getArticleService()->search(['user_id' => 1], ['created_at' => 'DESC'], 0, 10);
        $this->tester->assertEquals(2, count($articles));
    }

    public function testCreate()
    {
        $article = [
            'title' => 'test title',
            'content' => 'test content',
        ];

        $createdArticle = $this->getArticleService()->create($article);

        $this->tester->assertEquals($article['title'], $createdArticle['title']);
        $this->tester->grabNumRecords($this->getArticleTable(), ['id' => $createdArticle['id']]);
    }

    public function testCreateWithEmptyTitle()
    {
        $this->tester->expectException(ValidationException::class, function () {
            $article = [
                'title' => '',
                'content' => 'test content',
            ];
            $this->getArticleService()->create($article);
        });
    }

    public function testCreateWithEmptyContent()
    {
        $this->tester->expectException(ValidationException::class, function () {
            $article = [
                'title' => 'test title',
            ];
            $this->getArticleService()->create($article);
        });
    }

    public function testSetRecommended()
    {
        $article = $this->fakeArticle();
        $this->getArticleService()->setRecommended($article['id']);

        $this->tester->seeInDatabase($this->getArticleTable(), ['id' => $article['id'], 'is_recommended' => 1]);
    }

    public function testCancelRecommended()
    {
        $article = $this->fakeArticle(['is_recommended' => 1]);
        $this->getArticleService()->cancelRecommended($article['id']);

        $this->tester->seeInDatabase($this->getArticleTable(), ['id' => $article['id'], 'is_recommended' => 0]);
    }

    protected function fakeArticle($article = [])
    {
        $article = array_merge([
            'id' => 1,
            'title' => 'test title',
            'content' => 'test content',
            'user_id' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ], $article);

        $this->tester->haveInDatabase($this->getArticleTable(), $article);

        return $article;
    }

    protected function getArticleTable()
    {
        return $this->tester->createDao('Article:ArticleDao')->table();
    }

    /**
     * @return \Biz\Article\Service\ArticleService
     */
    protected function getArticleService()
    {
        return $this->tester->createService('Article:ArticleService');
    }
}
