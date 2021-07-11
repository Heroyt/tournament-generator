<?php

namespace TournamentGenerator;

use Exception;
use TournamentGenerator\Containers\GameContainer;
use TournamentGenerator\Containers\HierarchyContainer;
use TournamentGenerator\Containers\TeamContainer;
use TournamentGenerator\Helpers\Functions;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithGroups;
use TournamentGenerator\Interfaces\WithSkipSetters;
use TournamentGenerator\Interfaces\WithTeams;
use TournamentGenerator\Traits\WithGames as WithGamesTrait;
use TournamentGenerator\Traits\WithGroups as WithGroupsTrait;
use TournamentGenerator\Traits\WithSkipSetters as WithSkipSettersTrait;
use TournamentGenerator\Traits\WithTeams as WithTeamsTrait;

/**
 * Tournament round
 *
 * Round is a container for tournament groups. Groups in a round are played at the same time.
 * This modifies the generation of games - generate all games for each group and play them in alternating order (game 1 from group 1, game 1 from group 2, game 2 from group 1, ...).
 *
 * @package TournamentGenerator
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.1
 */
class Round extends HierarchyBase implements WithSkipSetters, WithTeams, WithGroups, WithGames
{
	use WithTeamsTrait;
	use WithGroupsTrait;
	use WithSkipSettersTrait;
	use WithGamesTrait;

	/**
	 * Round constructor.
	 *
	 * @param string          $name Round name
	 * @param string|int|null $id   Round id - if omitted -> it is generated automatically as unique string
	 */
	public function __construct(string $name = '', $id = null) {
		$this->setName($name);
		/** @infection-ignore-all */
		$this->setId($id ?? uniqid('', false));
		$this->games = new GameContainer($this->id);
		$this->teams = new TeamContainer($this->id);
		$this->container = new HierarchyContainer($this->id);
	}

	/**
	 * Adds one or more group to round
	 *
	 * @param Group ...$groups
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function addGroup(Group ...$groups) : Round {
		foreach ($groups as $group) {
			$this->insertIntoContainer($group);
		}
		return $this;
	}

	/**
	 * Creates a new group and adds it to round
	 *
	 * @param string          $name Group name
	 * @param string|int|null $id   Group id - if omitted -> it is generated automatically as unique string
	 *
	 * @return Group New group
	 * @throws Exception
	 */
	public function group(string $name, $id = null) : Group {
		$g = new Group($name, $id);
		$this->insertIntoContainer($g->setSkip($this->allowSkip));
		return $g;
	}

	/**
	 * Get all group ids
	 *
	 * @return string[]|int[] Array of ids
	 */
	public function getGroupsIds() : array {
		$groups = $this->orderGroups();
		return array_map(static function($a) {
			return $a->getId();
		}, $groups);
	}

	/**
	 * Sort groups by their order
	 *
	 * @return Group[] Sorted groups
	 */
	public function orderGroups() : array {
		$groups = $this->getGroups();
		usort($groups, static function($a, $b) {
			return $a->getOrder() - $b->getOrder();
		});
		return $groups;
	}

	/**
	 * Generate all games
	 *
	 * @return array
	 * @throws Exception
	 */
	public function genGames() : array {
		foreach ($this->getGroups() as $group) {
			$group->genGames();
		}
		return $this->getGames();
	}

	/**
	 * Check if all games in this round has been played
	 *
	 * @return bool
	 */
	public function isPlayed() : bool {
		if (count($this->games) === 0) {
			return false;
		}
		foreach ($this->getGroups() as $group) {
			if (!$group->isPlayed()) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Split teams into its Groups
	 *
	 * @param Group[] $groups
	 *
	 * @return $this
	 * @throws Exception
	 * @noinspection CallableParameterUseCaseInTypeContextInspection
	 */
	public function splitTeams(Group ...$groups) : Round {
		if (count($groups) === 0) {
			$groups = $this->getGroups();
		}

		$teams = $this->getTeams(true, Constants::SEED);
		if ($this::isSeeded($teams)) {
			Functions::sortAlternate($teams);
		}
		else {
			shuffle($teams);
		}

		$split = ceil(count($teams) / count($groups));
		foreach ($groups as $where) {
			if (count($teams) > 0) {
				$where->addTeam(...array_splice($teams, 0, $split));
			}
		}
		return $this;
	}

	/**
	 * Progresses all teams from the round
	 *
	 * @param bool $blank If true -> creates dummy teams for (does not progress the real team objects) - used for simulation
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function progress(bool $blank = false) : Round {
		foreach ($this->getGroups() as $group) {
			$group->progress($blank);
		}
		return $this;
	}

	/**
	 * Simulate all games in this round as they would be played for real
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function simulate() : Round {
		Helpers\Simulator::simulateRound($this);
		return $this;
	}

	/**
	 * Reset all game results as if they were not played
	 *
	 * @post All games in this round are marked as "not played"
	 * @post All scores in this round are deleted
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function resetGames() : Round {
		foreach ($this->getGroups() as $group) {
			$group->resetGames();
		}
		return $this;
	}
}
