<?php

namespace TournamentGenerator\Traits;

trait WithIterations
{

	/** @var int How many times should each team play every other team */
	protected int $iterations = 1;

	/**
	 * Set how many iterations should be generated = how many times should each team play every other team
	 *
	 * @param int $iterations
	 *
	 * @return $this
	 */
	public function setIterationCount(int $iterations): static {
		$this->iterations = $iterations;
		return $this;
	}

	/**
	 * Set how many iterations should be generated = how many times should each team play every other team
	 *
	 * @return int
	 */
	public function getIterationCount(): int {
		return $this->iterations;
	}

}