<?php
namespace Codeages\PhalconBiz\Event;

use Phalcon\Http\ResponseInterface;
use Phalcon\Http\RequestInterface;

class GetResponseForControllerResultEvent extends GetResponseEvent
{
    /**
     * The return value of the controller.
     *
     * @var mixed
     */
    private $controllerResult;

    public function __construct($app, RequestInterface $request, $controllerResult)
    {
        parent::__construct($app, $request);

        $this->controllerResult = $controllerResult;
    }

    /**
     * Returns the return value of the controller.
     *
     * @return mixed The controller return value
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
    }

    /**
     * Assigns the return value of the controller.
     *
     * @param mixed $controllerResult The controller return value
     */
    public function setControllerResult($controllerResult)
    {
        $this->controllerResult = $controllerResult;
    }
}
