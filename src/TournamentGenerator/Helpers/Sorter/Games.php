<?php

namespace TournamentGenerator\Helpers\Sorter;

use TournamentGenerator\Game;
use TournamentGenerator\Group;

/**
 * Tournament generator sorter for games
 *
 * WORKS BY COMPARING AVAILABLE GAMES BY THEIR TEAMS
 * TEAMS FROM LAST 3 GAMES
 * 1 = PLAYED ONLY 3RD GAME FROM END
 * 2 = PLAYED ONLY 2ND GAME FROM END
 * 3 = PLAYED 3RD AND 2ND GAME FROM END
 * 4 = PLAYED ONLY THE LAST GAME
 * 5 = PLAYED 3RD AND 1ST GAME FROM END
 * 6 = PLAYED 2ND ANS 1ST GAME FROM END
 * 7 = PLAYED ALL 3 LAST GAMES
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @package TournamentGenerator\Helpers\Sorter
 * @since   0.3
 */
class Games
{

	/** @var Group */
	private Group $group;
	/** @var Game[] */
	private array $games = [];

	/**
	 * Orders games from group
	 *
	 * @param Group $group
	 */
	public function __construct(Group $group) {
		$this->group = $group;
	}

	/**
	 * Orders games from group
	 *
	 * @return array
	 */
	public function orderGames() : array {

		$games = $this->group->getGames();

		if (count($games) <= 4) {
			return $games;
		}

		$this->games = [];

		$teams = [];
		foreach ($this->group->getTeams() as $team) {
			$teams[$team->getId()] = 0;
		}

		$this->moveCalculatedGames(array_shift($games), $teams);

		while (count($games) > 0) {
			// CYCLE 1
			// TEAM WHICH DIDN'T PLAY IN LAST GAME (< 4)
			if ($this->cycle1($games, $teams)) {
				continue;
			}

			// CYCLE 2
			// NOT TEAM WHICH PLAYED IN LAST TWO GAMES (NOT 6 or 7)
			if ($this->cycle2($games, $teams)) {
				continue;
			}

			// CYCLE 3
			// NOT TEAM WHICH PLAYED IN LAST THREE GAMES (NOT 7)
			// TEAMS THAT DIDN'T PLAY IN LAST GAME WILL PLAY THIS GAME (< 4)
			if ($this->cycle3($games, $teams)) {
				continue;
			}

			// CYCLE 4
			// NOT TEAM WHICH PLAYED IN LAST THREE GAMES (NOT 7)
			if ($this->cycle4($games, $teams)) {
				continue;
			}

			// CYCLE 5
			// TEAMS THAT DIDN'T PLAY IN LAST GAME WILL PLAY THIS GAME (< 4)
			if ($this->cycle5($games, $teams)) {
				continue;
			}

			// CYCLE 6
			// FIRST AVAILABLE GAME
			$this->moveCalculatedGames(array_shift($games), $teams);
		}

		return $this->games;
	}

	/**
	 * Move teams that did not play in the last game (< 4)
	 *
	 * @param Game  $game
	 * @param array $teams
	 */
	private function moveCalculatedGames(Game $game, array &$teams) : void {

		$this->games[] = $game;

		foreach (end($this->games)->getTeamsIds() as $tid) {
			$teams[$tid] += 4;
		}

		if (count($this->games) > 1) {
			foreach (prev($this->games)->getTeamsIds() as $tid) {
				$teams[$tid] -= 2;
			}
		}
		if (count($this->games) > 2) {
			foreach (prev($this->games)->getTeamsIds() as $tid) {
				--$teams[$tid];
			}
		}
		if (count($this->games) > 3) {
			foreach (prev($this->games)->getTeamsIds() as $tid) {
				--$teams[$tid];
			}
		}

	}

	/**
	 * Teams that did not play in the last game (< 4)
	 *
	 * @param array $games
	 * @param array $teams
	 *
	 * @return bool
	 */
	private function cycle1(array &$games, array &$teams) : bool {
		$found = false;
		foreach ($games as $key => $game) {
			if ($this->orderCheckTeamsVal($game, $teams, [4, 5, 6, 7])) {
				$this->moveCalculatedGames($game, $teams);
				unset($games[$key]);
				$found = true;
				break;
			}
		}
		return $found;
	}

	/**
	 * Get first available game
	 *
	 * @param Game  $game
	 * @param array $teams
	 * @param array $checkVals
	 * @param array $required
	 *
	 * @return bool
	 */
	private function orderCheckTeamsVal(Game $game, array $teams, array $checkVals, array $required = []) : bool {

		$requiredTeams = array_filter($teams, static function($a) use ($required) {
			return in_array($a, $required, true);
		});

		foreach ($game->getTeamsIds() as $tid) {
			if (in_array($teams[$tid], $checkVals, true)) {
				return false;
			}
			if (isset($requiredTeams[$tid])) {
				unset($requiredTeams[$tid]);
			}
		}

		return !(count($requiredTeams) > 0);

	}

	/**
	 * Teams that did not play in the last two games (not 6 or 7)
	 *
	 * @param array $games
	 * @param array $teams
	 *
	 * @return bool
	 */
	private function cycle2(array &$games, array &$teams) : bool {
		$found = false;
		foreach ($games as $key => $game) {
			if ($this->orderCheckTeamsVal($game, $teams, [6, 7])) {
				$this->moveCalculatedGames($game, $teams);
				unset($games[$key]);
				$found = true;
				break;
			}
		}
		return $found;
	}

	/**
	 * Teams that did not play in the last three games (not 7) and teams that did not play in the last game (< 4)
	 *
	 * @param array $games
	 * @param array $teams
	 *
	 * @return bool
	 */
	private function cycle3(array &$games, array &$teams) : bool {
		$found = false;
		foreach ($games as $key => $game) {
			if ($this->orderCheckTeamsVal($game, $teams, [7], [1, 2, 3])) {
				$this->moveCalculatedGames($game, $teams);
				unset($games[$key]);
				$found = true;
				break;
			}
		}
		return $found;
	}

	/**
	 * Teams that did not play in the last three games (not 7)
	 *
	 * @param array $games
	 * @param array $teams
	 *
	 * @return bool
	 */
	private function cycle4(array &$games, array &$teams) : bool {
		$found = false;
		foreach ($games as $key => $game) {
			if ($this->orderCheckTeamsVal($game, $teams, [7])) {
				$this->moveCalculatedGames($game, $teams);
				unset($games[$key]);
				$found = true;
				break;
			}
		}
		return $found;
	}

	/**
	 * Teams that did not play in the last game will play this game (< 4)
	 *
	 * @param array $games
	 * @param array $teams
	 *
	 * @return bool
	 */
	private function cycle5(array &$games, array &$teams) : bool {
		$found = false;
		foreach ($games as $key => $game) {
			if ($this->orderCheckTeamsVal($game, $teams, [], [1, 2, 3])) {
				$this->moveCalculatedGames($game, $teams);
				unset($games[$key]);
				$found = true;
				break;
			}
		}
		return $found;
	}

}
