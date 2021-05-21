<?php

namespace TournamentGenerator;

use Exception;
use TournamentGenerator\Interfaces\WithCategories;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithGroups;
use TournamentGenerator\Interfaces\WithRounds;
use TournamentGenerator\Interfaces\WithSkipSetters;
use TournamentGenerator\Interfaces\WithTeams;
use TournamentGenerator\Traits\WithCategories as WithCategoriesTrait;
use TournamentGenerator\Traits\WithGames as WithGamesTrait;
use TournamentGenerator\Traits\WithGroups as WithGroupsTrait;
use TournamentGenerator\Traits\WithRounds as WithRoundsTrait;
use TournamentGenerator\Traits\WithSkipSetters as WithSkipSettersTrait;
use TournamentGenerator\Traits\WithTeams as WithTeamsTrait;

/**
 * Tournament class
 *
 * Tournament is a main class. It is a container for every other object related to one tournament (categories -> rounds -> groups -> games -> teams).
 *
 * @package TournamentGenerator
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.1
 */
class Tournament extends Base implements WithSkipSetters, WithTeams, WithRounds, WithCategories, WithGroups, WithGames
{
	use WithTeamsTrait;
	use WithCategoriesTrait;
	use WithRoundsTrait;
	use WithGroupsTrait;
	use WithSkipSettersTrait;
	use WithGamesTrait;

	/** @var int Wait time between categories */
	protected int $expectedCategoryWait = 0;
	/** @var int Play time for one game */
	private int $expectedPlay = 0;
	/** @var int Wait time between games */
	private int $expectedGameWait = 0;
	/** @var int Wait time between rounds */
	private int $expectedRoundWait = 0;

	public function __construct(string $name = '') {
		$this->name = $name;
	}

	/**
	 * Set play time for one game
	 *
	 * @param int $play
	 *
	 * @return $this
	 */
	public function setPlay(int $play) : Tournament {
		$this->expectedPlay = $play;
		return $this;
	}

	/**
	 * Get play time for one game
	 *
	 * @return int
	 */
	public function getPlay() : int {
		return $this->expectedPlay;
	}

	/**
	 * Set wait time between games
	 *
	 * @param int $wait
	 *
	 * @return $this
	 */
	public function setGameWait(int $wait) : Tournament {
		$this->expectedGameWait = $wait;
		return $this;
	}

	/**
	 * Get wait time between games
	 *
	 * @return int
	 */
	public function getGameWait() : int {
		return $this->expectedGameWait;
	}

	/**
	 * Set wait time between rounds
	 *
	 * @param int $wait
	 *
	 * @return $this
	 */
	public function setRoundWait(int $wait) : Tournament {
		$this->expectedRoundWait = $wait;
		return $this;
	}

	/**
	 * Get wait time between rounds
	 *
	 * @return int
	 */
	public function getRoundWait() : int {
		return $this->expectedRoundWait;
	}

	/**
	 * Set the wait time between categories
	 *
	 * @param int $wait
	 *
	 * @return $this
	 */
	public function setCategoryWait(int $wait) : Tournament {
		$this->expectedCategoryWait = $wait;
		return $this;
	}

	/**
	 * Get the wait time between categories
	 *
	 * @return int
	 */
	public function getCategoryWait() : int {
		return $this->expectedCategoryWait;
	}

	/**
	 * Simulate all games as they would be played in reality - with dummy teams
	 *
	 * @param bool $returnTime If true - return the expected play time
	 *
	 * @return Game[]|int Generated games, or expected play time
	 * @throws Exception
	 */
	public function genGamesSimulate(bool $returnTime = false) {
		$games = Helpers\Simulator::simulateTournament($this);

		if ($returnTime) {
			return $this->getTournamentTime();
		}
		return $games;
	}

	/**
	 * Get the whole tournament time
	 *
	 * @return int
	 */
	public function getTournamentTime() : int {
		$games = count($this->getGames());
		return $games * $this->expectedPlay + ($games - 1) * $this->expectedGameWait + (count($this->getRounds()) - 1) * $this->expectedRoundWait + (count($this->getCategories()) - 1) * $this->expectedCategoryWait;
	}

	/**
	 * Simulate all games as they would be played in reality - with real teams
	 *
	 * @param bool $returnTime If true - return the expected play time
	 *
	 * @return int|Game[] Generated games, or expected play time
	 * @throws Exception
	 */
	public function genGamesSimulateReal(bool $returnTime = false) {
		$games = Helpers\Simulator::simulateTournamentReal($this);

		if ($returnTime) {
			return $this->getTournamentTime();
		}
		return $games;
	}

}
