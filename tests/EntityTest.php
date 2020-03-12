<?php

namespace Tests;

use Alchemistery\Entity;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    /** @test */
    public function can_make_an_entity()
    {
        $attributes = [
            'name' => 'Walter Hartwell White',
            'age' => 50,
            'job' => 'Alchemist',
            'nickname' => 'Heisenberg',
        ];

        $entity = $this->makeEntity($attributes);

        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals($attributes['name'], $entity->name);
        $this->assertEquals($attributes['age'], $entity->age);
        $this->assertEquals($attributes['job'], $entity->job);
        $this->assertNull($entity->country);
        $this->assertObjectNotHasAttribute('nickname', $entity);
        $this->assertTrue($entity->isConsistent());
    }

    /** @test */
    public function can_have_an_inconsistent_entity()
    {
        $attributes = [
            'name' => 'Casper',
        ];

        $entity = $this->makeEntity($attributes);

        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertFalse($entity->isConsistent());
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
            public $job;
            public $country;

            public function isConsistent(): bool
            {
                return ! is_null($this->name) && ! is_null($this->age);
            }
        };
    }
}
