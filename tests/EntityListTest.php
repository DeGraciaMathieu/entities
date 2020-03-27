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

    /** @test */
    public function can_get_inconsistent_entities_only()
    {
        $consistentEntity = $this->makeEntity([
            'name' => 'John',
            'age' => 25,
        ]);

        $unconsistentEntity = $this->makeEntity([
            'name' => 'Bob',
        ]);

        $entityList = $this->makeGoodEntityList([$consistentEntity, $unconsistentEntity]);

        $this->assertCount(1, $entityList->getInconsistentEntities());
        $this->assertContains($unconsistentEntity, $entityList->getInconsistentEntities());
    }    

    public function can_add_entity()
    {
        $firstEntity = $this->makeEntity([
            'name' => 'First',
            'age' => 25,
        ]);

        $secondEntity = $this->makeEntity([
            'name' => 'Second',
            'age' => 25,
        ]);

        $thirdEntity = $this->makeEntity([
            'name' => 'Third',
            'age' => 25,
        ]);

        $entityList = $this->makeGoodEntityList([$firstEntity]);

        $entityList[] = $secondEntity;
        $entityList[5] = $thirdEntity;

        $this->assertCount(3, $entityList);
        $this->assertContains($firstEntity, $entityList);
        $this->assertContains($secondEntity, $entityList);
        $this->assertContains($thirdEntity, $entityList);
    }

    /** @test */
    public function cant_add_unexpected_entity()
    {
        $entity = $this->makeEntity([
            'name' => 'First',
            'age' => 25,
        ]);

        $entityList = $this->makeBadEntityList([]);

        $this->expectException(UnexpectedEntityException::class);

        $entityList[] = $entity;
    }

    /** @test */
    public function can_remove_entity()
    {
        $entity = $this->makeEntity([
            'name' => 'First',
            'age' => 25,
        ]);

        $entityList = $this->makeGoodEntityList([$entity]);

        unset($entityList[0]);

        $this->assertCount(0, $entityList);
    }

    /** @test */
    public function can_check_entity()
    {
        $entity = $this->makeEntity([
            'name' => 'First',
            'age' => 25,
        ]);

        $entityList = $this->makeGoodEntityList([]);

        $this->assertFalse(isset($entityList[0]));

        $entityList[] = $entity;

        $this->assertTrue(isset($entityList[0]));
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
