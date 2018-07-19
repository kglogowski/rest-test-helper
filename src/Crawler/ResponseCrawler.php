<?php

namespace RestTestHelper\Crawler;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use RestTestHelper\Bag\ParameterBag;
use RestTestHelper\Decorator\ResponseXssi;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use RestTestHelper\Checker\Interfaces\NodeContentCheckerInterface;
use RestTestHelper\Checker\NodeTypeChecker;
use RestTestHelper\Generator\NodeGeneratorInterface;
use RestTestHelper\Model\NodeModel;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 *
 * Class ResponseCrawler
 */
class ResponseCrawler extends Assert implements ResponseCrawlerInterface
{
    /**
     * @var ParameterBag
     */
    protected $bag;

    /**
     * @var NodeTypeChecker
     */
    protected $typeChecker;

    /**
     * @var NodeGeneratorInterface
     */
    protected $nodeGenerator;

    /**
     * @var NodeModel
     */
    protected $root;

    /**
     * @var NodeModel
     */
    protected $content;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var int
     */
    protected $deep = 0;

    /**
     * @var int
     */
    protected $blockedDeep = null;

    /**
     * ResponseCrawler constructor.
     *
     * @param NodeTypeChecker        $typeChecker
     * @param NodeGeneratorInterface $nodeGenerator
     */
    public function __construct(
        NodeTypeChecker $typeChecker,
        NodeGeneratorInterface $nodeGenerator
    )
    {
        $this->typeChecker = $typeChecker;
        $this->nodeGenerator = $nodeGenerator;
        $this->bag = new ParameterBag();
    }

    /**
     * @param Client $client
     *
     * @return ResponseCrawlerInterface
     */
    public function reset(Client $client): ResponseCrawlerInterface
    {
        $this->client = $client;
        $this->root = new NodeModel();
        $this->content = $this->root;
        $this->bag = new ParameterBag();

        return $this;
    }

    /**
     * @param string      $method
     * @param array       $header
     * @param string|null $href
     * @param string|null $content
     *
     * @return ResponseCrawlerInterface
     */
    public function click(string $method = Request::METHOD_GET, array $header = [], string $href = null, string $content = null): ResponseCrawlerInterface
    {
        if ($this->isAvailable()) {
            if (null !== $href) {
                $this->content->setValue($href);
            }

            $this->client->request($method, $this->content->getValue(), [], [], $header, $content);
            $response = $this->client->getResponse();

            $responseXssi = new ResponseXssi($response);

            if (null != $responseXssi->getContent()) {
                $this->assert(self::ASSERT_JSON, [$responseXssi->getContent()]);
            }

            $subContent = $this->nodeGenerator->generate($responseXssi->getContent());

            $this->content
                ->clearChildren()
                ->setStatus($response->getStatusCode())
            ;

            foreach ($subContent->getChildren() as $child) {
                $this->content->addChild($child);
            }
        }

        return $this;
    }

    /**
     * @param string $expectedStatus
     *
     * @return ResponseCrawlerInterface
     */
    public function checkStatus(string $expectedStatus): ResponseCrawlerInterface
    {
        if ($this->isAvailable()) {
            $this->assert(self::ASSERT_EQUALS, [
                $expectedStatus,
                $this->content->getStatus()
            ]);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        if ($this->isAvailable()) {
            $status = $this->getActive()->getStatus();

            if (preg_match('/^2\d\d$/', $status)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isFailure(): bool
    {
        if ($this->isAvailable()) {
            $status = $this->getActive()->getStatus();

            if (preg_match('/^4\d\d$', $status)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $name
     *
     * @return ResponseCrawlerInterface
     */
    public function ifExists(string $name): ResponseCrawlerInterface
    {
        $this->increaseDeep();

        if ($this->isAvailable()) {
            $this->ifCondition($this->exists($name));
        }

        return $this;
    }

    /**
     * @return ResponseCrawlerInterface
     */
    public function ifHasChildren(): ResponseCrawlerInterface
    {
        $this->increaseDeep();

        if ($this->isAvailable()) {
            return $this->ifCondition(0 !== $this->getActive()->getChildren());
        }

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return ResponseCrawlerInterface
     */
    public function ifCondition(bool $bool): ResponseCrawlerInterface
    {
        $this->increaseDeep();

        if ($this->isAvailable()) {
            if (false === $bool) {
                $this->content->setAvailable(false);
                $this->setBlockedDeep($this->deep);
            }
        }

        return $this;
    }

    /**
     * @return ResponseCrawlerInterface
     */
    public function ifEnd(): ResponseCrawlerInterface
    {
        if ($this->deep === $this->blockedDeep) {
            $this->content->setAvailable(true);
        }

        $this->decreaseDeep();

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD)
     *
     * @param mixed $value
     * @param bool  $revert
     *
     * @return ResponseCrawlerInterface
     */
    public function dump($value, $revert = true): ResponseCrawlerInterface
    {
        if ($this->isAvailable()) {
            if (true === $revert) {
                switch (true) {
                    case ($value instanceof ResponseCrawlerInterface):
                        $value = $this->nodeGenerator->revert($value->getActive());
                        break;
                    case ($value instanceof NodeModel):
                        $value = $this->nodeGenerator->revert($value);
                        break;
                }
            }

            dump($value);
            die;
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    protected function exists(string $name): bool
    {
        if ($this->isAvailable()) {
            $exists = $this->content->getChildren()->filter(function (NodeModel $nodeModel) use ($name) {
                return $name === $nodeModel->getKey();
            })->first();

            return $exists instanceof NodeModel;
        }

        return true;
    }

    /**
     * @param string $name
     *
     * @return ResponseCrawlerInterface
     */
    public function child(string $name): ResponseCrawlerInterface
    {
        $this->increaseDeep();

        if ($this->isAvailable()) {
            $content = $this->content->getChild($name);

            if (!$content instanceof NodeModel) {
                throw new AssertionFailedError(sprintf('Child "%s" does not exist', $name));
            }

            $this->content = $content;
        }

        return $this;
    }

    /**
     * @return ResponseCrawlerInterface
     */
    public function end(): ResponseCrawlerInterface
    {
        $this->decreaseDeep();

        if ($this->isAvailable()) {
            $this->content = $this->content->getParent();
        }

        return $this;
    }

    /**
     * @param string $type
     *
     * @return ResponseCrawlerInterface
     */
    public function checkType(string $type): ResponseCrawlerInterface
    {
        if ($this->isAvailable()) {
            $this->typeChecker->check($this->content, $type);
        }

        return $this;
    }

    /**
     * @return ResponseCrawlerInterface
     */
    public function goToRoot(): ResponseCrawlerInterface
    {
        while (null !== $this->content->getParent()) {
            $this->content->setAvailable(true);
            $this->content = $this->content->getParent();
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function isAvailable(): bool
    {
        return $this->content->isAvailable();
    }

    /**
     * @return NodeModel
     */
    public function getRoot(): NodeModel
    {
        return $this->root;
    }

    /**
     * @return NodeModel
     */
    public function getActive(): NodeModel
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getActiveValue()
    {
        return $this->getActive()->getValue();
    }

    /**
     * @param NodeContentCheckerInterface $checker
     * @param array                       $header
     *
     * @return ResponseCrawlerInterface
     */
    public function checkContent(NodeContentCheckerInterface $checker, array $header = []): ResponseCrawlerInterface
    {
        $checker->checkContent($this, $header);

        return $this;
    }

    /**
     * @param NodeContentCheckerInterface $checker
     * @param array                       $header
     *
     * @return ResponseCrawlerInterface
     */
    public function checkContentItems(NodeContentCheckerInterface $checker, array $header = []): ResponseCrawlerInterface
    {
        if ($this->isAvailable()) {
            $content = $this->content;

            if (0 !== $this->getActive()->getChildren()->count()) {

                $this->increaseDeep();
                foreach ($content->getChildren() as $child) {
                    $this->content = $child;
                    $this->checkContent($checker, $header);
                }
                $this->decreaseDeep();
            }

            $this->content = $content;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eachChild(\Closure $function): ResponseCrawlerInterface
    {
        if ($this->isAvailable()) {
            $content = $this->content;

            if (0 !== $this->getActive()->getChildren()->count()) {

                $this->increaseDeep();
                foreach ($content->getChildren() as $child) {
                    $this->content = $child;
                    $function($this);
                }
                $this->decreaseDeep();
            }

            $this->content = $content;
        }

        return $this;
    }

    /**
     * @return ResponseCrawlerInterface
     */
    public function assertHasChildren(): ResponseCrawlerInterface
    {
        if ($this->isAvailable()) {
            $count = $this->getActive()->getChildren()->count();
            $this->assert(self::ASSERT_GREATER_THAN, [0, $count]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function assertCheckChildren(string $name, bool $exists = true): ResponseCrawlerInterface
    {
        if ($this->isAvailable()) {
            $existsChild = $this->getActive()->getChild($name);

            if (false === $exists && false !== $existsChild) {
                throw new AssertionFailedError(sprintf('Child "%s" is exists', $name));
            }

            if (true === $exists && false === $existsChild) {
                throw new AssertionFailedError(sprintf('Child "%s" does not exist', $name));
            }
        }

        return $this;
    }

    /**
     * @param string $type
     * @param array  $arguments
     *
     * @return ResponseCrawlerInterface
     */
    public function assertActive(string $type, array $arguments = []): ResponseCrawlerInterface
    {
        array_push($arguments, $this->getActiveValue());

        return $this->assert($type, $arguments);
    }

    /**
     * @param string $type
     * @param array  $arguments
     *
     * @return ResponseCrawlerInterface
     *
     * @throws \Exception
     */
    public function assert(string $type, array $arguments = []): ResponseCrawlerInterface
    {
        if ($this->isAvailable()) {
            $method = sprintf('assert%s', $type);

            if (!method_exists($this, $method)) {
                throw new \LogicException(sprintf('Method "%s" does not exist', $method));
            }

            call_user_func_array([$this, $method], $arguments);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addToBag(string $key): ResponseCrawlerInterface
    {
        $this->bag->set($key, $this->getActiveValue());

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBag(): ParameterBag
    {
        return $this->bag;
    }

    /**
     * increaseDeep
     */
    protected function increaseDeep()
    {
        $this->deep++;
    }

    /**
     * decreaseDeep
     */
    protected function decreaseDeep()
    {
        $this->deep--;
    }

    /**
     * @param int|null $blockedDeep
     */
    protected function setBlockedDeep(int $blockedDeep = null)
    {
        $this->blockedDeep = $blockedDeep;
    }
}