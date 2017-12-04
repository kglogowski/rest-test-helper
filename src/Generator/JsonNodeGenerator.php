<?php

namespace RestTestHelper\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use RestTestHelper\Model\NodeModel;

/**
 * Class JsonNodeGenerator
 */
class JsonNodeGenerator implements NodeGeneratorInterface
{
    /**
     * @param string $content
     *
     * @return NodeModel
     */
    public function generate(string $content): NodeModel
    {
        $decodeContent = json_decode($content, true);

        $root = new NodeModel();

        if (null !== $decodeContent) {
            foreach ($decodeContent as $key => $nodeValues) {
                $root->addChild($this->createNode($root, $key, $nodeValues));
            }
        }

        return $root;
    }

    /**
     * @param NodeModel $content
     *
     * @return array
     */
    public function revert(NodeModel $content): array
    {
        return $this->createArray($content);
    }

    /**
     * @param NodeModel $parent
     * @param $key
     * @param $value
     *
     * @return NodeModel
     */
    protected function createNode(NodeModel $parent, $key, $value)
    {
        $node = new NodeModel();
        $node
            ->setParent($parent)
            ->setKey($key)
        ;

        if (is_array($value)) {
            foreach ($value as $childrenKey => $childrenValue) {
                $node->addChild($this->createNode($node, $childrenKey, $childrenValue));
            }
        } else {
            $node->setValue($value);
        }

        return $node;
    }

    /**
     * @param NodeModel $node
     *
     * @return array
     */
    protected function createArray(NodeModel $node)
    {
        $responseArray = [];
        $responseArray['key'] = $node->getKey();
        $responseArray['value'] = $node->getValue();

        if (0 !== $node->getChildren()->count()) {
            $responseArray['children'] = [];

            foreach ($node->getChildren() as $child) {
                $responseArray['children'][] = $this->createArray($child);
            }
        }

        return $responseArray;
    }
}