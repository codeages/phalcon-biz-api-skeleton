<?php
namespace Codeages\PhalconBiz;

trait TestHelperTrait
{
    protected $biz;

    protected $configFilepath;

    public function createBiz()
    {
        if (!$this->configFilepath) {
            throw new \InvalidArgumentException('You must assign configFilePath value');
        }
        $config = require $this->configFilepath;
        $biz = new \Biz\AppBiz($config);
        return $biz;
    }
}