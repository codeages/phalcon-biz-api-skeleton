<?php
namespace Codeages\PhalconBiz\Authentication;

use Phalcon\Http\RequestInterface;

interface UserProvider
{
    /**
     * 载入 CurrentUser
     * 
     * @param mixed $identifier 用户标识符，可以是用户 id、username、access_key 等能唯一确定用户的标识符。
     * @return CurrentUser
     */
    public function loadUser($identifier, RequestInterface $request);
}