<?php

namespace TournamentGenerator\Interfaces;

use TournamentGenerator\Constants;

/**
 * Interface for objects that can generate games
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator\Interfaces
 * @since   0.4
 */
interface WithGeneratorSetters
{
	/**
	 * Sets a generator type
	 *
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setType(string $type = Constants::ROUND_ROBIN) : WithGeneratorSetters;

	/**
	 * Get generator type
	 *
	 * @return mixed
	 */
	public function getType() : string;

	/**
	 * Set max group size
	 *
	 * @param int $size
	 *
	 * @return $this
	 */
	public function setMaxSize(int $size) : WithGeneratorSetters;

	/**
	 * Get max group size
	 *
	 * @return int
	 */
	public function getMaxSize() : int;

	/**
	 * Set how many teams play in each game
	 *
	 * @param int $inGame 2/3/4
	 *
	 * @return $this
	 */
	public function setInGame(int $inGame) : WithGeneratorSetters;

	/**
	 * Get how many teams play in each game
	 *
	 * @return int
	 */
	public function getInGame() : int;
}
