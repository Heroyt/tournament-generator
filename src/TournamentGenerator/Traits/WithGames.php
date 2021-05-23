<?php


namespace TournamentGenerator\Traits;


use TournamentGenerator\Containers\GameContainer;
use TournamentGenerator\Game;
use TournamentGenerator\Interfaces\WithGames as WithGamesInterface;

/**
 * Trait WithGames
 *
 * @package TournamentGenerator\Traits
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
trait WithGames
{
	/** @var GameContainer List of games */
	protected GameContainer $games;

	/**
	 * Add a child container for games
	 *
	 * @param GameContainer $container
	 *
	 * @return WithGamesInterface
	 * @throws \Exception
	 */
	public function addGameContainer(GameContainer $container) : WithGamesInterface {
		$this->games->addChild($container);
		return $this;
	}

	/**
	 * Get all tournament games
	 *
	 * @return Game[]
	 */
	public function getGames() : array {
		return $this->games->get();
	}

	/**
	 * Get the container for games
	 *
	 * @return GameContainer
	 */
	public function getGameContainer() : GameContainer {
		return $this->games;
	}

	/**
	 * Sets a new autoincrement value (start) for the generated games
	 *
	 * @param int $id Id - probably from the database
	 *
	 * @warning Do this on the top-level hierarchy element (Tournament class) or else, it might be reset later
	 *
	 * @post Propagates the value to all child hierarchy objects
	 *
	 * @see GameContainer::setAutoIncrement()
	 *
	 * @return WithGamesInterface
	 * @since 0.5
	 */
	public function setGameAutoincrementId(int $id) : WithGamesInterface {
		$this->games->setAutoIncrement($id);
		return $this;
	}
}