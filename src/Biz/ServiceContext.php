<?php

namespace App\Biz;

class ServiceContext
{
    /**
     * @var string|int|null
     */
    private $userId;

    /**
     * @var string|null
     */
    private $username;

    /**
     * @var string|null
     */
    private $ip;

    /**
     * @var string|null
     */
    private $traceId;

    /**
     * @return int|string|null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int|string|null $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string|null $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string|null
     */
    public function getTraceId()
    {
        return $this->traceId;
    }

    /**
     * @param string|null $traceId
     */
    public function setTraceId($traceId)
    {
        $this->traceId = $traceId;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}