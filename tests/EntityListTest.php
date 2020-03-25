<?php

namespace Tests;

use Alchemistery\Entity;
use Alchemistery\EntityList;
use Alchemistery\UnexpectedEntityException;
use PHPUnit\Framework\TestCase;

class EntityListTest extends TestCase
{
    /** @test */
    public function can_make_entity_list()
    {
        $entityJohn = $this->makeEntity([
            'name' => 'John',
        ]);

        $entityBob = $this->makeEntity([
            'name' => 'Bob',
        ]);

        $entityList = $this->makeGoodEntityList([$entityJohn]);

        $entityList[] = $entityBob;

        $this->assertInstanceOf(EntityList::class, $entityList);
        $this->assertCount(2, $entityList);
        $this->assertContains($entityBob, $entityList);
        $this->assertContains($entityJohn, $entityList);
    }

    /** @test */
    public function can_make_an_expected_entity_list()
    {
        $attributes = [
            'name' => 'Walter Hartwell White',
        ];

        $entity = $this->makeEntity($attributes);

        $this->expectException(UnexpectedEntityException::class);

        $this->makeBadEntityList([$entity]);
    }

    /** @test */
    public function can_have_consistent_entities_only()
    {
        $consistentEntity = $this->makeEntity([
            'name' => 'John',
            'age' => 25,
        ]);

        $unconsistentEntity = $this->makeEntity([
            'name' => 'Bob',
        ]);

        $entityList = $this->makeGoodEntityList([$consistentEntity, $unconsistentEntity]);

        $this->assertCount(1, $entityList->getConsistentEntities());
        $this->assertContains($consistentEntity, $entityList->getConsistentEntities());
    }

    /**
     * @param array $entities
     * @return \Alchemistery\EntityList
     */
    protected function makeGoodEntityList(array $entities)
    {
        return new class($entities) extends EntityList {
            protected function hasExceptedType(Entity $entity): bool
            {
                return true;
            }
        };
    }

    /**
     * @param array $entities
     * @return \Alchemistery\EntityList
     */
    protected function makeBadEntityList(array $entities)
    {
        return new class($entities) extends EntityList {
            protected function hasExceptedType(Entity $entity): bool
            {
                return false;
            }
        };
    }

    /**
     * @param array $attributes
     * @return \Alchemistery\Entity
     */
    protected function makeEntity(array $attributes)
    {
        return new class($attributes) extends Entity {
            public $name;
            public $age;

            public function isConsistent(): bool
            {
                return ! is_null($this->name) && ! is_null($this->age);
            }
        };
    }
}
