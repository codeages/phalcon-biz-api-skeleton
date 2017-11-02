<?php
namespace Codeages\PhalconBiz;

interface TransformType
{
    public function transformItem($item);

    public function transformItems(array $items);
}