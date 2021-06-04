<?php


namespace Containers;


use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Category;
use TournamentGenerator\Containers\BaseContainer;
use TournamentGenerator\Containers\GameContainer;
use TournamentGenerator\Round;
use TournamentGenerator\Tournament;

class GameContainerTest extends TestCase
{

	public function testAutoincrement() : void {
		// Create structure
		$containers = [];
		$containers[0] = new GameContainer(0);
		$containers[1] = new GameContainer(1);
		$containers[2] = new GameContainer(2);
		$containers[3] = new GameContainer(3);
		$containers[4] = new GameContainer(4);
		$containers[5] = new GameContainer(5);
		$containers[6] = new GameContainer(6);

		$containers[0]->addChild($containers[1], $containers[2]);
		$containers[1]->addChild($containers[3], $containers[4]);
		$containers[2]->addChild($containers[5], $containers[6]);

		// Default value - 1
		foreach ($containers as $container) {
			self::assertEquals(1, $container->getAutoIncrement());
		}

		// Increment
		$containers[4]->incrementId();
		$containers[4]->incrementId();
		foreach ($containers as $container) {
			self::assertEquals(3, $container->getAutoIncrement());
			self::assertEquals(1, $container->getFirstIncrement());
		}

		// Reset
		$containers[0]->resetAutoIncrement();
		foreach ($containers as $container) {
			self::assertEquals(1, $container->getAutoIncrement());
		}

		// Set ID
		$containers[0]->setAutoIncrement(9);
		foreach ($containers as $container) {
			self::assertEquals(9, $container->getAutoIncrement());
		}

		// Increment
		$containers[6]->incrementId();
		foreach ($containers as $container) {
			self::assertEquals(10, $container->getAutoIncrement());
		}
		$containers[5]->incrementId();
		foreach ($containers as $container) {
			self::assertEquals(11, $container->getAutoIncrement());
		}
		$containers[5]->incrementId();
		foreach ($containers as $container) {
			self::assertEquals(12, $container->getAutoIncrement());
		}
		$containers[3]->incrementId();
		foreach ($containers as $container) {
			self::assertEquals(13, $container->getAutoIncrement());
			self::assertEquals(9, $container->getFirstIncrement());
		}

		// Reset
		$containers[3]->resetAutoIncrement();
		foreach ($containers as $container) {
			self::assertEquals(9, $container->getAutoIncrement());
		}
	}

	public function testInvalidHierarchy() : void {
		$gameContainer = new GameContainer(0);
		$baseContainer = new BaseContainer(1);

		$this->expectException(InvalidArgumentException::class);
		$gameContainer->addChild($baseContainer);
	}

	public function testAutoIncrementFromHierarchy() : void {
		$round = new Round('Round', 0);
		$roundContainer = $round->getGameContainer();
		$group1 = $round->group('Group 1', 1);
		$group1Container = $group1->getGameContainer();
		$group2 = $round->group('Group 2', 2);
		$group2Container = $group2->getGameContainer();
		$team1 = $group1->team('Team 1', 1);
		$team2 = $group1->team('Team 2', 2);
		$group2->addTeam($team1, $team2);

		$containers = [$roundContainer, $group1Container, $group2Container];
		foreach ($containers as $container) {
			self::assertEquals(1, $container->getAutoIncrement());
		}
		$game = $group1->game([$team1, $team2]);
		self::assertEquals(1, $game->getId());
		foreach ($containers as $container) {
			self::assertEquals(2, $container->getAutoIncrement());
		}
		$game = $group1->game([$team1, $team2]);
		self::assertEquals(2, $game->getId());
		foreach ($containers as $container) {
			self::assertEquals(3, $container->getAutoIncrement());
		}
		$game = $group2->game([$team1, $team2]);
		self::assertEquals(3, $game->getId());
		foreach ($containers as $container) {
			self::assertEquals(4, $container->getAutoIncrement());
		}
		$game = $group1->game([$team1, $team2]);
		self::assertEquals(4, $game->getId());
		foreach ($containers as $container) {
			self::assertEquals(5, $container->getAutoIncrement());
		}
		$game = $group2->game([$team1, $team2]);
		self::assertEquals(5, $game->getId());
		foreach ($containers as $container) {
			self::assertEquals(6, $container->getAutoIncrement());
		}

		// New Group
		$group3 = $round->group('Group 3', 3);
		$group3->addTeam($team1, $team2);
		$group3Container = $group3->getGameContainer();
		$containers[] = $group3Container;
		foreach ($containers as $container) {
			self::assertEquals(6, $container->getAutoIncrement());
		}


		$game = $group2->game([$team1, $team2]);
		self::assertEquals(6, $game->getId());
		foreach ($containers as $container) {
			self::assertEquals(7, $container->getAutoIncrement());
		}
	}

	public function testAutoIncrementFromHierarchy2() : void {
		$category = new Category('Category', 0);
		$categoryContainer = $category->getGameContainer();
		$round1 = $category->round('Round 1', 1);
		$round1Container = $round1->getGameContainer();
		$round2 = $category->round('Round 2', 2);
		$round2Container = $round2->getGameContainer();
		$group1 = $round1->group('Group 1', 1);
		$group1Container = $group1->getGameContainer();
		$group2 = $round1->group('Group 2', 2);
		$group2Container = $group2->getGameContainer();
		$round2Container = $round2->getGameContainer();
		$group3 = $round2->group('Group 3', 3);
		$group3Container = $group3->getGameContainer();
		$group4 = $round2->group('Group 4', 4);
		$group4Container = $group4->getGameContainer();
		$team1 = $group1->team('Team 1', 1);
		$team2 = $group1->team('Team 2', 2);
		$group2->addTeam($team1, $team2);
		$group3->addTeam($team1, $team2);
		$group4->addTeam($team1, $team2);

		$groups = [$group1, $group2, $group3, $group4];

		$containers = [$categoryContainer, $round1Container, $round2Container, $group1Container, $group2Container, $group3Container, $group4Container];
		foreach ($containers as $container) {
			self::assertEquals(1, $container->getAutoIncrement());
		}

		for ($i = 1; $i < 100; $i++) {
			$group = $groups[array_rand($groups)];
			$game = $group->game([$team1, $team2]);
			self::assertEquals($i, $game->getId());
			foreach ($containers as $container) {
				self::assertEquals($i + 1, $container->getAutoIncrement());
			}
		}
	}

	public function testAutoIncrementFromHierarchy3() : void {
		$tournament = new Tournament('Tournament');
		$tournamentContainer = $tournament->getGameContainer();
		$round1 = $tournament->round('Round 1', 1);
		$round1Container = $round1->getGameContainer();
		$round2 = $tournament->round('Round 2', 2);
		$round2Container = $round2->getGameContainer();
		$group1 = $round1->group('Group 1', 1);
		$group1Container = $group1->getGameContainer();
		$group2 = $round1->group('Group 2', 2);
		$group2Container = $group2->getGameContainer();
		$group3 = $round2->group('Group 3', 3);
		$group3Container = $group3->getGameContainer();
		$group4 = $round2->group('Group 4', 4);
		$group4Container = $group4->getGameContainer();
		$team1 = $group1->team('Team 1', 1);
		$team2 = $group1->team('Team 2', 2);
		$group2->addTeam($team1, $team2);
		$group3->addTeam($team1, $team2);
		$group4->addTeam($team1, $team2);

		$groups = [$group1, $group2, $group3, $group4];

		$containers = [$tournamentContainer, $round1Container, $round2Container, $group1Container, $group2Container, $group3Container, $group4Container];
		foreach ($containers as $container) {
			self::assertEquals(1, $container->getAutoIncrement());
		}

		for ($i = 1; $i < 100; $i++) {
			$group = $groups[array_rand($groups)];
			$game = $group->game([$team1, $team2]);
			self::assertEquals($i, $game->getId());
			foreach ($containers as $container) {
				self::assertEquals($i + 1, $container->getAutoIncrement());
			}
		}
	}
}