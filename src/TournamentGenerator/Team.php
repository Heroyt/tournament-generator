<?php

namespace TournamentGenerator;

/**
 * Class that identifies a team and holds the scores from the whole tournament.
 *
 * @author Tomáš Vojík <vojik@wboy.cz>
 *
 * @since v0.1
 *
 */
class Team extends Base
{

	/**
	* @var string      $name          The name of the team
	* @var string|int  $id            The unique identifier of the team
	*/

	/** @var array     $games         A list of games played by this team */
	protected $games = [];

	/** @var array     $gamesWith     Multi-dimensional associative array of number of games together with other teams */
	protected $gamesWith = [];

	/** @var int       $sumPoints     Sum of all points acquired through the whole tournament */
	protected $sumPoints = 0;

	/** @var int       $sumScore      Sum of all score acquired through the whole tournament */
	protected $sumScore = 0;

	/**
	* @var array       $groupResults  Associative array of results in each group
	*
	* array (
	* * groupId => array (
	* * * "group"  => Group, # GROUP OBJECT
	* * * "points" => int 0, # NUMBER OF POINTS AQUIRED
	* * * "score"  => int 0, # SUM OF SCORE AQUIRED
	* * * "wins"   => int 0, # NUMBER OF WINS
	* * * "draws"  => int 0, # NUMBER OF DRAWS
	* * * "losses" => int 0, # NUMBER OF LOSSES
	* * * "second" => int 0, # NUMBER OF TIMES BEING SECOND (ONLY FOR INGAME OPTION OF 3 OR 4)
	* * * "third"  => int 0  # NUMBER OF TIMES BEING THIRD  (ONLY FOR INGAME OPTION OF 4)
	* * )
	*)
	*/
	public $groupResults = [];

	/**
	* Initiates a team class
	*
	* @param string     $name  Name of the team
	* @param string|int $id    Unique identifier of the team
	*
	* @throws \InvalidArgumentException if the provided argument id is not of type 'null' or 'string' or 'int'
	*/
	function __construct(string $name = 'team', $id = null) {
		$this->setName($name);
		$this->setId(isset($id) ? $id : uniqid());
	}
	/**
	* Gets team statistics from the given group without the group object
	*
	* @param string|int $groupId  Unique identifier of the group to get its results
	*
	* @throws \Exception if the group with given groupId doesn't exist
	*
	* @return array  All the statistics including points, score, wins, draws, losses, times being second, times being third
	*/
	public function getGamesInfo($groupId) {
		return array_filter($this->getGroupResults($groupId), function($k) { return $k !== 'group'; }, ARRAY_FILTER_USE_KEY);
	}

	/**
	* Creates a new data-array to store statistics for a new group
	*
	* Resets the statistics if the group was already added
	*
	* @param Group $group  A group object to add its results
	*
	* @return self
	*/
	public function addGroupResults(Group $group) {
		$this->groupResults[$group->getId()] = [
			'group' => $group,
			'points' => 0,
			'score'  => 0,
			'wins'   => 0,
			'draws'  => 0,
			'losses' => 0,
			'second' => 0,
			'third'  => 0
		];
		return $this;
	}
	/**
	* Gets team statistics from the given group
	*
	* @param string|int|null $groupId  Unique identifier of the group to get its results
	*
	* @throws \Exception if the group with given groupId doesn't exist
	*
	* @return array  All the statistics including points, score, wins, draws, losses, times being second, times being third if the group id is set or all the statistics
	*/
	public function getGroupResults($groupId = null) {
		if (isset($groupId)) {
			if (!isset($this->groupResults[$groupId])) throw new \Exception('Trying to get unexisting group results ('.$groupId.')');
			return $this->groupResults[$groupId];
		}
		return $this->groupResults;
	}

	/**
	* Adds a record of a game with another team in a group
	*
	* @param Team  $team   A team that played with this team
	* @param Group $group  A group that the teams were playing in
	*
	* @return self
	*/
	public function addGameWith(Team $team, Group $group) {
		if (!isset($this->gamesWith[$group->getId()][$team->getId()])) $this->gamesWith[$group->getId()][$team->getId()] = 0;
		$this->gamesWith[$group->getId()][$team->getId()]++;
		return $this;
	}
	/**
	* Gets a record of a game with another team or teams
	*
	* @param Team|null  $team   A team to get the games with
	* @param Group|null $group  A group from where to get the games
	*
	* @return array|int The number of games played with a team in a group if both arguments are given, array of all games with all teams from a group if only group is given, array of games with team from all groups if only a team argument is given or all games with all teams from all groups if no argument is given
	*/
	public function getGameWith(Team $team = null, Group $group = null) {
		if (isset($group)) {
			if (isset($team)) return $this->gamesWith[$group->getId()][$team->getId()];
			return $this->gamesWith[$group->getId()];
		}
		if (isset($team)) {
			$return = [];
			foreach ($this->gamesWith as $id => $games) {
				$filter = array_filter($games, function($key) use ($team){
					return $key === $team->getId();
				}, ARRAY_FILTER_USE_KEY);
				if (count($filter) > 0) $return[$id] = $filter;
			}
			return $return;
		}
		return $this->gamesWith;
	}
	/**
	* Adds a group to a team and creates an array for all games to be played
	*
	* @param Group $group  A group to add
	*
	* @return self
	*/
	public function addGroup(Group $group) {
		if (!isset($this->games[$group->getId()])) $this->games[$group->getId()] = [];
		return $this;
	}
	/**
	* Adds a game to this team
	*
	* @param Game $game  A game to add
	*
	* @return self
	*/
	public function addGame(Game $game) {
		$group = $game->getGroup();
		if (!isset($this->games[$group->getId()])) $this->games[$group->getId()] = [];
		$this->games[$group->getId()][] = $game;
		return $this;
	}
	/**
	* Gets all game from given group
	*
	* @param Group|null      $group    A group to get its game from
	* @param string|int|null $groupId  An id of group to get its game from
	*
	* @return array Games from a group or all games if both arguments are null
	*/
	public function getGames(Group $group = null, $groupId = null) {
		if (isset($group) && isset($this->games[$group->getId()])) return $this->games[$group->getId()];
		if (isset($groupId) && isset($this->games[$groupId])) return $this->games[$groupId];
		return $this->games;
	}

	/**
	* Gets all points that the team has acquired through the tournament
	*
	* @return int Sum of the points acquired
	*/
	public function getSumPoints() {
		return $this->sumPoints;
	}
	/**
	* Gets all score that the team has acquired through the tournament
	*
	* @return int Sum of the score acquired
	*/
	public function getSumScore() {
		return $this->sumScore;
	}

	/**
	* Adds a win points to the team
	*
	* @param string|int|null $groupId  An id of group to add the results from
	*
	* @throws \Exception if the group results have not been added
	*
	* @uses Group::getWinPoints() to retrieve the points to add
	*
	* @return self
	*/
	public function addWin(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->getWinPoints();
		$this->sumPoints += $this->groupResults[$groupId]['group']->getWinPoints();
		$this->groupResults[$groupId]['wins']++;
		return $this;
	}
	/**
	* Remove win points to the team
	*
	* @param string|int|null $groupId  An id of group to add the results from
	*
	* @throws \Exception if the group results have not been added
	*
	* @uses Group::getWinPoints() to retrieve the points to add
	*
	* @return self
	*/
	public function removeWin(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->getWinPoints();
		$this->sumPoints -= $this->groupResults[$groupId]['group']->getWinPoints();
		$this->groupResults[$groupId]['wins']--;
		return $this;
	}

	/**
	* Adds a draw points to the team
	*
	* @param string|int|null $groupId  An id of group to add the results from
	*
	* @throws \Exception if the group results have not been added
	*
	* @uses Group::getDrawPoints() to retrieve the points to add
	*
	* @return self
	*/
	public function addDraw(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->getDrawPoints();
		$this->sumPoints += $this->groupResults[$groupId]['group']->getDrawPoints();
		$this->groupResults[$groupId]['draws']++;
		return $this;
	}
	/**
	* Remove draw points to the team
	*
	* @param string|int|null $groupId  An id of group to add the results from
	*
	* @throws \Exception if the group results have not been added
	*
	* @uses Group::getDrawPoints() to retrieve the points to add
	*
	* @return self
	*/
	public function removeDraw(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->getDrawPoints();
		$this->sumPoints -= $this->groupResults[$groupId]['group']->getDrawPoints();
		$this->groupResults[$groupId]['draws']--;
		return $this;
	}

	/**
	* Adds a loss points to the team
	*
	* @param string|int|null $groupId  An id of group to add the results from
	*
	* @throws \Exception if the group results have not been added
	*
	* @uses Group::getLossPoints() to retrieve the points to add
	*
	* @return self
	*/
	public function addLoss(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->getLostPoints();
		$this->sumPoints += $this->groupResults[$groupId]['group']->getLostPoints();
		$this->groupResults[$groupId]['losses']++;
		return $this;
	}
	/**
	* Remove loss points to the team
	*
	* @param string|int|null $groupId  An id of group to add the results from
	*
	* @throws \Exception if the group results have not been added
	*
	* @uses Group::getLossPoints() to retrieve the points to add
	*
	* @return self
	*/
	public function removeLoss(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->getLostPoints();
		$this->sumPoints -= $this->groupResults[$groupId]['group']->getLostPoints();
		$this->groupResults[$groupId]['losses']--;
		return $this;
	}

	/**
	* Adds points for being second to the team
	*
	* @param string|int|null $groupId  An id of group to add the results from
	*
	* @throws \Exception if the group results have not been added
	*
	* @uses Group::getSecondPoints() to retrieve the points to add
	*
	* @return self
	*/
	public function addSecond(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->getSecondPoints();
		$this->sumPoints += $this->groupResults[$groupId]['group']->getSecondPoints();
		$this->groupResults[$groupId]['second']++;
		return $this;
	}
	/**
	* Remove points for being second to the team
	*
	* @param string|int|null $groupId  An id of group to add the results from
	*
	* @throws \Exception if the group results have not been added
	*
	* @uses Group::getSecondPoints() to retrieve the points to add
	*
	* @return self
	*/
	public function removeSecond(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->getSecondPoints();
		$this->sumPoints -= $this->groupResults[$groupId]['group']->getSecondPoints();
		$this->groupResults[$groupId]['second']--;
		return $this;
	}

	/**
	* Adds points for being third to the team
	*
	* @param string|int|null $groupId  An id of group to add the results from
	*
	* @throws \Exception if the group results have not been added
	*
	* @uses Group::getThirdPoints() to retrieve the points to add
	*
	* @return self
	*/
	public function addThird(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->getThirdPoints();
		$this->sumPoints += $this->groupResults[$groupId]['group']->getThirdPoints();
		$this->groupResults[$groupId]['third']++;
		return $this;
	}
	/**
	* Remove points for being third to the team
	*
	* @param string|int|null $groupId  An id of group to add the results from
	*
	* @throws \Exception if the group results have not been added
	*
	* @uses Group::getThirdPoints() to retrieve the points to add
	*
	* @return self
	*/
	public function removeThird(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->getThirdPoints();
		$this->sumPoints -= $this->groupResults[$groupId]['group']->getThirdPoints();
		$this->groupResults[$groupId]['third']--;
		return $this;
	}

	/**
	* Calculate all the points acquired from given group ids
	*
	* @param array $groupIds  Array of group ids
	*
	* @return int Sum of the points or sum of all points if argument is empty
	*/
	public function sumPoints(array $groupIds = []) {
		if (count($groupIds) === 0) return $this->sumPoints;
		$sum = 0;
		foreach ($groupIds as $gid) {
			$sum += $this->groupResults[$gid]['points'] ?? 0;
		}
		return $sum;
	}
	/**
	* Calculate all score acquired from given group ids
	*
	* @param array $groupIds  Array of group ids
	*
	* @return int Sum of score or sum of all score if argument is empty
	*/
	public function sumScore(array $groupIds = []) {
		if (count($groupIds) === 0) return $this->sumScore;
		$sum = 0;
		foreach ($groupIds as $gid) {
			$sum += $this->groupResults[$gid]['score'] ?? 0;
		}
		return $sum;
	}

	/**
	* Adds score to the total sum
	*
	* @param int $score  Score to add
	*
	* @return self
	*/
	public function addScore(int $score) {
		$this->sumScore += $score;
		return $this;
	}
	/**
	* Removes score to the total sum
	*
	* @param int $score  Score to add
	*
	* @return self
	*/
	public function removeScore(int $score) {
		$this->sumScore -= $score;
		return $this;
	}
	/**
	* Adds points to the total sum
	*
	* @param int $points  Points to add
	*
	* @return self
	*/
	public function addPoints(int $points) {
		$this->sumPoints += $points;
		return $this;
	}
	/**
	* Removes points to the total sum
	*
	* @param int $points  Points to remove
	*
	* @return self
	*/
	public function removePoints(int $points) {
		$this->sumPoints -= $points;
		return $this;
	}
}
