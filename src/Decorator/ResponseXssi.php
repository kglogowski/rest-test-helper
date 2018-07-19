<?php

namespace RestTestHelper\Decorator;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseXssi
 */
class ResponseXssi
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * ResponseXssi constructor.
     *
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $content = $this->response->getContent();

        return preg_replace("/^\)\]\}\'\,\\n/", "", $content);
    }
}