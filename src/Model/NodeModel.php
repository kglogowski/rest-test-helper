<?php

namespace RestTestHelper\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class NodeModel
 */
class NodeModel
{
    /**
     * @var NodeModel
     */
    protected $parent;

    /**
     * @var NodeModel[]|ArrayCollection
     */
    protected $children;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var bool
     */
    protected $isAvailable = true;

    /**
     * @var string
     */
    protected $status = '';

    /**
     * @param NodeModel $nodeModel
     *
     * @return NodeModel
     */
    public function setParent(NodeModel $nodeModel): NodeModel
    {
        $this->parent = $nodeModel;

        return $this;
    }

    /**
     * @return NodeModel|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return 0 !== $this->getChildren()->count();
    }

    /**
     * @return NodeModel[]|ArrayCollection
     */
    public function getChildren()
    {
        if (null === $this->children) {
            $this->children = new ArrayCollection();
        }

        return $this->children;
    }

    /**
     * @param string $name
     *
     * @return NodeModel
     */
    public function getChild(string $name)
    {
        return $this->getChildren()->filter(function (NodeModel $nodeModel) use ($name) {
            return $name === $nodeModel->getKey();
        })->first();
    }

    /**
     * @param NodeModel $nodeModel
     *
     * @return NodeModel
     */
    public function addChild(NodeModel $nodeModel): NodeModel
    {
        if (null === $this->children) {
            $this->children = new ArrayCollection();
        }

        $this->children->add($nodeModel);
        $nodeModel->setParent($this);

        return $this;
    }

    /**
     * @return NodeModel
     */
    public function clearChildren(): NodeModel
    {
        $this->children = new ArrayCollection();

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return NodeModel
     */
    public function setKey(string $key): NodeModel
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return NodeModel
     */
    public function setValue($value): NodeModel
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    /**
     * @param boolean $isAvailable
     */
    public function setAvailable(bool $isAvailable)
    {
        $this->isAvailable = $isAvailable;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return NodeModel
     */
    public function setStatus(string $status): NodeModel
    {
        $this->status = $status;

        return $this;
    }
}