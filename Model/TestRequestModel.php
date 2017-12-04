<?php

namespace RestTestHelper\Model;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class TestRequestModel
 */
class TestRequestModel
{
    /**
     * @var string|null
     */
    protected $filename;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $header;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $routeParams;

    /**
     * TestRequestModel constructor.
     */
    public function __construct()
    {
        $this->method = Request::METHOD_GET;
        $this->header = [];
        $this->routeParams = [];
    }

    /**
     * @return null|string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param null|string $filename
     *
     * @return TestRequestModel
     */
    public function setFilename($filename): TestRequestModel
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return TestRequestModel
     */
    public function setMethod(string $method): TestRequestModel
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @param array $header
     *
     * @return TestRequestModel
     */
    public function setHeader(array $header): TestRequestModel
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $route
     *
     * @return TestRequestModel
     */
    public function setRoute(string $route): TestRequestModel
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return array
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * @param array $routeParams
     *
     * @return TestRequestModel
     */
    public function setRouteParams(array $routeParams): TestRequestModel
    {
        $this->routeParams = $routeParams;

        return $this;
    }
}