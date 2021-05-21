<?php


namespace TournamentGenerator\Traits;


use TournamentGenerator\Game;

/**
 * Trait WithGames
 *
 * @package TournamentGenerator\Traits
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
trait WithGames
{
	/** @var Game[] List of games */
	protected array $games = [];

	/**
	 * Get all tournament games
	 *
	 * @return Game[]
	 */
	public function getGames() : array {
		if ($this instanceof \TournamentGenerator\Interfaces\WithRounds) {
			$games = [];
			foreach ($this->getRounds() as $round) {
				$games[] = $round->getGames();
			}
			return array_merge(...$games);
		}
		return $this->games;
	}
}