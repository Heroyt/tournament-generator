<?php


namespace Containers;


use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Containers\BaseContainer;
use TournamentGenerator\Helpers\Sorter\BaseSorter;
use TournamentGenerator\Interfaces\WithId;
use TournamentGenerator\Team;

class BaseContainerTest extends TestCase
{

	public function testContainerParents() : void {
		$container = new BaseContainer(0);
		self::assertNull($container->getParent());

		$containerChild1 = new BaseContainer(1, $container);
		self::assertSame($container, $containerChild1->getParent());

		$container->addChild($containerChild1);
		self::assertSame($container, $containerChild1->getParent());

		$containerChild2 = new BaseContainer(2);
		self::assertNull($containerChild2->getParent());

		$containerChild2->setParent($container);
		self::assertSame($container, $containerChild2->getParent());

		$container->addChild($containerChild2);
		self::assertSame($container, $containerChild2->getParent());

		$containerChild3 = new BaseContainer(3);
		self::assertNull($containerChild3->getParent());

		$container->addChild($containerChild3);
		self::assertSame($container, $containerChild3->getParent());
	}

	public function testContainerParentInvalid() : void {
		$container1 = new BaseContainer(0);
		$container2 = new BaseContainer(1);
		self::assertNull($container1->getParent());

		$containerChild1 = new BaseContainer(2, $container1);
		self::assertSame($container1, $containerChild1->getParent());

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Parent container can only be set once!');
		$containerChild1->setParent($container2);
	}

	public function testContainerBasic() : void {
		$container = new BaseContainer(1);
		$container2 = new BaseContainer(2);
		$container3 = new BaseContainer(3);

		// Check initialization
		self::assertSame(1, $container->id);
		self::assertCount(0, $container);
		self::assertSame([], $container->get());

		// Check adding child containers
		$container->addChild($container2, $container3);
		self::assertSame([2, 3], $container->getLeafIds());

		// Try to add a duplicate child
		$container->addChild($container2);
		self::assertSame([2, 3], $container->getLeafIds());

		// Add values to child containers
		$container2->insert('aaa', 'bbb', 'ccc');
		$container3->insert('aaa', 'ddd', 'eee');

		self::assertCount(6, $container);
		$expected = ['aaa', 'bbb', 'ccc', 'ddd', 'eee'];
		foreach ($container as $value) {
			self::assertContains($value, $expected);
		}

		$container->insert('ggg', 'hhh');
		self::assertCount(8, $container);
		$expected = ['aaa', 'bbb', 'ccc', 'ddd', 'eee', 'ggg', 'hhh'];
		foreach ($container as $value) {
			self::assertContains($value, $expected);
		}
		foreach ($container->getTopLevel() as $value) {
			self::assertContains($value, ['ggg', 'hhh']);
		}
	}

	public function testContainerQuery() : void {
		$container = new BaseContainer(1);
		$container2 = new BaseContainer(2);
		$container3 = new BaseContainer(3);

		$container->addChild($container2, $container3);
		$container2->insert('aaa', 'bbb', 'ccc');
		$container3->insert('aaa', 'ddd', 'eee');

		// Check unique
		$result = $container->unique()->get();
		self::assertCount(5, $result);
		$expected = ['aaa', 'bbb', 'ccc', 'ddd', 'eee'];
		$i = 0;
		foreach ($container as $key => $value) {
			self::assertEquals($i++, $key);
			self::assertContains($value, $expected);
		}

		// Check sort
		$result = $container->sort(static function($a, $b) {
			return strcmp($a, $b);
		})->get();
		self::assertCount(6, $result);
		self::assertSame([
											 'aaa',
											 'aaa',
											 'bbb',
											 'ccc',
											 'ddd',
											 'eee',
										 ],
										 array_values($result) // Get rid of array keys
		);

		// Check sort descending
		$result = $container->sort(static function($a, $b) {
			return strcmp($a, $b);
		})->desc()->get();
		self::assertCount(6, $result);
		self::assertSame([
											 'eee',
											 'ddd',
											 'ccc',
											 'bbb',
											 'aaa',
											 'aaa',
										 ],
										 array_values($result) // Get rid of array keys
		);


		// Check filter
		$container2->insert('aaaaa', 'bbbbb');
		$container3->insert('ggggg');
		$result = $container->filter(static function($a) {
			return strlen($a) === 5;
		})->get();
		self::assertCount(3, $result);
		$expected = ['aaaaa', 'bbbbb', 'ggggg'];
		foreach ($result as $value) {
			self::assertContains($value, $expected);
		}

		// Check getting a query
		$result = $container
			->getQuery()
			->sort(static function($a, $b) {
				return strcmp($a, $b);
			})
			->get();
		self::assertCount(9, $result);
		self::assertSame([
											 'aaa',
											 'aaa',
											 'aaaaa',
											 'bbb',
											 'bbbbb',
											 'ccc',
											 'ddd',
											 'eee',
											 'ggggg',
										 ],
										 array_values($result) // Get rid of array keys
		);

		// Check getting a top-level query
		$container->insert('c', 'a', 'b');
		$result = $container
			->getTopLevelQuery()
			->sort(static function($a, $b) {
				return strcmp($a, $b);
			})
			->get();
		self::assertCount(3, $result);
		self::assertSame([
											 'a',
											 'b',
											 'c',
										 ],
										 array_values($result) // Get rid of array keys
		);
	}

	public function testContainerQueryComplex() : void {
		$container = new BaseContainer(1);
		$container2 = new BaseContainer(2);
		$container3 = new BaseContainer(3);

		$container->addChild($container2, $container3);
		$container2->insert(
			['a' => 0, 'b' => 99],
			['a' => 2, 'b' => 5],
			['a' => 9, 'b' => -1],
		);
		$container3->insert(
			(object) ['a' => 2, 'b' => 2],
			(object) ['a' => 8, 'b' => 1],
			(object) ['a' => 5, 'b' => 4],
		);

		// Check sortBy
		$result = $container->sortBy('a')->get();
		self::assertCount(6, $result);
		self::assertEquals([
												 ['a' => 0, 'b' => 99],
												 ['a' => 2, 'b' => 5],
												 (object) ['a' => 2, 'b' => 2],
												 (object) ['a' => 5, 'b' => 4],
												 (object) ['a' => 8, 'b' => 1],
												 ['a' => 9, 'b' => -1],
											 ],
											 array_values($result) // Get rid of array keys
		);
		$result = $container->sortBy('b')->get();
		self::assertCount(6, $result);
		self::assertEquals([
												 ['a' => 9, 'b' => -1],
												 (object) ['a' => 8, 'b' => 1],
												 (object) ['a' => 2, 'b' => 2],
												 (object) ['a' => 5, 'b' => 4],
												 ['a' => 2, 'b' => 5],
												 ['a' => 0, 'b' => 99],
											 ],
											 array_values($result) // Get rid of array keys
		);

		// Check sortBy descending
		$result = $container->sortBy('a')->desc()->get();
		self::assertCount(6, $result);
		self::assertEquals([
												 ['a' => 9, 'b' => -1],
												 (object) ['a' => 8, 'b' => 1],
												 (object) ['a' => 5, 'b' => 4],
												 (object) ['a' => 2, 'b' => 2],
												 ['a' => 2, 'b' => 5],
												 ['a' => 0, 'b' => 99],
											 ],
											 array_values($result) // Get rid of array keys
		);
		$result = $container->sortBy('b')->desc()->get();
		self::assertCount(6, $result);
		self::assertEquals([
												 ['a' => 0, 'b' => 99],
												 ['a' => 2, 'b' => 5],
												 (object) ['a' => 5, 'b' => 4],
												 (object) ['a' => 2, 'b' => 2],
												 (object) ['a' => 8, 'b' => 1],
												 ['a' => 9, 'b' => -1],
											 ],
											 array_values($result) // Get rid of array keys
		);
	}

	public function testContainerQueryPluckPrimitive() : void {
		$container = new BaseContainer(0);

		for ($i = 0; $i < 10; $i++) {
			$container->insert($i);
		}

		$values = $container->only('value1')->get();
		self::assertEquals(range(0, 9), $values);

		$values = $container->only('value2')->get();
		self::assertEquals(range(0, 9), $values);

		$values = $container->only('value3')->get();
		self::assertEquals(range(0, 9), $values);
	}

	public function testContainerQueryPluckArray() : void {
		$container = new BaseContainer(0);

		for ($i = 0; $i < 10; $i++) {
			$container->insert([
													 'value1' => $i,
													 'value2' => $i + 1,
													 'value3' => 10 - $i,
												 ]);
		}

		$values = $container->only('value1')->get();
		self::assertEquals(range(0, 9), $values);

		$values = $container->only('value2')->get();
		self::assertEquals(range(1, 10), $values);

		$values = $container->only('value3')->get();
		self::assertEquals(range(10, 1), $values);
	}

	public function testContainerQueryPluckObject() : void {
		$container = new BaseContainer(0);

		for ($i = 0; $i < 10; $i++) {
			$container->insert((object) [
				'value1' => $i,
				'value2' => $i + 1,
				'value3' => 10 - $i,
			]);
		}

		$values = $container->only('value1')->get();
		self::assertEquals(range(0, 9), $values);

		$values = $container->only('value2')->get();
		self::assertEquals(range(1, 10), $values);

		$values = $container->only('value3')->get();
		self::assertEquals(range(10, 1), $values);
	}

	public function testContainerQueryPluckClass() : void {
		$container = new BaseContainer(0);

		$names = [];
		for ($i = 0; $i < 10; $i++) {
			$names[] = 'Team '.$i;
			$container->insert(new Team('Team '.$i, $i));
		}

		$values = $container->only('getId')->get();
		self::assertEquals(range(0, 9), $values);

		$values = $container->only('getName')->get();
		self::assertEquals($names, $values);

		$values = $container->only('nonexistentValue')->get();
		self::assertEquals($container->get(), $values);
	}

	public function testContainerQueryPluckInvalid() : void {
		$container = new BaseContainer(0);

		for ($i = 0; $i < 10; $i++) {
			$container->insert(new Team('Team '.$i, $i));
		}

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('only() can be only called once.');
		$values = $container->only('getId')->only('getName')->get();
	}

	public function arrays() : array {
		return [
			[
				[1, 2, 3, 4, 5, 6, 7, 8],
			],
			[
				['a', 'b', 'c', 'd'],
			],
		];
	}

	/**
	 * @dataProvider arrays
	 *
	 * @param array $arr
	 */
	public function testContainerFromArray(array $arr) : void {
		$container = BaseContainer::fromArray($arr);
		$this::assertEquals($arr, $container->get());
	}

	public function testContainerInsertNested() : void {
		$helpers = [];
		for ($i = 0; $i < 10; $i++) {
			$helpers[] = new Helper($i);
		}

		$container = new BaseContainer('a');

		$container->insert(...$helpers);

		$expected = $helpers;
		foreach ($helpers as $helper) {
			$expected = array_merge($expected, $helper->getContainer()->get());
		}
		self::assertEquals($expected, $container->get());
	}

	public function testContainerQueryWhereId() : void {
		$container = new BaseContainer(0);

		$helpers = [];
		for ($i = 1; $i < 10; $i++) {
			$helpers[] = new Helper2($i);
		}
		$container->insert(...$helpers);

		foreach ($helpers as $helper) {
			self::assertEquals($helper, $container->whereId($helper->getId())->getFirst());
			self::assertCount(1, $container->whereId($helper->getId())->get());
			self::assertEquals($helper, $container->getQuery()->whereId($helper->getId())->getFirst());
			self::assertCount(1, $container->getQuery()->whereId($helper->getId())->get());
		}
	}

	public function testContainerIds() : void {
		$container = new BaseContainer(0);

		$helpers = [];
		for ($i = 1; $i < 10; $i++) {
			$helpers[] = new Helper2($i);
		}
		$container->insert(...$helpers);

		self::assertEquals(range(1, 9), $container->ids()->get());
		self::assertEquals(range(1, 9), $container->getQuery()->ids()->get());
	}

	public function testContainerUniqueObjects() : void {
		$container = new BaseContainer(0);

		$helpers = [];
		for ($i = 1; $i < 10; $i++) {
			$helpers[] = new Helper2($i);
		}
		$container->insert(...$helpers);
		$container->insert(...$helpers); // Insert twice

		self::assertCount(count($helpers) * 2, $container->get());
		self::assertEquals($helpers, $container->unique()->get());
	}

	public function testContainerSort() : void {
		$container = new BaseContainer(0);

		$values = range(1, 100);
		shuffle($values);

		$container->insert(...$values);

		self::assertEquals(range(1, 100), $container->addSorter(new SimpleSorter())->get());
		self::assertEquals(range(1, 100), $container->getQuery()->addSorter(new SimpleSorter())->get());
	}

	public function testContainerQueryGetContainer() : void {
		$container = new BaseContainer(0);

		$container->insert(...range(1, 100));

		$newContainer = $container->filter(static function($a) {
			return $a >= 50;
		})->getContainer();

		self::assertInstanceOf(BaseContainer::class, $newContainer);
		self::assertEquals(range(50, 100), $newContainer->get());
	}

	public function testGetFirstEmpty() : void {
		$container = new BaseContainer(0);

		self::assertNull($container->getQuery()->getFirst());
	}

	public function testGetEmpty() : void {
		$container = new BaseContainer(0);

		self::assertEmpty($container->get());
	}

	public function testSortDefault() : void {
		$container = new BaseContainer(0);

		$values = range(1, 100);
		shuffle($values);
		$container->insert(1, ...$values);

		$this::assertEquals(array_merge([1], range(1, 100)), array_values($container->sort()->get()));
	}

	public function testIteration() : void {
		$container = new BaseContainer(0);

		$values = range(1, 100);
		$container->insert(...$values);

		$i = 1;
		foreach ($container as $value) {
			$this::assertEquals($i++, $value);
		}
	}

	public function testInsertFlat() : void {
		$helpers = [];
		for ($i = 0; $i < 10; $i++) {
			$helpers[] = new Helper($i);
		}

		$container = new BaseContainer('a');

		$container->insertFlat(...$helpers);

		self::assertEquals($helpers, $container->get());
	}

	public function items() : array {
		return [
			[
				['a' => 2],
				['a' => 1],
			],
			[
				(object) ['a' => 2],
				(object) ['a' => 1],
			],
		];
	}

	/**
	 * @dataProvider items
	 *
	 * @param $obj1
	 * @param $obj2
	 */
	public function testSortByNonexistentProperty($obj1, $obj2) : void {
		$container = new BaseContainer(0);

		$container->insert($obj1, $obj2);

		self::assertEquals([$obj1, $obj2], array_values($container->sortBy('b')->get()));
		self::assertEquals([$obj2, $obj1], array_values($container->sortBy('a')->get()));
	}

}

class Helper implements WithId
{

	public                  $id;
	protected BaseContainer $container;

	public function __construct($id) {
		$this->id = $id;
		$this->container = new BaseContainer($id);
		for ($i = 0; $i < random_int(1, 10); $i++) {
			$this->container->insert(random_int(0, 100));
		}
	}

	/**
	 * @return BaseContainer
	 */
	public function getContainer() : BaseContainer {
		return $this->container;
	}

	/**
	 * Gets the unique identifier of the object
	 *
	 * @return string|int  Unique identifier of the object
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Sets the unique identifier of the object
	 *
	 * @param string|int $id Unique identifier of the object
	 *
	 * @return WithId
	 * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'int'
	 *
	 */
	public function setId($id) : WithId {
		$this->id = $id;
		return $this;
	}
}

class Helper2 implements WithId
{

	public $id;

	public function __construct($id) {
		$this->id = $id;
	}

	/**
	 * Gets the unique identifier of the object
	 *
	 * @return string|int  Unique identifier of the object
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Sets the unique identifier of the object
	 *
	 * @param string|int $id Unique identifier of the object
	 *
	 * @return WithId
	 * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'int'
	 *
	 */
	public function setId($id) : WithId {
		$this->id = $id;
		return $this;
	}
}

class SimpleSorter implements BaseSorter
{

	/**
	 * Sort function to call
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function sort(array $data) : array {
		sort($data);
		return $data;
	}
}