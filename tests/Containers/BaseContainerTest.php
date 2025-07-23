<?php

declare(strict_types=1);

namespace Containers;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use TournamentGenerator\Containers\BaseContainer;
use TournamentGenerator\Helpers\Sorter\BaseSorter;
use TournamentGenerator\Interfaces\WithId;
use TournamentGenerator\Team;

/**
 * @internal
 *
 * @coversNothing
 */
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
        $result = $container->sort(static fn ($a, $b) : int => strcmp((string) $a, (string) $b))->get();
        self::assertCount(6, $result);
        self::assertSame(
            [
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
        $result = $container->sort(static fn ($a, $b) : int => strcmp((string) $a, (string) $b))->desc()->get();
        self::assertCount(6, $result);
        self::assertSame(
            [
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
        $result = $container->filter(static fn ($a) : bool => 5 === strlen((string) $a))->get();
        self::assertCount(3, $result);
        $expected = ['aaaaa', 'bbbbb', 'ggggg'];
        foreach ($result as $value) {
            self::assertContains($value, $expected);
        }

        // Check getting a query
        $result = $container
            ->getQuery()
            ->sort(static fn ($a, $b) : int => strcmp((string) $a, (string) $b))
            ->get()
        ;
        self::assertCount(9, $result);
        self::assertSame(
            [
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
            ->sort(static fn ($a, $b) : int => strcmp((string) $a, (string) $b))
            ->get()
        ;
        self::assertCount(3, $result);
        self::assertSame(
            [
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
        self::assertEquals(
            [
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
        self::assertEquals(
            [
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
        self::assertEquals(
            [
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
        self::assertEquals(
            [
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
        $baseContainer = new BaseContainer(0);

        for ($i = 0; $i < 10; ++$i) {
            $baseContainer->insert($i);
        }

        $values = $baseContainer->only('value1')->get();
        self::assertEquals(range(0, 9), $values);

        $values = $baseContainer->only('value2')->get();
        self::assertEquals(range(0, 9), $values);

        $values = $baseContainer->only('value3')->get();
        self::assertEquals(range(0, 9), $values);
    }

    public function testContainerQueryPluckArray() : void {
        $baseContainer = new BaseContainer(0);

        for ($i = 0; $i < 10; ++$i) {
            $baseContainer->insert(
                [
                    'value1' => $i,
                    'value2' => $i + 1,
                    'value3' => 10 - $i,
                ]
            );
        }

        $values = $baseContainer->only('value1')->get();
        self::assertEquals(range(0, 9), $values);

        $values = $baseContainer->only('value2')->get();
        self::assertEquals(range(1, 10), $values);

        $values = $baseContainer->only('value3')->get();
        self::assertEquals(range(10, 1), $values);
    }

    public function testContainerQueryPluckObject() : void {
        $baseContainer = new BaseContainer(0);

        for ($i = 0; $i < 10; ++$i) {
            $baseContainer->insert(
                (object) [
                    'value1' => $i,
                    'value2' => $i + 1,
                    'value3' => 10 - $i,
                ]
            );
        }

        $values = $baseContainer->only('value1')->get();
        self::assertEquals(range(0, 9), $values);

        $values = $baseContainer->only('value2')->get();
        self::assertEquals(range(1, 10), $values);

        $values = $baseContainer->only('value3')->get();
        self::assertEquals(range(10, 1), $values);
    }

    public function testContainerQueryPluckClass() : void {
        $baseContainer = new BaseContainer(0);

        $names = [];
        for ($i = 0; $i < 10; ++$i) {
            $names[] = 'Team ' . $i;
            $baseContainer->insert(new Team('Team ' . $i, $i));
        }

        $values = $baseContainer->only('getId')->get();
        self::assertEquals(range(0, 9), $values);

        $values = $baseContainer->only('getName')->get();
        self::assertEquals($names, $values);

        $values = $baseContainer->only('nonexistentValue')->get();
        self::assertEquals($baseContainer->get(), $values);
    }

    public function testContainerQueryPluckInvalid() : void {
        $baseContainer = new BaseContainer(0);

        for ($i = 0; $i < 10; ++$i) {
            $baseContainer->insert(new Team('Team ' . $i, $i));
        }

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('only() can be only called once.');
        $baseContainer->only('getId')->only('getName')->get();
    }

    #[DataProvider('arrays')]
    public function testContainerFromArray(array $arr) : void {
        $baseContainer = BaseContainer::fromArray($arr);
        $this::assertEquals($arr, $baseContainer->get());
    }

    public static function arrays() : iterable {
        yield 'one-to-eight' => [
            [1, 2, 3, 4, 5, 6, 7, 8],
        ];

        yield 'a-to-d' => [
            ['a', 'b', 'c', 'd'],
        ];
    }

    public function testContainerInsertNested() : void {
        $helpers = [];
        for ($i = 0; $i < 10; ++$i) {
            $helpers[] = new Helper($i);
        }

        $baseContainer = new BaseContainer('a');

        $baseContainer->insert(...$helpers);

        $expected = $helpers;
        foreach ($helpers as $helper) {
            $expected = array_merge($expected, $helper->getContainer()->get());
        }
        self::assertEquals($expected, $baseContainer->get());
    }

    public function testContainerQueryWhereId() : void {
        $baseContainer = new BaseContainer(0);

        $helpers = [];
        for ($i = 1; $i < 10; ++$i) {
            $helpers[] = new Helper2($i);
        }
        $baseContainer->insert(...$helpers);

        foreach ($helpers as $helper) {
            self::assertEquals($helper, $baseContainer->whereId($helper->getId())->getFirst());
            self::assertCount(1, $baseContainer->whereId($helper->getId())->get());
            self::assertEquals($helper, $baseContainer->getQuery()->whereId($helper->getId())->getFirst());
            self::assertCount(1, $baseContainer->getQuery()->whereId($helper->getId())->get());
        }
    }

    public function testContainerIds() : void {
        $baseContainer = new BaseContainer(0);

        $helpers = [];
        for ($i = 1; $i < 10; ++$i) {
            $helpers[] = new Helper2($i);
        }
        $baseContainer->insert(...$helpers);

        self::assertEquals(range(1, 9), $baseContainer->ids()->get());
        self::assertEquals(range(1, 9), $baseContainer->getQuery()->ids()->get());
    }

    public function testContainerUniqueObjects() : void {
        $baseContainer = new BaseContainer(0);

        $helpers = [];
        for ($i = 1; $i < 10; ++$i) {
            $helpers[] = new Helper2($i);
        }
        $baseContainer->insert(...$helpers);
        $baseContainer->insert(...$helpers); // Insert twice

        self::assertCount(count($helpers) * 2, $baseContainer->get());
        self::assertEquals($helpers, $baseContainer->unique()->get());
    }

    public function testContainerSort() : void {
        $baseContainer = new BaseContainer(0);

        $values = range(1, 100);
        shuffle($values);

        $baseContainer->insert(...$values);

        self::assertEquals(range(1, 100), $baseContainer->addSorter(new SimpleSorter())->get());
        self::assertEquals(range(1, 100), $baseContainer->getQuery()->addSorter(new SimpleSorter())->get());
    }

    public function testContainerQueryGetContainer() : void {
        $baseContainer = new BaseContainer(0);

        $baseContainer->insert(...range(1, 100));

        $newContainer = $baseContainer->filter(static fn ($a) : bool => $a >= 50)->getContainer();

        self::assertInstanceOf(BaseContainer::class, $newContainer);
        self::assertEquals(range(50, 100), $newContainer->get());
    }

    public function testGetFirstEmpty() : void {
        $baseContainer = new BaseContainer(0);

        self::assertNull($baseContainer->getQuery()->getFirst());
    }

    public function testGetEmpty() : void {
        $baseContainer = new BaseContainer(0);

        self::assertEmpty($baseContainer->get());
    }

    public function testSortDefault() : void {
        $baseContainer = new BaseContainer(0);

        $values = range(1, 100);
        shuffle($values);
        $baseContainer->insert(1, ...$values);

        $this::assertEquals(array_merge([1], range(1, 100)), array_values($baseContainer->sort()->get()));
    }

    public function testIteration() : void {
        $baseContainer = new BaseContainer(0);

        $values = range(1, 100);
        $baseContainer->insert(...$values);

        $i = 1;
        foreach ($baseContainer as $value) {
            $this::assertEquals($i++, $value);
        }
    }

    public function testInsertFlat() : void {
        $helpers = [];
        for ($i = 0; $i < 10; ++$i) {
            $helpers[] = new Helper($i);
        }

        $baseContainer = new BaseContainer('a');

        $baseContainer->insertFlat(...$helpers);

        self::assertEquals($helpers, $baseContainer->get());
    }

    #[DataProvider('items')]
    public function testSortByNonexistentProperty(array|stdClass $obj1, array|stdClass $obj2) : void {
        $baseContainer = new BaseContainer(0);

        $baseContainer->insert($obj1, $obj2);

        self::assertEquals([$obj1, $obj2], array_values($baseContainer->sortBy('b')->get()));
        self::assertEquals([$obj2, $obj1], array_values($baseContainer->sortBy('a')->get()));
    }

    public static function items() : array {
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
}

class Helper implements WithId
{
    protected BaseContainer $container;

    public function __construct(public $id) {
        $this->container = new BaseContainer($this->id);
        for ($i = 0; $i < random_int(1, 10); ++$i) {
            $this->container->insert(random_int(0, 100));
        }
    }

    public function getContainer() : BaseContainer {
        return $this->container;
    }

    /**
     * Gets the unique identifier of the object.
     *
     * @return int|string Unique identifier of the object
     */
    public function getId() : int|string {
        return $this->id;
    }

    /**
     * Sets the unique identifier of the object.
     *
     * @param int|string $id Unique identifier of the object
     *
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'int'
     */
    public function setId(int|string $id) : WithId {
        $this->id = $id;

        return $this;
    }
}

class Helper2 implements WithId
{
    public function __construct(public $id) {}

    /**
     * Gets the unique identifier of the object.
     *
     * @return int|string Unique identifier of the object
     */
    public function getId() : int|string {
        return $this->id;
    }

    /**
     * Sets the unique identifier of the object.
     *
     * @param int|string $id Unique identifier of the object
     *
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'int'
     */
    public function setId(int|string $id) : WithId {
        $this->id = $id;

        return $this;
    }
}

class SimpleSorter implements BaseSorter
{
    /**
     * Sort function to call.
     */
    public function sort(array $data) : array {
        sort($data);

        return $data;
    }
}
