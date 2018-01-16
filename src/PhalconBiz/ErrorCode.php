<?php

namespace Codeages\PhalconBiz;

/**
 * 系统通用错误码
 *
 * 约定100以内的错误码为系统通用错误码，业务错误码请设置为100以上。
 */
class ErrorCode
{
    /**
     * 接口、资源不存在 (http code: 404).
     */
    const NOT_FOUND = 1;

    /**
     * 请求报文格式不正确 (http code: 400)
     * 例如：
     * 1. 请求体非 json 格式
     * 2. 未设置 application/json 头部
     * 3. 认证签名错误、签名过期
     * 4. 认证用户不存在
     */
    const BAD_REQUEST = 2;

    /**
     * API请求次数已达上限 (http code: 403)
     */
    const TOO_MANY_CALLS = 3;

    /**
     * 请求验证非法 (http code: 401)
     *
     * 1. 验证信息不存在、格式不正确
     * 2. 验证签名不正确
     * 3. 验证对应的用户不存在
     * 4. 验证信息已过期
     * 5. 被禁止访问
     */
    const INVALID_AUTHENTICATION = 4;

    /**
     * 服务内部错误，需联系管理员 (http code: 500)
     */
    const INTERNAL_SERVER_ERROR = 5;

    /**
     * 服务暂时下线，请稍后重试 (http code: 503)
     *
     * 1. 升级维护中
     * 2. 过载保护中
     * 3. 内部服务处理超时.
     */
    const SERVICE_UNAVAILABLE = 6;

    /**
     * 权限不足，无权访问 (http code: 405)
     */
    const ACCESS_DENIED = 7;

    /**
     * 参数缺失、参数不正确 (http code: 422)
     */
    const INVALID_ARGUMENT = 8;
}
