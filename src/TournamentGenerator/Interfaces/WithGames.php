<?php


namespace TournamentGenerator\Interfaces;


use TournamentGenerator\Game;

/**
 * Interface for objects that contain games
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator\Interfaces
 * @since   0.4
 */
interface WithGames
{
	/**
	 * Get all tournament games
	 *
	 * @return Game[]
	 */
	public function getGames() : array;
}