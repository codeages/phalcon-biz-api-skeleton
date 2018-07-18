<?php

namespace App\Biz;

use Codeages\PhalconBiz\ErrorCode as BaseErrorCode;

/**
 * 业务错误码
 *
 * 约定业务错误码的 Code 为100以上，通用错误码为100以下，
 * 通用错误码见`Codeages\PhalconBiz\ErrorCode`。
 */
class ErrorCode extends BaseErrorCode
{
}
