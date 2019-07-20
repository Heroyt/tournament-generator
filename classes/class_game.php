<?php

/**
 *
 */
class Game
{

	private $teams = [];
	private $results = [];
	private $group = null;
	private $winId = null;
	private $lossId = null;
	private $secondId = null;
	private $thirdId = null;
	private $drawIds = [];

	function __construct(array $teams = [], Group $group = null) {
		$this->group = $group;
		$error = [];
		$tids = [];
		foreach ($teams as $key => $team) {
			if (!$team instanceof Team) {
				$error[] = $team;
				unset($teams[$key]);
			}
			else {
				if (!isset($team->games[$group->id])) $team->games[$group->id] = [];
				$team->games[$group->id][] = $this;
				$tids[] = $team->id;
			}
		}
		$this->teams = $teams;
		foreach ($this->teams as $team) {
			foreach ($tids as $id) {
				if ($team->id !== $id) {
					if (!isset($team->gamesWith[$group->id][$id])) $team->gamesWith[$group->id][$id] = 0;
					$team->gamesWith[$group->id][$id]++;
				}
			}
		}
		if (count($error) > 0) throw new Exception('Trying to add teams ('.count($error).') that are not instance of Team class'.PHP_EOL.print_r($error, true));
	}

	public function addTeam(...$teams) {
		$error = [];
		foreach ($this->teams as $team) {
			foreach ($teams as $team2) {
				if ($team2 instanceof Team) {
					if (!isset($team->gamesWith[$this->group->id][$team2->id])) $team->gamesWith[$this->group->id][$team2->id] = 0;
					$team->gamesWith[$this->group->id][$team2->id]++;
					if (!isset($team2->gamesWith[$this->group->id][$team->id])) $team2->gamesWith[$this->group->id][$team->id] = 0;
					$team2->gamesWith[$this->group->id][$team->id]++;
				}
			}
		}
		foreach ($teams as $key => $team) {
			if ($team instanceof Team) {
				$this->teams[] = $team;
				if (!isset($team->games[$this->group->id])) $team->games[$this->group->id] = [];
				$team->games[$this->group->id][] = $this;
				foreach ($teams as $key2 => $team2) {
					if ($team2 instanceof Team) {
						if (!isset($team->gamesWith[$this->group->id][$team2->id])) $team->gamesWith[$this->group->id][$team2->id] = 0;
						$team->gamesWith[$this->group->id][$team2->id]++;
					}
				}
			}
			else {
				$error[] = $team;
				unset($teams[$key]);
			}
		}
		if (count($error) > 0) throw new Exception('Trying to add teams ('.count($error).') that are not instance of Team class'.PHP_EOL.print_r($error, true));
		return $this;
	}
	public function getTeams(){
		return $this->teams;
	}
	public function getTeamsIds(){
		$ids = [];
		foreach ($this->teams as $team) {
			$ids[] = $team->id;
		}
		return $ids;
	}
	public function getTeam(string $id) {
		foreach ($this->teams as $team) {
			if ($team->id === $id) return $team;
		}
		return false;
	}

	/**
	* $results = array (
	* * team->id => team->score
	* )
	*/
	public function setResults(array $results = []) {
		if (count($this->results) === 0) $this->resetResults();
		arsort($results);
		$i = 1;
		foreach ($results as $id => $score) {
			$team = $this->getTeam($id);
			if ($team === false) throw new Exception('Couldn\'t find team with id of "'.$id.'"');
			$this->results[$team->id] = ['score' => $score];
			$prev = prev($results);
			next($results);
			$next = next($results);
			switch ($this->group->getInGame()) {
				case 2:{
					if ($score === $prev || $score === $next) {
						$this->drawIds[] = $team->id;
						$team->addDraw($this->group->id);
						$this->results[$team->id] += ['points' => $this->group->drawPoints, 'type' => 'draw'];
					}
					elseif ($i === 1) {
						$this->winId = $team->id;
						$team->addWin($this->group->id);
						$this->results[$team->id] += ['points' => $this->group->winPoints, 'type' => 'win'];
					}
					else {
						$this->lossId = $team->id;
						$team->addLoss($this->group->id);
						$this->results[$team->id] += ['points' => $this->group->lostPoints, 'type' => 'loss'];
					}
					break;}
				case 3:{
					switch ($i) {
						case 1:
							$this->winId = $team->id;
							$team->addWin($this->group->id);
							$this->results[$team->id] += ['points' => $this->group->winPoints, 'type' => 'win'];
							break;
						case 2:
							$this->secondId = $team->id;
							$team->addSecond($this->group->id);
							$this->results[$team->id] += ['points' => $this->group->secondPoints, 'type' => 'second'];
							break;
						case 3:
							$this->lossId = $team->id;
							$team->addLoss($this->group->id);
							$this->results[$team->id] += ['points' => $this->group->lostPoints, 'type' => 'loss'];
							break;
					}
					break;}
				case 4:{
					switch ($i) {
						case 1:
							$this->winId = $team->id;
							$team->addWin($this->group->id);
							$this->results[$team->id] += ['points' => $this->group->winPoints, 'type' => 'win'];
							break;
						case 2:
							$this->secondId = $team->id;
							$team->addSecond($this->group->id);
							$this->results[$team->id] += ['points' => $this->group->secondPoints, 'type' => 'second'];
							break;
						case 3:
							$this->thirdId = $team->id;
							$team->addThird($this->group->id);
							$this->results[$team->id] += ['points' => $this->group->thirdPoints, 'type' => 'third'];
							break;
						case 4:
							$this->lossId = $team->id;
							$team->addLoss($this->group->id);
							$this->results[$team->id] += ['points' => $this->group->lostPoints, 'type' => 'loss'];
							break;
					}
					break;}
			}
			$team->groupResults[$this->group->id]['score'] += $score;
			$i++;
		}
		return $this;
	}
	public function resetResults() {
		foreach ($this->results as $teamId => $score) {
			$team = $this->getTeam($teamId);
			$team->groupResults[$this->group->id]['score'] -= $score['score'];
			switch ($score['type']) {
				case 'win':
					$team->removeWin($this->group->id);
					break;
				case 'draw':
					$team->removeDraw($this->group->id);
					break;
				case 'loss':
					$team->removeLoss($this->group->id);
					break;
				case 'second':
					$team->removeSecond($this->group->id);
					break;
				case 'third':
					$team->removeThird($this->group->id);
					break;
			}
		}
		$this->results = [];
		return $this;
	}
	public function getWin() {
		return $this->winId;
	}
	public function getLoss() {
		return $this->lossId;
	}
	public function getSecond() {
		return $this->secondId;
	}
	public function getThrird() {
		return $this->thirdId;
	}
	public function getDraw() {
		return $this->drawIds;
	}

	public function isPlayed() {
		if (count($this->results) > 0) return true;
		return false;
	}
}

?>
