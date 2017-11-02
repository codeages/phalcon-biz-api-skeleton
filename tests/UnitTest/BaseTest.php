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

        if (isset($this->biz['db'])) {
            $this->biz['db']->beginTransaction();
        }

        if (isset($this->biz['redis'])) {
            $this->biz['redis']->flushDB();
        }
    }

    protected function _after()
    {
        if (isset($this->biz['db'])) {
            $this->biz['db']->rollBack();
        }

        if (isset($this->biz['redis'])) {
            $this->biz['redis']->flushDB();
        }

        unset($this->biz);
    }
}