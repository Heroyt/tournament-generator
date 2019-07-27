<?php

namespace TournamentGenerator;

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

	function __construct(array $teams, Group $group) {
		$this->group = $group;
		$error = [];
		$tids = [];
		foreach ($teams as $key => $team) {
			if (!$team instanceof Team) {
				$error[] = $team;
				unset($teams[$key]);
				continue;
			}
			$team->addGame($this);
			$tids[] = $team->id;
		}
		$this->teams = $teams;
		foreach ($this->teams as $team) {
			foreach ($this->teams as $team2) {
				if ($team === $team2) continue;
				$team->addGameWith($team2, $group);
			}
		}
		if (count($error) > 0) throw new \Exception('Trying to add teams ('.count($error).') that are not instance of Team class'.PHP_EOL.print_r($error, true));
	}

	public function getGroup() {
		return $this->group;
	}

	public function addTeam(...$teams) {
		$error = [];
		foreach ($teams as $key => $team) {
			if (!$team instanceof Team) {
				$error[] = $team;
				unset($teams[$key]);
				continue;
			}
			$this->teams[] = $team;
			$team->addGame($this);

			foreach ($this->teams as $team2) {
				if ($team === $team2) continue;
				if ($team instanceof Team) {
					$team->addGameWith($team2, $this->group);
					$team2->addGameWith($team, $this->group);
				}
			}
		}
		if (count($error) > 0) throw new \Exception('Trying to add teams ('.count($error).') that are not instance of Team class'.PHP_EOL.print_r($error, true));
		return $this;
	}
	public function getTeams(){
		return $this->teams;
	}
	public function getTeamsIds(){
		return array_map(function($a){ return $a->id; }, $this->teams);
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
		$inGame = /** @scrutinizer ignore-call */ $this->group->getInGame();
		$i = 1;
		foreach ($results as $id => $score) {
			$team = $this->getTeam($id);
			if ($team === false) throw new \Exception('Couldn\'t find team with id of "'.$id.'"');
			$this->results[$team->id] = ['score' => $score];
			$team->sumScore += $score;
			$prev = prev($results);
			next($results);
			$next = next($results);
			switch ($inGame) {
				case 2:
					$this->setResults2($score, $prev, $next, $team);
					break;
				case 3:
					$this->setResults3($i, $team);
					break;
				case 4:
					$this->setResults4($i, $team);
					break;
			}
			$team->groupResults[$this->group->id]['score'] += $score;
			$i++;
		}
		return $this;
	}
	private function setResults2($score, $prev, $next, $team) {
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
		return $this;
	}
	private function setResults3($i, $team) {
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
		return $this;
	}
	private function setResults4($i, $team) {
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
		return $this;
	}

	public function resetResults() {
		foreach ($this->results as $teamId => $score) {
			$team = $this->getTeam($teamId);
			$team->groupResults[$this->group->id]['score'] -= $score['score'];
			$team->sumScore -= $score['score'];
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
	public function getThird() {
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
