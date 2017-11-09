<?php
namespace Codeages\PhalconBiz\Authentication;

interface UserProvider
{
    /**
     * @param $id
     * @return CurrentUser
     */
    public function get($id);
}