<?php

namespace App\Controller\TransformType;

class UserType extends \Codeages\PhalconBiz\AbstractTransformType
{
    public function transformItem($item)
    {
        return [
            'id' => (int) $item['id'],
            'username' => $item['username'],
            'created_at' => date('c', $item['created_at']),
            'updated_at' => date('c', $item['updated_at']),
        ];
    }
}
