<?php

namespace RestTestHelper\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use RestTestHelper\Crawler\ResponseCrawlerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RestWebTestCase
 *
 * @author Krzysztof Głogowski <k.glogowski2@gmail.com>
 */
abstract class RestWebTestCase extends WebTestCase
{
    /**
     * @return Client
     */
    protected function getClient(): Client
    {
        return static::makeClient();
    }

    /**
     * @param Client $client
     * @param string $route
     * @param array  $routeParameters
     * @param string $method
     * @param string $content
     *
     * @return Response
     */
    protected function request(Client $client, string $route, array $routeParameters = [], string $method = Request::METHOD_GET, string $content = ''): Response
    {
        $client->request($method, $this->getUrl($route, $routeParameters), [], [], $this->getRequestHeaders(), $content);

        $response = $client->getResponse();

        return $response;
    }

    /**
     * @param string $fileName
     * @param bool   $useMockDir
     *
     * @return string
     */
    protected function getJsonMockFileContent(string $fileName, $useMockDir = true): string
    {
        return file_get_contents(sprintf('%s%s', $useMockDir ? $this->getMockDir() : '', $fileName));
    }

    /**
     * @param string $route
     * @param string $method
     * @param string $filename
     * @param array  $routeParams
     * @param int    $expectedResponse
     *
     * @return Response
     */
    protected function callRequestTest(
        string $route,
        string $method,
        string $filename,
        array $routeParams = [],
        $expectedResponse = Response::HTTP_CREATED
    ): Response
    {
        $client = $this->getClient();
        $response = $this->request($client, $route, $routeParams, $method, $this->getJsonMockFileContent($filename));

        $this->assertJson($response->getContent());
        $this->assertEquals($expectedResponse, $response->getStatusCode());

        return $response;
    }

    /**
     * @param Response $response
     * @return \stdClass
     */
    protected function getResponseContent(Response $response)
    {
        return json_decode($response->getContent());
    }

    /**
     * @return string
     */
    abstract protected function getMockDir(): string;

    /**
     * {@inheritdoc}
     */
    public function getRequestHeaders(): array
    {
        return [];
    }

    /**
     * @return ResponseCrawlerInterface
     */
    public function createCrawler(): ResponseCrawlerInterface
    {
        $crawler = $this->getContainer()->get('rest_test_helper.node.response_crawler');

        return $crawler->reset($this->getClient());
    }
}