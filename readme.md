# alchemistery/entities

This package provide a way to implements entities. Useful for your services or repositories

## Usage

Create your entity in dedicated class :

```php
use Alchemistery\Entity;

class Human extends Entity
{
    public $name;
    public $age;

    public function isConsistent(): bool
    {
        return ! is_null($this->name) && ! is_null($this->age);
    }
}
```

Then instanciate a new entity like that :

```php
$human = new Human([
    'name' => 'Bob',
    'age' => 42,
]);

$human->name // Bob
$human->age // 42
$human->isConsistent(); // true
```

Create your entity list like this :

```php
use Alchemistery\EntityList;

class People extends EntityList
{
    public function hasExpectedType(Entity $entity): bool
    {
        return $entity instanceof Human::class;
    }

    public function getYoungest(): Human
    {
        $consistentPeople = array_filter($this->entities, function ($human) {
            return $human->isConsistent();
        });
        
        uasort($consistentPeople, function ($a, $b) {
            if ($a->age === $b->age) {
                return 0;
            }
    
            return ($a > $b) ? -1 : 1;
        });

        return array_pop($consistentPeople);
    }
}
```

Then instanciate a list like that :

```php
$bob = new Human(['name' => 'Bob', 'age' => 12]);
$john = new Human(['name' => 'John', 'age' => 10]);

$people = new People([$bob, $john]);

$people[0]->name // Bob
$people[1]->name // John
$people->getYoungest()->name // John
```
