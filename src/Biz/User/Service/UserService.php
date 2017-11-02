<?php

namespace Biz\User\Service;

interface UserService
{
    public function getUser($id);
    
    public function countUsers($conditions);
    
    public function searchUsers($conditions, $sorts, $start, $limit);

    public function createUser($user);
    
    public function banUser($id);
}
