<?php
namespace Test\ApiTest;

use Test\ApiTester;
use Codeception\Util\HttpCode;

class ArticleCest extends BaseCest
{
    protected $I;

    public function searchArticles(ApiTester $I)
    {
        $this->fakeArticle($I, ['id' => 1]);
        $this->fakeArticle($I, ['id' => 2]);

        $I->sendGET('/articles');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function getExistArticle(ApiTester $I)
    {
        $article = $this->fakeArticle($I);

        $I->sendGET("/articles/{$article['id']}");

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->assertEquals($article['id'], $I->grabDataFromResponseByJsonPath('$.id')[0]);
    }

    public function getNotExistArticle(ApiTester $I)
    {
        $I->sendGET("/articles/9999");

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseJsonMatchesJsonPath('$.error');
    }

    public function createArticle(ApiTester $I)
    {
        $article = ['title' => 'test title', 'content' => 'test content'];
        $I->sendPOST('/articles', $article);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->assertEquals($article['title'], $I->grabDataFromResponseByJsonPath('$.title')[0]);
    }

    protected function fakeArticle($I, $article = [])
    {
        $article = array_merge([
            'id' => 1,
            'title' => 'test title',
            'content' => 'test content',
            'user_id' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ], $article);

        $I->haveInDatabase($this->getArticleTable(), $article);

        return $article;
    }

    protected function getArticleTable()
    {
        return $this->I->createDao('Article:ArticleDao')->table();
    }
    
}
