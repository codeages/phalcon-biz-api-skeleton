<?php
namespace Codeages\PhalconBiz\Authentication;

use Phalcon\Http\RequestInterface;

interface Authenticator
{
    public function authenticate(RequestInterface $request);
}
