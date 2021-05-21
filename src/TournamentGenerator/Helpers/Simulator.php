<?php

namespace TournamentGenerator\Helpers;

use Exception;
use TournamentGenerator\Category;
use TournamentGenerator\Game;
use TournamentGenerator\Group;
use TournamentGenerator\Interfaces\WithRounds;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;
use TournamentGenerator\Tournament;

/**
 * Class responsible for simulating a tournament
 *
 * Simulating a tournament can be useful if you want to generate all the games beforehand.
 * It generates random scores and progresses teams into next groups / rounds to generate them too.
 * This will allow generating the whole tournament beforehand to calculate all the games and time necessary to play.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @package TournamentGenerator\Helpers
 *
 * @since   0.3
 */
class Simulator
{

	/**
	 * Simulates games in a given group.
	 *
	 * @param Group                       $group   Group to simulate
	 * @param TeamFilter[]|TeamFilter[][] $filters Filters applied to returned teams (ex. if you only want to return the winning team)
	 * @param bool                        $reset   If the group should reset its scores after simulation
	 *
	 * @return Team[] Teams sorted and filtered after simulation
	 * @throws Exception
	 */
	public static function simulateGroup(Group $group, array $filters = [], bool $reset = true) : array {
		foreach ($group->getGames() as $game) {
			$teams = $game->getTeams();
			$results = [];
			foreach ($teams as $team) {
				$results[$team->getId()] = floor(random_int(0, 500));
			}
			$game->setResults($results);
		}
		$returnTeams = $group->sortTeams(null, $filters);
		if (!$reset) {
			return $returnTeams;
		}
		foreach ($group->getGames() as $game) {
			$game->resetResults();
		}
		return $returnTeams;
	}

	/**
	 * Simulates games in a round
	 *
	 * @param Round $round Round to simulate
	 *
	 * @throws Exception
	 */
	public static function simulateRound(Round $round) : void {
		foreach ($round->getGroups() as $group) {
			if ($group->isPlayed()) {
				continue;
			}
			$group->simulate([], false);
		}
	}

	/**
	 * Simulate the whole tournament as it was played for real
	 *
	 * Generates the games for each round, simulates it, progresses the teams and resets the scores. Uses dummy team objects to progress.
	 *
	 * @param Tournament $tournament Tournament to simulate
	 *
	 * @post The games' scores will be reset
	 *
	 * @return Game[] All played games
	 * @throws Exception
	 * @see  Simulator::simulateRounds()
	 *
	 */
	public static function simulateTournament(Tournament $tournament) : array {
		return self::simulateRounds($tournament);
	}

	/**
	 * Simulate the whole tournament as it was played for real
	 *
	 * Generates the games for each round, simulates it, progresses the teams and resets the scores. Uses dummy team objects to progress.
	 *
	 * @param WithRounds $object Tournament or Category to generate games from.
	 *
	 * @return Game[] All played games
	 * @throws Exception
	 * @post The games' scores will be reset
	 *
	 */
	public static function simulateRounds(WithRounds $object) : array {
		if (count($object->getRounds()) === 0) {
			throw new Exception('There are no rounds to simulate games from.');
		}

		/** @var Game[][] $games Array of games for each round */
		$games = [];

		foreach ($object->getRounds() as $round) {
			$games[] = $round->genGames();
			$round
				->simulate()
				->progress(true);
		}
		foreach ($object->getRounds() as $round) {
			$round->resetGames();
		}

		return array_merge(...$games);
	}

	/**
	 * Generates and simulates the tournament as if it was played for real.
	 *
	 * Progresses the real teams, does not create dummy teams.
	 *
	 * @param Tournament $tournament Tournament to simulate
	 *
	 * @return Game[]
	 * @throws Exception
	 * @see Simulator::simulateRoundsReal()
	 */
	public static function simulateTournamentReal(Tournament $tournament) : array {
		return self::simulateRoundsReal($tournament);
	}

	/**
	 * Generates and simulates rounds as if it was played for real.
	 *
	 * Progresses the real teams, does not create dummy teams.
	 *
	 * @param WithRounds $object Tournament or Category to generate games from.
	 *
	 * @return Game[]
	 * @throws Exception
	 */
	public static function simulateRoundsReal(WithRounds $object) : array {
		if (count($object->getRounds()) === 0) {
			throw new Exception('There are no rounds to simulate games from.');
		}

		/** @var Game[][] $games Array of games for each round */
		$games = [];

		foreach ($object->getRounds() as $round) {
			$games[] = $round->genGames();
			$round
				->simulate()
				->progress();
		}
		return array_merge(...$games);
	}

	/**
	 * Generates and simulates a tournament category.
	 *
	 * Generates the games for each round, simulates it, progresses the teams and resets the scores. Uses dummy team objects to progress.
	 *
	 * @param Category $category Category to simulate
	 *
	 * @return Game[]
	 * @throws Exception
	 * @see Simulator::simulateRounds()
	 */
	public static function simulateCategory(Category $category) : array {
		return self::simulateRounds($category);
	}

	/**
	 * Generates and simulates a category as if it was played for real.
	 *
	 * Progresses the real teams, does not create dummy teams.
	 *
	 * @param Category $category Category to simulate
	 *
	 * @return Game[]
	 * @throws Exception
	 * @see Simulator::simulateRoundsReal()
	 */
	public static function simulateCategoryReal(Category $category) : array {
		return self::simulateRoundsReal($category);
	}

}
