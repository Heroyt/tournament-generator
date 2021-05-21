<?php

namespace TournamentGenerator\Interfaces;

/**
 * Interface that allows for setting skipping of not-playable games
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator\Interfaces
 * @since   0.4
 */
interface WithSkipSetters
{
	/**
	 * Allows round skipping
	 *
	 * @return $this
	 */
	public function allowSkip() : WithSkipSetters;

	/**
	 * Disallow round skipping
	 *
	 * @return $this
	 */
	public function disallowSkip() : WithSkipSetters;

	/**
	 * Set round skipping
	 *
	 * @param bool $skip
	 *
	 * @return $this
	 */
	public function setSkip(bool $skip) : WithSkipSetters;

	/**
	 * Getter for round skipping
	 *
	 * @return bool
	 */
	public function getSkip() : bool;
}
