<?php
namespace Codeages\PhalconBiz\Authentication;

interface KeyProvider
{
    /**
     * @param $id
     * @return AccessKey
     */
    public function get($id);
}