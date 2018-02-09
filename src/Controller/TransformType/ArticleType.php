<?php

namespace App\Controller\TransformType;

class ArticleType extends \Codeages\PhalconBiz\AbstractTransformType
{
    public function transformItem($item)
    {
        return [
            'id' => (int) $item['id'],
            'title' => $item['title'],
            'content' => $item['content'],
            'created_at' => date('c', $item['created_at']),
            'updated_at' => date('c', $item['updated_at']),
        ];
    }
}
