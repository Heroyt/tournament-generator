<?php


namespace TournamentGenerator\Interfaces;


use TournamentGenerator\Containers\GameContainer;
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

	/**
	 * Get the container for games
	 *
	 * @return GameContainer
	 */
	public function getGameContainer() : GameContainer;
	/**
	 * Add a child container for games
	 *
	 * @param GameContainer $container
	 *
	 * @return WithGames
	 */
	public function addGameContainer(GameContainer $container) : WithGames;
}