<?php

namespace RestTestHelper\Checker;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use RestTestHelper\Model\NodeModel;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 *
 * Class NodeTypeChecker
 */
class NodeTypeChecker extends Assert
{
    const
        TYPE_STRING = 'string',
        TYPE_INTEGER = 'integer',
        TYPE_FLOAT = 'float',
        TYPE_MONEY = 'money'
    ;

    /**
     * @param NodeModel $nodeModel
     * @param string    $expectedType
     */
    public function check(NodeModel $nodeModel, string $expectedType)
    {
        switch ($expectedType) {
            case self::TYPE_INTEGER:
                if (!is_int($nodeModel->getValue())) {
                    throw new AssertionFailedError(sprintf('Failed type for "%s"', $nodeModel->getKey()));
                }

                break;
            case self::TYPE_STRING:
                if (!is_string($nodeModel->getValue())) {
                    throw new AssertionFailedError(sprintf('Failed type for "%s"', $nodeModel->getKey()));
                }

                break;
            case self::TYPE_FLOAT:
                if (true !== filter_var($nodeModel->getValue(), FILTER_VALIDATE_FLOAT)) {
                    throw new AssertionFailedError(sprintf('Failed type for "%s"', $nodeModel->getKey()));
                }

                break;
            case self::TYPE_MONEY:
                if (!preg_match('/^\d+(,\d{3})*(\.\d{1,2})?$/', $nodeModel->getValue())) {
                    throw new AssertionFailedError(sprintf('Failed type for "%s"', $nodeModel->getKey()));
                }

                break;
            default:
                if (!is_a($nodeModel->getValue(), $expectedType)) {
                    throw new AssertionFailedError(sprintf('Failed type for "%s"', $nodeModel->getKey()));
                }

                break;
        }
    }
}