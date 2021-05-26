<?php


namespace TournamentGenerator\Traits;


use Exception;
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
	 * @throws Exception
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
	 * @post    Propagates the value to all child hierarchy objects
	 *
	 * @return WithGamesInterface
	 * @see     GameContainer::setAutoIncrement()
	 *
	 * @since   0.5
	 */
	public function setGameAutoincrementId(int $id) : WithGamesInterface {
		$this->games->setAutoIncrement($id);
		return $this;
	}

	/**
	 * Set the game's results
	 *
	 * Results is an array of [teamId => teamScore] key-value pairs. This method will look for a game with given teams and try to set the first not played.
	 *
	 * @param int[] $results array of [teamId => teamScore] key-value pairs
	 *
	 * @return Game|null
	 * @throws Exception
	 */
	public function setResults(array $results) : ?Game {
		$ids = array_keys($results);
		/** @var Game|null $game */
		$game = $this->games->filter(static function(Game $game) use ($ids) {
			return !$game->isPlayed() && count(array_diff($ids, $game->getTeamsIds())) === 0;
		})->getFirst();
		if (isset($game)) {
			$game->setResults($results);
		}
		return $game;
	}
}