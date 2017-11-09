<?php
namespace Codeages\PhalconBiz;

use Codeages\Biz\Framework\Context\Biz;

trait BizAwareTrait
{
    /**
     * @var Biz
     */
    protected $biz;

    public function setBiz(Biz $biz)
    {
        $this->biz = $biz;
    }
}