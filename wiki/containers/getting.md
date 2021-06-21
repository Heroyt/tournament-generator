## Getting values

To get the values from the container, the container object will use a **ContainerQuery**. This allows for modifying and
filtering the contained values.

To get all values, call the `get()` method:

```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

$values = $container->get(); // [$object1, $object2, $object3, $object4, $object5]

```

This gets all values from the container and **all** child containers.

You can also get the **ContainerQuery** to add modifiers to it.

```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

$query = $container->getQuery();

// Modifiers to the query
//...

$query->get();  // [$object1, $object2, $object3, $object4, $object5]
```

### Special getters

The container gets values from all its child containers by default. If you don't want to get all the values, you can use one of the following methods:

#### getFirst()

This gets only the first value from the container.

```php
$container = new \TournamentGenerator\Containers\BaseContainer(1);
$container->insert(1, 2, 3);
$container->getQuery()->getFirst(); // 1
```

#### getTopLevel()
This gets only the values of the given container and not its children.
  
```php
  /** @var TournamentGenerator\Containers\BaseContainer $container */
  
  # container:
  #  - value1
  #  - value2
  #  - value3
  #
  #  - container2:
  #    - value4
  #    - value5
  #
  #    - container3:
  #      - value6
  #    - container4:
  #      - value7
  #
  #  - container5:
  #    - value8
  #    - value9
  #
  #    - container6:
  #      - value10
  #    - container7:
  #      - value11
  
  $container->get();         // [value1, value2, value3, value4, value5, value6, value7, value8, value9, value10, value11]
  $container->getTopLevel(); // [value1, value2, value3]
```
#### getTopLevelQuery()
This gets the query of the given container only for the top-level values and not its children.
```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

# container:
#  - value1
#  - value2
#  - value3
#
#  - container2:
#    - value4
#    - value5
#
#    - container3:
#      - value6
#    - container4:
#      - value7
#
#  - container5:
#    - value8
#    - value9
#
#    - container6:
#      - value10
#    - container7:
#      - value11

$container
->getQuery()
/* Modifiers */
->get();         // [value1, value2, value3, value4, value5, value6, value7, value8, value9, value10, value11]
$container
->getTopLevelQuery()
/* Modifiers */
->get(); // [value1, value2, value3]
```
#### getLeafIds()
Useful for getting the "leaf" container ids. Leaf container is a child container that has no children.
```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

# container:
#  - id: 1
#
#  - container2:
#    - id: 2
#
#    - container3:
#      - id: 3
#    - container4:
#      - id: 4
#
#  - container5:
#    - id: 5
#
#    - container6:
#      - id: 6
#    - container7:
#      - id: 7

$container->getLeafIds();   // [3, 4, 6, 7]
```
This is useful for getting the leaf hierarchy object ids, because its container should have the same id.

### Iteration

The container implements a [Iterator](https://www.php.net/manual/en/class.iterator) and [Countable](https://www.php.net/manual/en/class.countable) interfaces. This means that you can use the container in a [foreach()](https://www.php.net/manual/en/control-structures.foreach.php) loop and call a [count()](https://www.php.net/manual/en/function.count.php) function on it. It iterates through all its values (including children).

```php
use TournamentGenerator\Containers\BaseContainer;

$container = new BaseContainer(1);
$container->insert(1, 2, 3);

$container2 = new BaseContainer(2);
$container2->insert(5, 6, 7);

$container->addChild($container2);

count($container); // 6

foreach ($container as $value) {
  echo $value.PHP_EOL;
}
// 1
// 2
// 3
// 4
// 5
// 6
//
```

### Available modifiers

- All modifiers can be called on the **Container** object itself, or the **ContainerQuery**.
- All modifiers can be combined in any way

#### Filter

Allows you to filter the values using a callback similar to php's [array_filter()](https://www.php.net/manual/en/function.array-filter.php) function. One query can have multiple filters active at the same time.

```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

$container
  ->getQuery()
  ->filter(static function(object $obj) {
    return $obj->value > 0;
  })
  ->get();

// Or

$container
  ->filter(static function(object $obj) {
    return $obj->value > 0;
  })
  ->get()
```

#### WhereId

Works with objects that implement the `HasId` interface. Filter objects that match a given id.

```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

$container
  ->getQuery()
  ->whereId(1)
  ->get();

// Or

$container
  ->whereId(1)
  ->get();
```

#### Sort

Sort objects using a callback function similar to php's [usort()](https://www.php.net/manual/en/function.usort.php) function.

```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

$container
  ->getQuery()
  ->sort(static function(int $a, int $b) {
    return $a - $b;
  })
  ->get();

// Or

$container
  ->sort(static function(int $a, int $b) {
    return $a - $b;
  })
  ->get();
```

#### SortBy

Sort objects by some public property or array key.

```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

$data = [
  [
    'id' => 1,
    'name' => 'first object',
  ],
  [
    'id' => 5,
    'name' => 'fifth object',
  ],
  [
    'id' => 3,
    'name' => 'third object',
  ],
  [
    'id' => 2,
    'name' => 'second object',
  ],
];

$container->insert(...$data); // Add all associative arrays to the container

$container
  ->getQuery()
  ->sortBy('id')
  ->get();

// Or

$container
  ->sortBy('name')
  ->get();
```

#### Desc

Reverses the sort order. Can be used with the `sort()` and `sortBy()` methods.

```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

$data = [
  [
    'id' => 1,
    'name' => 'first object',
  ],
  [
    'id' => 5,
    'name' => 'fifth object',
  ],
  [
    'id' => 3,
    'name' => 'third object',
  ],
  [
    'id' => 2,
    'name' => 'second object',
  ],
];

$container->insert(...$data); // Add all associative arrays to the container

$container
  ->sortBy('id')
  ->get(); // 1, 2, 3, 4, 5
  
$container
  ->sortBy('id')
  ->desc()
  ->get(); // 5, 4, 3, 2, 1
```

#### Unique

Filter values and remove duplicates. Works with primitive types and class instances.

```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

$container->insert(1, 2, 3, 4, 1, 1, 9, 2);

$container
  ->unique()
  ->get(); // [1, 2, 3, 4, 9]
```

#### Only

Pluck a specific property, or an array key from objects and return it in an array. 

```php
/** @var TournamentGenerator\Containers\BaseContainer $container */

$data = [
  [
    'id' => 1,
    'name' => 'first object',
  ],
  [
    'id' => 5,
    'name' => 'fifth object',
  ],
  [
    'id' => 3,
    'name' => 'third object',
  ],
  [
    'id' => 2,
    'name' => 'second object',
  ],
];

$container->insert(...$data); // Add all associative arrays to the container

$container
  ->only('id')
  ->get(); // [1, 5, 3, 2]
```