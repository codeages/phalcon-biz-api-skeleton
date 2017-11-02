<?php
namespace Codeages\PhalconBiz;

abstract class AbstractTransformType implements TransformType
{
    public function transformItems(array $items)
    {
        $transformedItems = [];
        foreach ($items as $item) {
            $transformedItems[] = $this->transformItem($item);
        }

        return $transformedItems;
    }
}