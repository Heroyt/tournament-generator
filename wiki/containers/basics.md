## Containers

Containers are object wrappers that allow for fluent operations over contained data.

Containers are a hierarchical tree-like structure. Each container can have multiple child containers and one parent
container. This allows for searching all containers in a hierarchy.

### Creating containers

All containers inherit from a **BaseContainer** class. **BaseContainer** can hold any php object. One container *should*
hold only one type of data.

```php
use TournamentGenerator\Containers\BaseContainer;

$container = new BaseContainer($id);

$container->insert($object1, $object2);
```

This initiates a container, containing 2 objects: `$object1` and `$object2`.

```php
use TournamentGenerator\Containers\BaseContainer;

$container2 = new BaseContainer($id2);

$container2->insert($object3, $object4, $object5);

$container->addChild($container2);
```

This creates another container with the ID of `$id2` with 3 objects: `$object3`, `$object4` and `$object5`.
The `addChild()` method appends the `$container2` to `$container1` as a child creating a tree-like structure:

```json
{
	// $container
	"id": $id,
	"values": [
		$object1,
		$object2
	],
	"children": [
		{
			// $container2
			"id": $id2,
			"values": [
				$object3,
				$object4,
				$object5
			],
			children: []
		}
	]
}
```

#### Creating from an array

Container allows for creation right from an array.

```php
use TournamentGenerator\Containers\BaseContainer;

$data = [1, 2, 3, 4];
$container = BaseContainer::fromArray($data);
```

### Inserting values

The `insert()` method is used to insert any value into the container.

```php
use TournamentGenerator\Containers\BaseContainer;

$container = new BaseContainer($id);
$container->insert(1, 2, 3);
```

It also works with a class instances.

```php
use TournamentGenerator\Containers\BaseContainer;
use TournamentGenerator\Team;

$container = new BaseContainer($id);
$container->insert(new Team('Team 1'), new Team('Team 2'));
```

If the class has a container-getter method `getContainer()`, the `insert()` method will also add its container as a child to itself.

```php
use TournamentGenerator\Containers\BaseContainer;

class MyClass {

  public BaseContainer $container;
  public string $name;
  
  public function __construct(string $name) {
    $this->name = $name;
    $this->container = new BaseContainer($name);
  }
  
  public function getContainer() : BaseContainer {
    return $this->container;
  }
}

$container = new BaseContainer(1);

$container->insert(new MyClass('First'), new MyClass('Second'));

#
# container:
#   - id: 1
#   - values: [
#               MyClass('First'), 
#               MyClass('Second'),
#             ]
#   - children: [
#                 MyClass('First')->container,
#                 MyClass('Second')->container,
#               ]
#
```

If you want to insert the objects without adding the child containers, you can use the `insertFlat()` method.

```php
use TournamentGenerator\Containers\BaseContainer;

class MyClass {

  public BaseContainer $container;
  public string $name;
  
  public function __construct(string $name) {
    $this->name = $name;
    $this->container = new BaseContainer($name);
  }
  
  public function getContainer() : BaseContainer {
    return $this->container;
  }
}

$container = new BaseContainer(1);

$container->insertFlat(new MyClass('First'), new MyClass('Second'));

#
# container:
#   - id: 1
#   - values: [
#               MyClass('First'), 
#               MyClass('Second'),
#             ]
#   - children: []
#
```
