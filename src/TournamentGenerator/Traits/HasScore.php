<?php


namespace TournamentGenerator\Traits;

use TournamentGenerator\Base;

/**
 * Trait HasScore
 *
 * A trait for a Team object that adds the ability to calculate scores / points.
 *
 * @package TournamentGenerator\Traits
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
trait HasScore
{
	/** @var int $sumPoints Sum of all points acquired through the whole tournament */
	protected int $sumPoints = 0;
	/** @var int $sumScore Sum of all score acquired through the whole tournament */
	protected int $sumScore = 0;

	/**
	 * Gets all points that the team has acquired through the tournament
	 *
	 * @return int Sum of the points acquired
	 */
	public function getSumPoints() : int {
		return $this->sumPoints;
	}

	/**
	 * Gets all score that the team has acquired through the tournament
	 *
	 * @return int Sum of the score acquired
	 */
	public function getSumScore() : int {
		return $this->sumScore;
	}

	/**
	 * Calculate all the points acquired from given group ids
	 *
	 * @param array $groupIds Array of group ids
	 *
	 * @return int Sum of the points or sum of all points if argument is empty
	 */
	public function sumPoints(array $groupIds = []) : int {
		if (count($groupIds) === 0) {
			return $this->sumPoints;
		}
		$sum = 0;
		foreach ($groupIds as $gid) {
			$sum += $this->groupResults[$gid]['points'] ?? 0;
		}
		return $sum;
	}

	/**
	 * Calculate all score acquired from given group ids
	 *
	 * @param array $groupIds Array of group ids
	 *
	 * @return int Sum of score or sum of all score if argument is empty
	 */
	public function sumScore(array $groupIds = []) : int {
		if (count($groupIds) === 0) {
			return $this->sumScore;
		}
		$sum = 0;
		foreach ($groupIds as $gid) {
			$sum += $this->groupResults[$gid]['score'] ?? 0;
		}
		return $sum;
	}

	/**
	 * Adds score to the total sum
	 *
	 * @param int $score Score to add
	 *
	 * @return $this
	 */
	public function addScore(int $score) : Base {
		$this->sumScore += $score;
		return $this;
	}

	/**
	 * Removes score to the total sum
	 *
	 * @param int $score Score to add
	 *
	 * @return $this
	 */
	public function removeScore(int $score) : Base {
		$this->sumScore -= $score;
		return $this;
	}

	/**
	 * Adds points to the total sum
	 *
	 * @param int $points Points to add
	 *
	 * @return $this
	 */
	public function addPoints(int $points) : Base {
		$this->sumPoints += $points;
		return $this;
	}

	/**
	 * Removes points to the total sum
	 *
	 * @param int $points Points to remove
	 *
	 * @return $this
	 */
	public function removePoints(int $points) : Base {
		$this->sumPoints -= $points;
		return $this;
	}
}