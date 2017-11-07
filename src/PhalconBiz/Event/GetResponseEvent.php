<?php

namespace Codeages\PhalconBiz\Event;

use Phalcon\Http\ResponseInterface;

class GetResponseEvent extends WebEvent
{
    /**
     * The response object.
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * Returns the response object.
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets a response and stops event propagation.
     *
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        $this->stopPropagation();
    }

    /**
     * Returns whether a response was set.
     *
     * @return bool Whether a response was set
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }
}
