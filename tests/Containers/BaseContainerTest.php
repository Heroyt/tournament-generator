<?php


namespace Containers;


use Exception;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Containers\BaseContainer;

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

}