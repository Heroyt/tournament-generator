<?php


namespace Containers;


use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Category;
use TournamentGenerator\Containers\HierarchyContainer;
use TournamentGenerator\Group;
use TournamentGenerator\Round;

class HierarchyContainerTest extends TestCase
{

	public function testHierarchyBasic() : void {
		$container = new HierarchyContainer(0);

		self::assertNull($container->getLevelType());

		$round1 = new Round('Round 1', 1);
		$round2 = new Round('Round 2', 2);
		$round3 = new Round('Round 3', 3);
		$round4 = new Round('Round 4', 4);

		$container->insert($round1, $round2, $round3, $round4);

		self::assertEquals(Round::class, $container->getLevelType());
		self::assertCount(4, $container);
		self::assertCount(4, $container->getTopLevel());

		$groups = [
			$round1->group('Group 1', 1),
			$round1->group('Group 2', 2),
			$round1->group('Group 3', 3),
			$round1->group('Group 4', 4),
			$round2->group('Group 5', 5),
			$round2->group('Group 6', 6),
			$round2->group('Group 7', 7),
			$round2->group('Group 8', 8),
			$round3->group('Group 8', 8),
			$round3->group('Group 9', 9),
			$round3->group('Group 10', 10),
			$round3->group('Group 11', 11),
			$round4->group('Group 12', 12),
			$round4->group('Group 13', 13),
			$round4->group('Group 14', 14),
			$round4->group('Group 15', 15),
		];

		self::assertEquals(Group::class, $round1->getContainer()->getLevelType());
		self::assertEquals(Group::class, $round2->getContainer()->getLevelType());
		self::assertEquals(Group::class, $round3->getContainer()->getLevelType());
		self::assertEquals(Group::class, $round4->getContainer()->getLevelType());

		foreach ($groups as $group) {
			self::assertNull($group->getContainer()->getLevelType());
		}

		self::assertCount(20, $container);
		self::assertCount(16, $container->getLeafIds());
		self::assertCount(4, $container->getTopLevel());

		$result = $container->getHierarchyLevel(Round::class);
		self::assertCount(4, $result);
		self::assertEquals([
												 $round1,
												 $round2,
												 $round3,
												 $round4
											 ], $result);

		$result = $container->getHierarchyLevel(Group::class);
		self::assertCount(16, $result);
		self::assertEquals($groups, $result);

		$result = $container->getHierarchyLevel(Category::class);
		self::assertCount(0, $result);
		self::assertEquals([], $result);

		// Test id filter
		$result = $container->whereId(2)->get();
		self::assertCount(2, $result);
		self::assertEquals([
			$round2,
			$groups[1],
											 ], $result);
	}

	public function testInvalidLevelClass() : void {
		$container = new HierarchyContainer(0);
		$this->expectException(InvalidArgumentException::class);
		$container->getHierarchyLevel('NonexistentClass');
	}

	public function testInvalidInsert() : void {
		$container = new HierarchyContainer(0);

		$round = new Round('Round');
		$category = new Category('Category');

		$container->insert($round);

		self::assertEquals(Round::class, $container->getLevelType());
		$this->expectException(InvalidArgumentException::class);
		$container->insert($category);
	}

}