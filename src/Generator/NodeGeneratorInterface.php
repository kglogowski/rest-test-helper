<?php

namespace RestTestHelper\Generator;

use RestTestHelper\Model\NodeModel;

/**
 * Interface NodeGeneratorInterface
 */
interface NodeGeneratorInterface
{
    /**
     * @param string $content
     *
     * @return NodeModel
     */
    public function generate(string $content): NodeModel;

    /**
     * @param NodeModel $content
     *
     * @return array
     */
    public function revert(NodeModel $content): array;
}