<?php


namespace TournamentGenerator\Traits;

use Exception;
use TournamentGenerator\Base;

/**
 * Trait Positions
 *
 * A trait for a Team class (maybe something else in the future) for logging positions (first, second,..).
 *
 * @package TournamentGenerator\Traits
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
trait HasPositions
{
	/**
	 * @var array[] Associative array of results in each group
	 * @details [
	 * * groupId => [
	 * * * "group"  => Group, # GROUP OBJECT
	 * * * "points" => int 0, # NUMBER OF POINTS ACQUIRED
	 * * * "score"  => int 0, # SUM OF SCORE ACQUIRED
	 * * * "wins"   => int 0, # NUMBER OF WINS
	 * * * "draws"  => int 0, # NUMBER OF DRAWS
	 * * * "losses" => int 0, # NUMBER OF LOSSES
	 * * * "second" => int 0, # NUMBER OF TIMES BEING SECOND (ONLY FOR INGAME OPTION OF 3 OR 4)
	 * * * "third"  => int 0  # NUMBER OF TIMES BEING THIRD  (ONLY FOR INGAME OPTION OF 4)
	 * * ]
	 * ]
	 */
	public array $groupResults = [];


	/**
	 * Adds a win to the team
	 *
	 * @param string|int|null $groupId An id of group to add the results from
	 *
	 * @return $this
	 * @throws Exception if the group results have not been added
	 *
	 * @uses Group::getWinPoints() to retrieve the points to add
	 *
	 */
	public function addWin(string $groupId = '') : Base {
		if (!isset($this->groupResults[$groupId])) {
			throw new Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		}
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->getWinPoints();
		$this->addPoints($this->groupResults[$groupId]['group']->getWinPoints());
		$this->groupResults[$groupId]['wins']++;
		return $this;
	}

	/**
	 * Remove a win from the team
	 *
	 * @param string|int|null $groupId An id of group to add the results from
	 *
	 * @return $this
	 * @throws Exception if the group results have not been added
	 *
	 * @uses Group::getWinPoints() to retrieve the points to add
	 *
	 */
	public function removeWin(string $groupId = '') : Base {
		if (!isset($this->groupResults[$groupId])) {
			throw new Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		}
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->getWinPoints();
		$this->removePoints($this->groupResults[$groupId]['group']->getWinPoints());
		$this->groupResults[$groupId]['wins']--;
		return $this;
	}

	/**
	 * Adds a draw to the team
	 *
	 * @param string|int|null $groupId An id of group to add the results from
	 *
	 * @return $this
	 * @throws Exception if the group results have not been added
	 *
	 * @uses Group::getDrawPoints() to retrieve the points to add
	 *
	 */
	public function addDraw(string $groupId = '') : Base {
		if (!isset($this->groupResults[$groupId])) {
			throw new Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		}
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->getDrawPoints();
		$this->addPoints($this->groupResults[$groupId]['group']->getDrawPoints());
		$this->groupResults[$groupId]['draws']++;
		return $this;
	}

	/**
	 * Remove a draw from the team
	 *
	 * @param string|int|null $groupId An id of group to add the results from
	 *
	 * @return $this
	 * @throws Exception if the group results have not been added
	 *
	 * @uses Group::getDrawPoints() to retrieve the points to add
	 *
	 */
	public function removeDraw(string $groupId = '') : Base {
		if (!isset($this->groupResults[$groupId])) {
			throw new Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		}
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->getDrawPoints();
		$this->removePoints($this->groupResults[$groupId]['group']->getDrawPoints());
		$this->groupResults[$groupId]['draws']--;
		return $this;
	}

	/**
	 * Adds a loss to the team
	 *
	 * @param string|int|null $groupId An id of group to add the results from
	 *
	 * @return $this
	 * @throws Exception if the group results have not been added
	 *
	 * @uses Group::getLossPoints() to retrieve the points to add
	 *
	 */
	public function addLoss(string $groupId = '') : Base {
		if (!isset($this->groupResults[$groupId])) {
			throw new Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		}
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->getLostPoints();
		$this->addPoints($this->groupResults[$groupId]['group']->getLostPoints());
		$this->groupResults[$groupId]['losses']++;
		return $this;
	}

	/**
	 * Remove a loss from the team
	 *
	 * @param string|int|null $groupId An id of group to add the results from
	 *
	 * @return $this
	 * @throws Exception if the group results have not been added
	 *
	 * @uses Group::getLossPoints() to retrieve the points to add
	 *
	 */
	public function removeLoss(string $groupId = '') : Base {
		if (!isset($this->groupResults[$groupId])) {
			throw new Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		}
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->getLostPoints();
		$this->removePoints($this->groupResults[$groupId]['group']->getLostPoints());
		$this->groupResults[$groupId]['losses']--;
		return $this;
	}

	/**
	 * Add points for being second to the team
	 *
	 * @param string|int|null $groupId An id of group to add the results from
	 *
	 * @return $this
	 * @throws Exception if the group results have not been added
	 *
	 * @uses Group::getSecondPoints() to retrieve the points to add
	 *
	 */
	public function addSecond(string $groupId = '') : Base {
		if (!isset($this->groupResults[$groupId])) {
			throw new Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		}
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->getSecondPoints();
		$this->addPoints($this->groupResults[$groupId]['group']->getSecondPoints());
		$this->groupResults[$groupId]['second']++;
		return $this;
	}

	/**
	 * Remove points for being second from the team
	 *
	 * @param string|int|null $groupId An id of group to add the results from
	 *
	 * @return $this
	 * @throws Exception if the group results have not been added
	 *
	 * @uses Group::getSecondPoints() to retrieve the points to add
	 *
	 */
	public function removeSecond(string $groupId = '') : Base {
		if (!isset($this->groupResults[$groupId])) {
			throw new Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		}
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->getSecondPoints();
		$this->removePoints($this->groupResults[$groupId]['group']->getSecondPoints());
		$this->groupResults[$groupId]['second']--;
		return $this;
	}

	/**
	 * Add points for being third to the team
	 *
	 * @param string|int|null $groupId An id of group to add the results from
	 *
	 * @return $this
	 * @throws Exception if the group results have not been added
	 *
	 * @uses Group::getThirdPoints() to retrieve the points to add
	 *
	 */
	public function addThird(string $groupId = '') : Base {
		if (!isset($this->groupResults[$groupId])) {
			throw new Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		}
		$this->groupResults[$groupId]['points'] += $this->groupResults[$groupId]['group']->getThirdPoints();
		$this->addPoints($this->groupResults[$groupId]['group']->getThirdPoints());
		$this->groupResults[$groupId]['third']++;
		return $this;
	}

	/**
	 * Remove points for being third from the team
	 *
	 * @param string|int|null $groupId An id of group to add the results from
	 *
	 * @return $this
	 * @throws Exception if the group results have not been added
	 *
	 * @uses Group::getThirdPoints() to retrieve the points to add
	 *
	 */
	public function removeThird(string $groupId = '') : Base {
		if (!isset($this->groupResults[$groupId])) {
			throw new Exception('Group '.$groupId.' is not set for this team ('.$this->name.')');
		}
		$this->groupResults[$groupId]['points'] -= $this->groupResults[$groupId]['group']->getThirdPoints();
		$this->removePoints($this->groupResults[$groupId]['group']->getThirdPoints());
		$this->groupResults[$groupId]['third']--;
		return $this;
	}

}