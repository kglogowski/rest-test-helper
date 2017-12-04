<?php

namespace RestTestHelper\Checker;

use RestTestHelper\Checker\Interfaces\NodeContentCheckerInterface;
use RestTestHelper\Crawler\ResponseCrawlerInterface;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 *
 * Class AbstractNodeContentChecker
 */
abstract class AbstractNodeContentChecker implements NodeContentCheckerInterface
{
    /**
     * @param ResponseCrawlerInterface $crawler
     * @param array                    $header
     */
    public function checkContent(ResponseCrawlerInterface $crawler, array $header = [])
    {
        if ($crawler->getActive()->isAvailable()) {
            $this->check($crawler, $header);
        }
    }
}