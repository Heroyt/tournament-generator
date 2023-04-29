<?php

namespace TournamentGenerator;

use Exception;
use TournamentGenerator\Containers\GameContainer;
use TournamentGenerator\Containers\HierarchyContainer;
use TournamentGenerator\Containers\TeamContainer;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithGroups;
use TournamentGenerator\Interfaces\WithRounds;
use TournamentGenerator\Interfaces\WithSkipSetters;
use TournamentGenerator\Interfaces\WithTeams;
use TournamentGenerator\Traits\WithGames as WithGamesTrait;
use TournamentGenerator\Traits\WithGroups as WithGroupsTrait;
use TournamentGenerator\Traits\WithRounds as WithRoundsTrait;
use TournamentGenerator\Traits\WithSkipSetters as WithSkipSettersTrait;
use TournamentGenerator\Traits\WithTeams as WithTeamsTrait;


/**
 * Tournament category
 *
 * Category is an optional container for rounds in a tournament. It separates a tournament into bigger chunks that are played separately one after another.
 * This allows "sub-tournaments" in one tournament with the possibility to progress teams from one category to the next one.
 *
 * @package TournamentGenerator
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator
 * @since   0.1
 */
class Category extends HierarchyBase implements WithSkipSetters, WithRounds, WithTeams, WithGroups, WithGames
{
	use WithTeamsTrait;
	use WithRoundsTrait;
	use WithGroupsTrait;
	use WithSkipSettersTrait;
	use WithGamesTrait;


	/**
     * Category constructor.
     *
     * @param string $name Category name
     * @param int|string|null $id Category id - if omitted -> it is generated automatically as unique string
     */
    public function __construct(string $name = '', int|string|null $id = null) {
        $this->setName($name);
        /** @infection-ignore-all */
        $this->setId($id ?? uniqid('', false));
        $this->games = new GameContainer($this->id);
        $this->teams = new TeamContainer($this->id);
        $this->container = new HierarchyContainer($this->id);
    }

	/**
	 * Allows skipping of games with less than the minimum amounts of games
	 *
	 * @return $this
	 */
	public function allowSkip() : Category {
		$this->allowSkip = true;
		return $this;
	}

	/**
	 * Simulate all games as they would be played in reality - with dummy teams
	 *
	 * @return Game[] Generated games, or expected play time
	 * @throws Exception
	 */
	public function genGamesSimulate() : array {
		return Helpers\Simulator::simulateCategory($this);
	}

	/**
	 * Simulate all games as they would be played in reality - with real teams
	 *
	 * @return Game[] Generated games, or expected play time
	 * @throws Exception
	 */
	public function genGamesSimulateReal() : array {
		return Helpers\Simulator::simulateCategoryReal($this);
	}

	/**
	 * @inheritDoc
	 * @return array
	 * @throws Exception
	 */
	public function jsonSerialize() : array {
		return [
			'id'     => $this->getId(),
			'name'   => $this->getName(),
			'games'  => $this->getGames(),
			'teams'  => $this->teams->ids(),
			'rounds' => $this->queryRounds()->ids(),
			'groups' => $this->queryGroups()->ids(),
		];
	}

}
