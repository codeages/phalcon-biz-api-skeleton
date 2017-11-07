<?php
namespace Codeages\PhalconBiz\Event;

use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;

/**
 * Allows to filter a Response object.
 *
 * You can call getResponse() to retrieve the current response. With
 * setResponse() you can set a new response that will be returned to the
 * browser.
 */
class FilterResponseEvent extends WebEvent
{
    /**
     * The current response object.
     *
     * @var ResponseInterface
     */
    private $response;

    public function __construct($app, RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($app, $request);

        $this->setResponse($response);
    }

    /**
     * Returns the current response object.
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets a new response object.
     *
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
