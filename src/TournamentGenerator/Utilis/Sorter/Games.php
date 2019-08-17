<?php

namespace TournamentGenerator\Utilis\Sorter;

/**
 *Tournament generator sorter for games
 */

// WORKS BY COMPARING AVAILABLE GAMES BY THEIR TEAMS
// TEAMS FROM LAST 3 GAMES
// 1 = PLAYED ONLY 3RD GAME FROM END
// 2 = PLAYED ONLY 2ND GAME FROM END
// 3 = PLAYED 3RD AND 2ND GAME FROM END
// 4 = PLAYED ONLY THE LAST GAME
// 5 = PLAYED 3RD AND 1ST GAME FROM END
// 6 = PLAYED 2ND ANS 1ST GAME FROM END
// 7 = PLAYED ALL 3 LAST GAMES
class Games
{

	private $group;
	private $games = [];
	/**
	 * Orderes games from group
	 * @param \TournamentGenerator\Group $group
	 */
	function __construct(\TournamentGenerator\Group $group) {
		$this->group = $group;
	}

	/**
	 * Orderes games from group
	 * @return array
	 */
	public function orderGames() {

		$games = $this->group->getGames();

		if (count($games) <= 4) return $games;

		$this->games = [];

		$teams = [];
		foreach ($this->group->getTeams() as $team) { $teams[$team->getId()] = 0; }

		$this->moveCalculatedGames(array_shift($games), $teams);

		while (count($games) > 0) {
			// CYCLE 1
			// TEAM WHICH DIDN'T PLAY IN LAST GAME (< 4)
			if ($this->cycle1($games, $teams)) continue;

			// CYCLE 2
			// NOT TEAM WHICH PLAYED IN LAST TWO GAMES (NOT 6 or 7)
			if ($this->cycle2($games, $teams)) continue;

			// CYCLE 3
			// NOT TEAM WHICH PLAYED IN LAST THREE GAMES (NOT 7)
			// TEAMS THAT DIDN'T PLAY IN LAST GAME WILL PLAY THIS GAME (< 4)
			if ($this->cycle3($games, $teams)) continue;

			// CYCLE 4
			// NOT TEAM WHICH PLAYED IN LAST THREE GAMES (NOT 7)
			if ($this->cycle4($games, $teams)) continue;

			// CYCLE 5
			// TEAMS THAT DIDN'T PLAY IN LAST GAME WILL PLAY THIS GAME (< 4)
			if ($this->cycle5($games, $teams)) continue;

			// CYCLE 6
			// FIRST AVAILABLE GAME
			$this->moveCalculatedGames(array_shift($games),$teams);
		}

		return $this->games;
	}

	// TEAM WHICH DIDN'T PLAY IN LAST GAME (< 4)
	private function cycle1(array &$games, array &$teams) {
		$found = false;
		foreach ($games as $key => $game) {
			if ($this->orderCheckTeamsVal($game, $teams, [4,5,6,7])) {
				$this->moveCalculatedGames($game,$teams);
				unset($games[$key]);
				$found = true;
				break;
			}
		}
		return $found;
	}
	// NOT TEAM WHICH PLAYED IN LAST TWO GAMES (NOT 6 or 7)
	private function cycle2(array &$games, array &$teams) {
		$found = false;
		foreach ($games as $key => $game) {
			if ($this->orderCheckTeamsVal($game, $teams, [6,7])) {
				$this->moveCalculatedGames($game,$teams);
				unset($games[$key]);
				$found = true;
				break;
			}
		}
		return $found;
	}
	// NOT TEAM WHICH PLAYED IN LAST THREE GAMES (NOT 7)
	// TEAMS THAT DIDN'T PLAY IN LAST GAME WILL PLAY THIS GAME (< 4)
	private function cycle3(array &$games, array &$teams) {
		$found = false;
		foreach ($games as $key => $game) {
			if ($this->orderCheckTeamsVal($game, $teams, [7], [1,2,3])) {
				$this->moveCalculatedGames($game,$teams);
				unset($games[$key]);
				$found = true;
				break;
			}
		}
		return $found;
	}
	// NOT TEAM WHICH PLAYED IN LAST THREE GAMES (NOT 7)
	private function cycle4(array &$games, array &$teams) {
		$found = false;
		foreach ($games as $key => $game) {
			if ($this->orderCheckTeamsVal($game, $teams, [7])) {
				$this->moveCalculatedGames($game,$teams);
				unset($games[$key]);
				$found = true;
				break;
			}
		}
		return $found;
	}
	// TEAMS THAT DIDN'T PLAY IN LAST GAME WILL PLAY THIS GAME (< 4)
	private function cycle5(array &$games, array &$teams) {
		$found = false;
		foreach ($games as $key => $game) {
			if ($this->orderCheckTeamsVal($game, $teams, [], [1,2,3])) {
				$this->moveCalculatedGames($game,$teams);
				unset($games[$key]);
				$found = true;
				break;
			}
		}
		return $found;
	}

	private function moveCalculatedGames(\tournamentGenerator\Game $game, array &$teams) {

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
				$teams[$tid] -= 1;
			}
		}
		if (count($this->games) > 3) {
			foreach (prev($this->games)->getTeamsIds() as $tid) {
				$teams[$tid] -= 1;
			}
		}

		return $teams;

	}
	private function orderCheckTeamsVal(\tournamentGenerator\Game $game, array &$teams, array $checkVals, array $required = []) {

		$requiredTeams = array_filter($teams, function($a) use ($required) { return in_array($a, $required); });

		foreach ($game->getTeamsIds() as $tid) {
			if (in_array($teams[$tid], $checkVals)) return false;
			if (isset($requiredTeams[$tid])) unset($requiredTeams[$tid]);
		}

		if (count($requiredTeams) > 0) return false;

		return true;

	}

}
