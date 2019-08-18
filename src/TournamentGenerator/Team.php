<?php

namespace TournamentGenerator;

/**
 *
 */
class Team
{

	private $name = 'team';
	private $id = '';
	public $games = [];
	public $gamesWith = [];
	public $sumPoints = 0;
	public $sumScore = 0;

	/**
	* ARRAY WITH GROUPS AND IT'S RESULTS
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

	function __construct(string $name = 'team', $id = null) {
		$this->setName($name);
		$this->setId(isset($id) ? $id : uniqid());
	}
	public function __toString() {
		return $this->name;
	}
	public function getGamesInfo($groupId) {
		return array_filter($this->groupResults[$groupId], function($k) { return $k !== 'group'; }, ARRAY_FILTER_USE_KEY);
	}

	public function setName(string $name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setId($id) {
		if (!is_string($id) && !is_int($id)) {
			$this->id = uniqid();
			throw new \Exception('Unsupported id type ('.gettype($id).') - expected type of string or int');
		}
		$this->id = $id;
	}
	public function getId() {
		return $this->id;
	}

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
	public function getGroupResults($groupId = null) {
		if (isset($groupId)) {
			if (!isset($this->groupResults[$groupId])) throw new \Exception('Trying to get unexisting group results ('.$groupId.')');
			return $this->groupResults[$groupId];
		}
		return $this->groupResults;
	}

	public function addGameWith(Team $team, Group $group) {
		if (!isset($this->gamesWith[$group->getId()][$team->getId()])) $this->gamesWith[$group->getId()][$team->getId()] = 0;
		$this->gamesWith[$group->getId()][$team->getId()]++;
		return $this;
	}
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
	public function addGroup(Group $group) {
		if (!isset($this->games[$group->getId()])) $this->games[$group->getId()] = [];
		return $this;
	}
	public function addGame(Game $game) {
		$group = $game->getGroup();
		if (!isset($this->games[$group->getId()])) $this->games[$group->getId()] = [];
		$this->games[$group->getId()][] = $game;
		return $this;
	}

	public function getSumPoints() {
		return $this->sumPoints;
	}
	public function getSumScore() {
		return $this->sumScore;
	}

	public function addWin(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->winPoints;
		$this->sumPoints += $this->groupResults[$groupId]['group']->winPoints;
		$this->groupResults[$groupId]['wins']++;
		return $this;
	}
	public function removeWin(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->winPoints;
		$this->sumPoints -= $this->groupResults[$groupId]['group']->winPoints;
		$this->groupResults[$groupId]['wins']--;
		return $this;
	}

	public function addDraw(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->drawPoints;
		$this->sumPoints += $this->groupResults[$groupId]['group']->drawPoints;
		$this->groupResults[$groupId]['draws']++;
		return $this;
	}
	public function removeDraw(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->drawPoints;
		$this->sumPoints -= $this->groupResults[$groupId]['group']->drawPoints;
		$this->groupResults[$groupId]['draws']--;
		return $this;
	}

	public function addLoss(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->lostPoints;
		$this->sumPoints += $this->groupResults[$groupId]['group']->lostPoints;
		$this->groupResults[$groupId]['losses']++;
		return $this;
	}
	public function removeLoss(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->lostPoints;
		$this->sumPoints -= $this->groupResults[$groupId]['group']->lostPoints;
		$this->groupResults[$groupId]['losses']--;
		return $this;
	}

	public function addSecond(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->secondPoints;
		$this->sumPoints += $this->groupResults[$groupId]['group']->secondPoints;
		$this->groupResults[$groupId]['second']++;
		return $this;
	}
	public function removeSecond(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->secondPoints;
		$this->sumPoints -= $this->groupResults[$groupId]['group']->secondPoints;
		$this->groupResults[$groupId]['second']--;
		return $this;
	}

	public function addThird(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->thirdPoints;
		$this->sumPoints += $this->groupResults[$groupId]['group']->thirdPoints;
		$this->groupResults[$groupId]['third']++;
		return $this;
	}
	public function removeThird(string $groupId = ''){
		if (!isset($this->groupResults[$groupId])) throw new \Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->thirdPoints;
		$this->sumPoints -= $this->groupResults[$groupId]['group']->thirdPoints;
		$this->groupResults[$groupId]['third']--;
		return $this;
	}

	public function sumPoints(array $groupIds = []) {
		if (count($groupIds) === 0) return $this->sumPoints;
		$sum = 0;
		foreach ($groupIds as $gid) {
			$sum += $this->groupResults[$gid]['points'] ?? 0;
		}
		return $sum;
	}
	public function sumScore(array $groupIds = []) {
		if (count($groupIds) === 0) return $this->sumScore;
		$sum = 0;
		foreach ($groupIds as $gid) {
			$sum += $this->groupResults[$gid]['score'] ?? 0;
		}
		return $sum;
	}

	public function addScore(int $score) {
		$this->sumScore += $score;
	}
	public function removeScore(int $score) {
		$this->sumScore -= $score;
	}
}
