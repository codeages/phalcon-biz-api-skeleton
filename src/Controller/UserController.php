<?php
namespace Controller;

use Phalcon\Mvc\Controller;
use Codeages\PhalconBiz\ControllerTrait;

/**
 * @RoutePrefix('/users')
 */
class UserController extends Controller
{
    use ControllerTrait;

    /**
     * 检索用户
     * 
     * @Get('/')
     */
    public function search()
    {
        $conditions = $this->conditions();
        $sorts = $this->sorts(['created_at' => 'desc']);
        
        $pagination = $this->pagination(
            $this->getUserService()->countUsers($conditions)
        );

        $users = $this->getUserService()->searchUsers($conditions, $sorts, $pagination->offset, $pagination->limit);

        return $this->items($users, 'User', $pagination);
    }

    /**
     * 获取单个用户信息
     * 
     * @Get('/{userId}')
     */
    public function get($id)
    {
        // $user = $this->getUserService()->geteUser($id);
        // if (empty($user)) {

        // }

        // return $this->item($user, 'User');
    }

    /**
     * 创建用户
     * 
     * @Post('/')
     */
    public function create()
    {
        $user = $this->request->getPost();

        return $this->getUserService()->createUser($user);
    }

    /**
     * 更新用户个人信息
     * 
     * @Post('/{userId}')
     */
    public function update()
    {

    }

    /**
     * 检索用户的照片
     * 
     * @Get('/{userId}/photos')
     */
    public function searchPhotos()
    {
        return $this->success();
    }

    /**
     * 获取用户的一张照片信息
     * 
     * @Get('/{userId}/photos/{photo_id}')
     * @Transformer('UserPhoto')
     *
     * @return void
     */
    public function getPhoto()
    {

    }

    /**
     * 重置用户的密码
     *
     * @Post('/{userId}/actions/reset_password')
     * 
     * @return void
     */
    public function resetPassword()
    {
        return $this->success();
    }

    /**
     * 禁用用户
     * 
     * @Post('/{userId}/actions/ban')
     *
     * @return void
     */
    public function ban($userId)
    {
        return $this->success();
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}