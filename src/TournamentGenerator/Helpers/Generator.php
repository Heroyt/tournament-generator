<?php

namespace TournamentGenerator\Helpers;

use Exception;
use TournamentGenerator\Constants;
use TournamentGenerator\Game;
use TournamentGenerator\Group;
use TournamentGenerator\Interfaces\WithGeneratorSetters;
use TournamentGenerator\Interfaces\WithSkipSetters;
use TournamentGenerator\Team;
use TournamentGenerator\Traits\WithSkipSetters as WithSkipSettersTrait;

/**
 * Generator class is responsible for generating all different games in rounds.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @package TournamentGenerator\Helpers
 *
 * @since   0.3
 */
class Generator implements WithGeneratorSetters, WithSkipSetters
{
	use WithSkipSettersTrait;

	/** @var Group Group object */
	private Group $group;
	/** @var string Type of a round to create */
	private string $type = Constants::ROUND_ROBIN;
	/** @var int Number of teams in one game - 2/3/4 */
	private int $inGame = 2;
	/** @var int Maximum size of group before split */
	private int $maxSize = 4;

	/**
	 * Generator constructor.
	 *
	 * @param Group $group Group object to generate the games from
	 */
	public function __construct(Group $group) {
		$this->group = $group;
	}

	/**
	 * Get round type
	 *
	 * @return string
	 */
	public function getType() : string {
		return $this->type;
	}

	/**
	 * Set round type
	 *
	 * @param string $type
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function setType(string $type = Constants::ROUND_ROBIN) : Generator {
		if (in_array($type, Constants::GroupTypes, true)) {
			$this->type = $type;
		}
		else {
			throw new Exception('Unknown group type: '.$type);
		}
		return $this;
	}

	/**
	 * Get how many teams are playing in one game
	 *
	 * @return int
	 */
	public function getInGame() : int {
		return $this->inGame;
	}

	/**
	 * Set how many teams are playing in one game
	 *
	 * @param int $inGame
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function setInGame(int $inGame) : Generator {
		if ($inGame < 2 || $inGame > 4) {
			throw new Exception('Expected 2,3 or 4 as inGame '.$inGame.' given');
		}
		$this->inGame = $inGame;
		return $this;
	}

	/**
	 * Get tha maximum group size
	 *
	 * @return int
	 */
	public function getMaxSize() : int {
		return $this->maxSize;
	}

	/**
	 * Set the maximum group size
	 *
	 * @param int $size
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function setMaxSize(int $size) : Generator {
		if ($size < 2) {
			throw new Exception('Max group size has to be at least 2, '.$size.' given');
		}
		$this->maxSize = $size;
		return $this;
	}

	/**
	 * Generate games for a round
	 *
	 * @return Game[] List of generated games
	 * @throws Exception
	 */
	public function genGames() : array {
		switch ($this->type) {
			case Constants::ROUND_ROBIN:
				$this->group->addGame(...$this->r_rGames());
				break;
			case Constants::ROUND_TWO:
				$this->two_twoGames();
				break;
			case Constants::ROUND_SPLIT:
				$this->cond_splitGames();
				break;
		}
		return $this->group->getGames();
	}

	/**
	 * Generate round-robin games
	 *
	 * @param array $teams
	 *
	 * @return Game[]
	 * @throws Exception
	 */
	protected function r_rGames(array $teams = []) : array {
		$games = [];
		if (count($teams) === 0) {
			$teams = $this->group->getTeams();
		}
		switch ($this->inGame) {
			case 2:
				$games = self::circle_genGames2($this->group, $teams);
				break;
			case 3:
				$games = $this->r_r3Games($teams, $games);
				break;
			case 4:
				$games = $this->r_r4Games($teams, $games);
				break;
		}
		return $games;
	}

	/**
	 * Generate games for round robin and 2 teams in one game
	 *
	 * @param array $teams
	 * @param Group $group
	 *
	 * @return Game[]
	 * @throws Exception
	 */
	public static function circle_genGames2(Group $group, array $teams = []) : array {

		if (count($teams) % 2 !== 0) {
			$teams[] = Constants::DUMMY_TEAM;
		} // IF NOT EVEN NUMBER OF TEAMS, ADD DUMMY

		shuffle($teams); // SHUFFLE TEAMS FOR MORE RANDOMNESS

		$rounds = [];
		for ($i = 0; $i < count($teams) - 1; $i++) {
			$rounds[] = self::circle_saveBracket($teams, $group); // SAVE CURRENT ROUND

			$teams = self::circle_rotateBracket($teams); // ROTATE TEAMS IN BRACKET
		}

		return array_merge(...$rounds);
	}

	/**
	 * Get one generated round-robin round
	 *
	 * @param array $teams
	 * @param Group $group
	 *
	 * @return Game[]
	 * @throws Exception
	 */
	public static function circle_saveBracket(array $teams, Group $group) : array {

		$bracket = [];

		for ($i = 0; $i < count($teams) / 2; $i++) { // GO THROUGH HALF OF THE TEAMS

			$home = $teams[$i];
			$reverse = array_reverse($teams);
			$away = $reverse[$i];

			if ($home === Constants::DUMMY_TEAM || $away === Constants::DUMMY_TEAM) {
				continue;
			} // SKIP WHEN DUMMY_TEAM IS PRESENT

			$bracket[] = new Game([$home, $away], $group);

		}

		return $bracket;

	}

	/**
	 * Rotate array of teams
	 *
	 * @param array $teams
	 *
	 * @return array
	 */
	public static function circle_rotateBracket(array $teams) : array {

		$first = array_shift($teams); // THE FIRST TEAM REMAINS FIRST
		$last = array_shift($teams);  // THE SECOND TEAM MOVES TO LAST PLACE

		return array_merge([$first], $teams, [$last]); // MERGE BACK TOGETHER

	}

	/**
	 * Generate a round-robin for three teams in one game
	 *
	 * @param array     $teams
	 * @param array     $games
	 * @param Team|null $lockedTeam1
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function r_r3Games(array $teams, array &$games, Team $lockedTeam1 = null) : array {
		$teamsB = $teams;
		$generatedGames = [];
		while (count($teamsB) >= 3) {
			$lockedTeam = array_shift($teamsB);
			$gamesTemp = self::circle_genGames2($this->group, $teamsB);
			foreach ($gamesTemp as $game) {
				if (isset($lockedTeam1)) {
					$game->addTeam($lockedTeam1);
				}
				$game->addTeam($lockedTeam);
			}
			$generatedGames[] = $gamesTemp;
		}
		$games = array_merge($games, ...$generatedGames);
		return $games;
	}

	/**
	 * Generate a round-robin for four teams in one game
	 *
	 * @param array $teams
	 * @param array $games
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function r_r4Games(array $teams, array &$games) : array {
		$teamsB = $teams;
		$lockedTeam1 = array_shift($teamsB);
		while (count($teamsB) >= 4) {
			$this->r_r3Games($teamsB, $games, $lockedTeam1);
			$lockedTeam1 = array_shift($teamsB);
		}
		$games[] = new Game(array_merge([$lockedTeam1], $teamsB), $this->group);
		return $games;
	}

	/**
	 * Generates games for teams, where a team plays only against one other team
	 *
	 * @param array $teams
	 *
	 * @return Generator
	 * @throws Exception
	 */
	protected function two_twoGames(array $teams = []) : Generator {
		if (count($teams) === 0) {
			$teams = $this->group->getTeams();
		}
		shuffle($teams);
		$count = count($teams); // Store original count for exception

		$discard = array_splice($teams, 0, $count % $this->inGame);

		$teams = $this->saveTwoTwoGames($teams);

		if (!$this->allowSkip && count($discard) > 0) {
			throw new Exception('Couldn\'t make games with all teams. Expected k*'.$this->inGame.' teams '.$count.' teams given - discarting '.count($discard).' teams ('.implode(', ', $discard).') in group '.$this->group.' - allow skip '.($this->allowSkip ? 'True' : 'False'));
		}
		return $this;
	}

	/**
	 * Create games from array of teams for a Two-Two game
	 *
	 * @param Team[] $teams
	 *
	 * @pre The input array must be divisible by the Generator::$inGame value
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function saveTwoTwoGames(array $teams) : array {
		while (count($teams) > 0) {
			$tInGame = [];
			for ($i = 0; $i < $this->inGame; $i++) {
				$tInGame[] = array_shift($teams);
			}
			$this->group->game($tInGame);
		}
		return $teams;
	}

	/**
	 * Automatically split teams in a group
	 *
	 * @param array $teams
	 *
	 * @return Generator
	 * @throws Exception
	 */
	protected function cond_splitGames(array $teams = []) : Generator {
		if (count($teams) === 0) {
			$teams = $this->group->getTeams();
		}

		$count = count($teams);
		if ($count > $this->maxSize) {
			$games = [];

			// Split teams into chunks of maximum size
			$groups = array_chunk($teams, (int) ceil($count / ceil($count / $this->maxSize)));
			// Generate games for each chunk
			foreach ($groups as $group) {
				$games[] = $this->r_rGames($group);
			}

			$this->fillGamesFromChunks($games);

			return $this;
		}
		$this->group->addGame(...$this->r_rGames());

		return $this;
	}

	/**
	 * Add games from chunks to groups
	 *
	 * Fills game in alternating order (chunk 1 - 1, chunk 2 - 1 , chunk 3 - 1, chunk 1 - 2, chunk 2 - 2...)
	 *
	 * @param Game[][] $games
	 *
	 * @throws Exception
	 */
	protected function fillGamesFromChunks(array $games) : void {
		$gameCount = Functions::nestedCount($games);
		while ($gameCount > 0) {
			foreach ($games as $key => $group) {
				$this->group->addGame(array_shift($games[$key]));
				if (count($games[$key]) === 0) {
					unset($games[$key]);
				}
				$gameCount--;
			}
		}
	}

	/**
	 * Sort games to minimize teams playing multiple games after one other
	 *
	 * @pre  The autoincrement must be reset on the group's container
	 * @post All games will have reset ids in the new order
	 *
	 * @return array
	 * @throws Exception
	 */
	public function orderGames() : array {
        return (new Sorter\GameSorter($this->group))->sort($this->group->getGames());
	}

}
