<?php

namespace Alchemistery;

use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

abstract class EntityList implements Countable, ArrayAccess, IteratorAggregate
{
    /**
     * @var array
     */
    protected $entities = [];

    /**
     * EntityList constructor.
     *
     * @param array $entities
     * @throws \Alchemistery\UnexpectedEntityException
     */
    public function __construct(array $entities)
    {
        foreach ($entities as $entity) {
            if ($this->hasExceptedType($entity)) {
                $this->entities[] = $entity;

                continue;
            }

            throw new UnexpectedEntityException();
        }
    }

    /**
     * True if given entity has expected type
     *
     * @param \Alchemistery\Entity $entity
     * @return bool
     */
    abstract protected function hasExceptedType(Entity $entity) : bool;

    /**
     * Filter entities who's not consistent
     *
     * @return array
     */
    public function getConsistentEntities()
    {
        return array_filter($this->entities, function (Entity $entity) {
            return $entity->isConsistent();
        });
    }

    /**
     * Filter entities who's consistent
     *
     * @return self
     */
    public function getInconsistentEntities()
    {
        return array_filter($this->entities, function (Entity $entity) {
            return ! $entity->isConsistent();
        });
    }    

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->entities);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->entities[$offset];
    }

    /**
     * @param mixed $offset
     * @param $entity
     * @throws \Alchemistery\UnexpectedEntityException
     */
    public function offsetSet($offset, $entity)
    {
        if (! $this->hasExceptedType($entity)) {
            throw new UnexpectedEntityException();
        }

        if (is_null($offset)) {
            $this->entities[] = $entity;
        } else {
            $this->entities[$offset] = $entity;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->entities[$offset]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->entities);
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->entities);
    }
}
