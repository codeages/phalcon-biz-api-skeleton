<?php
namespace Controller\TransformType;

class UserType extends \Codeages\PhalconBiz\AbstractTransformType
{
    public function transformItem($item)
    {
        return [
            'id' => (int) $item['id'],
            'username' => $item['username'],
            'email' => $item['email'],
        ];
    }
}