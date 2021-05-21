<?php

namespace TournamentGenerator;

use Exception;
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
class Round extends Base implements WithSkipSetters, WithTeams, WithGroups, WithGames
{
	use WithTeamsTrait;
	use WithGroupsTrait;
	use WithSkipSettersTrait;
	use WithGamesTrait;

	/**
	 * Round constructor.
	 *
	 * @param string $name Round name
	 * @param null   $id   Round id - if omitted -> it is generated automatically as unique string
	 */
	public function __construct(string $name = '', $id = null) {
		$this->setName($name);
		$this->setId($id ?? uniqid('', false));
	}

	/**
	 * Adds one or more group to round
	 *
	 * @param Group ...$groups
	 *
	 * @return $this
	 */
	public function addGroup(Group ...$groups) : Round {
		foreach ($groups as $group) {
			$this->groups[] = $group;
		}
		return $this;
	}

	/**
	 * Creates a new group and adds it to round
	 *
	 * @param string $name Group name
	 * @param null   $id   Group id - if omitted -> it is generated automatically as unique string
	 *
	 * @return Group New group
	 */
	public function group(string $name, $id = null) : Group {
		$g = new Group($name, $id);
		$this->groups[] = $g->setSkip($this->allowSkip);
		return $g;
	}

	/**
	 * Get all group ids
	 *
	 * @return string[]|int[] Array of ids
	 */
	public function getGroupsIds() : array {
		$this->orderGroups();
		return array_map(static function($a) {
			return $a->getId();
		}, $this->groups);
	}

	/**
	 * Sort groups by their order
	 *
	 * @return Group[] Sorted groups
	 */
	public function orderGroups() : array {
		usort($this->groups, static function($a, $b) {
			return $a->getOrder() - $b->getOrder();
		});
		return $this->groups;
	}

	/**
	 * Generate all games
	 *
	 * @return array
	 */
	public function genGames() : array {
		$games = [];
		foreach ($this->groups as $group) {
			$group->genGames();
			$games[] = $group->orderGames();
		}
		$this->games = array_merge(...$games);
		return $this->games;
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
		foreach ($this->groups as $group) {
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

		$teams = $this->getTeams();
		shuffle($teams);

		while (count($teams) > 0) {
			foreach ($groups as $group) {
				if (count($teams) > 0) {
					$group->addTeam(array_shift($teams));
				}
			}
		}
		return $this;
	}

	/**
	 * Get all groups in this round
	 *
	 * @return Group[]
	 */
	public function getGroups() : array {
		$this->orderGroups();
		return $this->groups;
	}

	/**
	 * Progresses all teams from the round
	 *
	 * @param bool $blank If true -> creates dummy teams for (does not progress the real team objects) - used for simulation
	 *
	 * @return $this
	 */
	public function progress(bool $blank = false) : Round {
		foreach ($this->groups as $group) {
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
	 */
	public function resetGames() : Round {
		foreach ($this->groups as $group) {
			$group->resetGames();
		}
		return $this;
	}
}
