<?php
namespace Test\UnitTest;

use Codeages\PhalconBiz\TestHelperTrait;

abstract class BaseTest extends \Codeception\Test\Unit
{
    use TestHelperTrait;

    /**
     * @var \Test\UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->configFilepath = dirname(dirname(__DIR__)).'/config/biz.php';
        $this->biz = $this->createBiz();
    }
}