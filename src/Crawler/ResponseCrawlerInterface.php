<?php

namespace RestTestHelper\Crawler;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use RestTestHelper\Checker\Interfaces\NodeContentCheckerInterface;
use RestTestHelper\Model\NodeModel;

/**
 * Interface ResponseCrawlerInterface
 */
interface ResponseCrawlerInterface
{
    const
        ASSERT_NOT_NULL = 'NotNull',
        ASSERT_NULL = 'Null',
        ASSERT_TRUE = 'True',
        ASSERT_FALSE = 'False',
        ASSERT_EQUALS = 'Equals',
        ASSERT_JSON = 'Json',
        ASSERT_NOT_EMPTY = 'NotEmpty',
        ASSERT_EMPTY = 'Empty',
        ASSERT_GREATER_THAN = 'GreaterThan',
        ASSERT_GREATER_THAN_OR_EQUAL = 'GreaterThanOrEqual',
        ASSERT_REGEXP = 'RegExp',
        ASSERT_CONTAINS = 'Contains'
    ;

    /**
     * @param Client $client
     *
     * @return ResponseCrawlerInterface
     */
    public function reset(Client $client): ResponseCrawlerInterface;

    /**
     * @param string      $method
     * @param array       $header
     * @param string|null $href
     * @param string|null $content
     *
     * @return ResponseCrawlerInterface
     */
    public function click(string $method = Request::METHOD_GET, array $header = [], string $href = null, string $content = null): ResponseCrawlerInterface;

    /**
     * @param string $expectedStatus
     *
     * @return ResponseCrawlerInterface
     */
    public function checkStatus(string $expectedStatus): ResponseCrawlerInterface;

    /**
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * @return bool
     */
    public function isFailure(): bool;

    /**
     * @param string $name
     *
     * @return ResponseCrawlerInterface
     */
    public function ifExists(string $name): ResponseCrawlerInterface;

    /**
     * @param bool $bool
     *
     * @return ResponseCrawlerInterface
     */
    public function ifCondition(bool $bool): ResponseCrawlerInterface;

    /**
     * @return ResponseCrawlerInterface
     */
    public function ifHasChildren(): ResponseCrawlerInterface;

    /**
     * @return ResponseCrawlerInterface
     */
    public function ifEnd(): ResponseCrawlerInterface;

    /**
     * @param mixed $value
     * @param bool  $revert
     *
     * @return ResponseCrawlerInterface
     */
    public function dump($value, $revert = true): ResponseCrawlerInterface;

    /**
     * @param string $name
     *
     * @return ResponseCrawlerInterface
     */
    public function child(string $name): ResponseCrawlerInterface;

    /**
     * @return ResponseCrawlerInterface
     */
    public function end(): ResponseCrawlerInterface;

    /**
     * @param string $type
     *
     * @return ResponseCrawlerInterface
     */
    public function checkType(string $type): ResponseCrawlerInterface;

    /**
     * @return ResponseCrawlerInterface
     */
    public function goToRoot(): ResponseCrawlerInterface;

    /**
     * @return NodeModel
     */
    public function getRoot(): NodeModel;

    /**
     * @return NodeModel
     */
    public function getActive(): NodeModel;

    /**
     * @return mixed
     */
    public function getActiveValue();

    /**
     * @param NodeContentCheckerInterface $checker
     * @param array                       $header
     *
     * @return ResponseCrawlerInterface
     */
    public function checkContent(NodeContentCheckerInterface $checker, array $header = []): ResponseCrawlerInterface;

    /**
     * @param NodeContentCheckerInterface $checker
     * @param array                       $header
     *
     * @return ResponseCrawlerInterface
     */
    public function checkContentItems(NodeContentCheckerInterface $checker, array $header = []): ResponseCrawlerInterface;

    /**
     * @param \Closure $function
     *
     * @return ResponseCrawlerInterface
     */
    public function eachChild(\Closure $function): ResponseCrawlerInterface;

    /**
     * @return ResponseCrawlerInterface
     */
    public function assertHasChildren(): ResponseCrawlerInterface;

    /**
     * @param string $name
     * @param bool   $exists
     *
     * @return ResponseCrawlerInterface
     */
    public function assertCheckChildren(string $name, bool $exists = true): ResponseCrawlerInterface;

    /**
     * @param string $type
     * @param array  $arguments
     *
     * @return ResponseCrawlerInterface
     *
     * @throws \Exception
     */
    public function assert(string $type, array $arguments = []): ResponseCrawlerInterface;

    /**
     * @param string $type
     * @param array  $arguments
     *
     * @return ResponseCrawlerInterface
     */
    public function assertActive(string $type, array $arguments = []): ResponseCrawlerInterface;
}