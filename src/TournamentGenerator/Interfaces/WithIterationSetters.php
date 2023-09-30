<?php

namespace TournamentGenerator\Interfaces;

interface WithIterationSetters
{
	/**
	 * Set how many iterations should be generated = how many times should each team play every other team
	 *
	 * @param int $iterations
	 *
	 * @return $this
	 */
	public function setIterationCount(int $iterations): static;

	/**
	 * Set how many iterations should be generated = how many times should each team play every other team
	 *
	 * @return int
	 */
	public function getIterationCount(): int;
}