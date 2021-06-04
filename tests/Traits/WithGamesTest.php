<?php


namespace Traits;


use Exception;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Category;
use TournamentGenerator\Group;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithGroups;
use TournamentGenerator\Interfaces\WithRounds;
use TournamentGenerator\Round;
use TournamentGenerator\Tournament;

/**
 * Tests for WithGames trait
 *
 * @package Traits
 */
class WithGamesTest extends TestCase
{

	public function getClasses() : array {
		return [
			[new Tournament('Tournament')],
			[new Category('Category')],
			[new Round('Round')],
			[new Group('Group')],
		];
	}

	public function getClassesWithIncrements() : array {
		$data = [];
		$classes = [Tournament::class, Category::class, Round::class, Group::class];
		foreach ($classes as $class) {
			$increments = range(2, 20);
			foreach ($increments as $increment) {
				$data[] = array_merge([new $class('Class name')], [$increment]);
			}
		}
		return $data;
	}

	/**
	 * @dataProvider getClasses
	 *
	 * @param WithGames $class
	 */
	public function testGettingGames(WithGames $class) : void {
		$games = $this->setupGames($class);

		$gotGames = $class->getGames();
		self::assertCount(count($games), $gotGames);
		self::assertEquals($games, $gotGames);

		$gotGames = $class->getGameContainer()->get();
		self::assertCount(count($games), $gotGames);
		self::assertEquals($games, $gotGames);
	}

	/**
	 * @param WithGames $class
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function setupGames(WithGames $class) : array {
		$games = [];
		$teams = [];
		// Create 10 teams
		for ($i = 0; $i < 10; $i++) {
			$teams[$i] = $class->team('Team '.$i, $i);
		}

		// Create random rounds, groups and games
		if ($class instanceof WithRounds) {
			$rounds = random_int(1, 10);
			for ($i = 0; $i < $rounds; $i++) {
				$round = $class->round('Round '.$i, $i);
				$groups = random_int(1, 4);
				for ($ii = 0; $ii < $groups; $ii++) {
					$id = 4 * $i + $ii;
					$group = $round->group('Group '.$id, $id);
					$groupTeams = array_rand($teams, 4);
					foreach ($groupTeams as $team) {
						$group->addTeam($teams[$team]);
					}
					$gamesNum = random_int(3, 10);
					for ($iii = 0; $iii < $gamesNum; $iii++) {
						$teamsGame = array_map(static function(int $key) use ($teams, $groupTeams) {
							return $teams[$groupTeams[$key]];
						}, array_rand($groupTeams, 2));
						$games[] = $group->game($teamsGame);
					}
				}
			}
		}
		elseif ($class instanceof WithGroups) {
			$groups = random_int(2, 4);
			for ($ii = 0; $ii < $groups; $ii++) {
				$id = 4 * $i + $ii;
				$group = $class->group('Group '.$id, $id);
				$groupTeams = array_rand($teams, 4);
				foreach ($groupTeams as $team) {
					$group->addTeam($teams[$team]);
				}
				$gamesNum = random_int(3, 10);
				for ($iii = 0; $iii < $gamesNum; $iii++) {
					$teamsGame = array_map(static function(int $key) use ($teams, $groupTeams) {
						return $teams[$groupTeams[$key]];
					}, array_rand($groupTeams, 2));
					$games[] = $group->game($teamsGame);
				}
			}
		}
		else {
			$groupTeams = array_rand($teams, 4);
			foreach ($groupTeams as $team) {
				$class->addTeam($teams[$team]);
			}
			$gamesNum = random_int(3, 10);
			for ($iii = 0; $iii < $gamesNum; $iii++) {
				$teamsGame = array_map(static function(int $key) use ($teams, $groupTeams) {
					return $teams[$groupTeams[$key]];
				}, array_rand($groupTeams, 2));
				$games[] = $class->game($teamsGame);
			}
		}
		return $games;
	}

	/**
	 * @dataProvider getClasses
	 *
	 * @param WithGames $class
	 */
	public function testAutoincrement(WithGames $class) : void {
		$games = $this->setupGames($class);

		$expectedId = 1;
		foreach ($games as $game) {
			self::assertEquals($expectedId, $game->getId());
			$expectedId++;
		}
	}

	/**
	 * @dataProvider getClassesWithIncrements
	 *
	 * @param WithGames $class
	 */
	public function testSetAutoincrement(WithGames $class, int $startIncrement) : void {
		$class->setGameAutoincrementId($startIncrement);
		$games = $this->setupGames($class);

		$expectedId = $startIncrement;
		foreach ($games as $key => $game) {
			self::assertEquals($expectedId, $game->getId(), 'Expected ID did not match for game '.$key.'/'.count($games).PHP_EOL.'Start: '.$startIncrement.PHP_EOL.'Class: '.get_class($class));
			$expectedId++;
		}
	}

	/**
	 * @dataProvider getClasses
	 *
	 * @param WithGames $class
	 */
	public function testSettingResult(WithGames $class) : void {
		$this->setupGames($class);
		$teams = [];
		$games = [];
		if ($class instanceof WithGroups) {
			$groups = $class->getGroups();
			foreach ($groups as $group) {
				$teams[$group->getId()] = [];
				foreach ($group->getTeams() as $team) {
					$teams[$group->getId()][$team->getId()] = $team;
				}
				$games[$group->getId()] = $group->getGames();
			}
		}
		elseif ($class instanceof Group) {
			$teams[$class->getId()] = [];
			foreach ($class->getTeams() as $team) {
				$teams[$class->getId()][$team->getId()] = $team;
			}
			$games[$class->getId()] = $class->getGames();
		}

		// Test setting results for existing games
		foreach ($games as $groupGames) {
			foreach ($groupGames as $game) {
				$ids = $game->getTeamsIds();
				$results = [];
				foreach ($ids as $teamId) {
					$results[$teamId] = random_int(0, 10000);
				}
				$game2 = $class->setResults($results);
				self::assertSame($game, $game2);
				self::assertCount(count($results), $game->getResults());
			}
		}

		// Test setting results for invalid games
		if ($class instanceof WithGroups) {
			for ($i = 0; $i < 10; $i++) {
				$groups = array_rand($teams, 2);
				$results = [];
				foreach ($groups as $group) {
					$results[array_rand($teams[$group])] = random_int(0, 10000);
				}
				$game = $class->setResults($results);
				self::assertNull($game);
			}
		}
	}
}