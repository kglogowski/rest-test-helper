<?php

namespace RestTestHelper\Checker\Interfaces;

use RestTestHelper\Crawler\ResponseCrawlerInterface;

/**
 * Interface NodeContentCheckerInterface
 */
interface NodeContentCheckerInterface
{
    /**
     * @param ResponseCrawlerInterface $crawler
     * @param array                    $header
     */
    public function check(ResponseCrawlerInterface $crawler, array $header = []);

    /**
     * @param ResponseCrawlerInterface $crawler
     * @param array                    $header
     */
    public function checkContent(ResponseCrawlerInterface $crawler, array $header = []);
}