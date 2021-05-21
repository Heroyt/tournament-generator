<?php

namespace TournamentGenerator\Interfaces;

use Exception;
use TournamentGenerator\Constants;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;

/**
 * Functions for objects that contain teams
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator\Interfaces
 * @since   0.4
 */
interface WithTeams
{
	/**
	 * Add one or more teams into the object.
	 *
	 * @param Team ...$teams Team objects
	 *
	 * @return $this
	 */
	public function addTeam(Team ...$teams) : WithTeams;

	/**
	 * Create a new team and add it into the object
	 *
	 * @param string $name Name of the new team
	 * @param null   $id   Id of the new team - if omitted -> it is generated automatically as unique string
	 *
	 * @return Team Newly created team
	 */
	public function team(string $name = '', $id = null) : Team;

	/**
	 * Get all teams in the object
	 *
	 * @param bool         $ordered  If true - order the teams by their score/points
	 * @param string|null  $ordering What to order the teams by - Constants::POINTS, Constants::SCORE
	 * @param TeamFilter[] $filters  Filters to filter the returned teams (ex. if you only want to get the first 3 teams)
	 *
	 * @return Team[]
	 */
	public function getTeams(bool $ordered = false, ?string $ordering = Constants::POINTS, array $filters = []) : array;

	/**
	 * Sort the teams by their score/points
	 *
	 * @param string|null  $ordering What to order the teams by - Constants::POINTS, Constants::SCORE
	 * @param TeamFilter[] $filters  Filters to filter the returned teams (ex. if you only want to get the first 3 teams)
	 *
	 * @return Team[]
	 */
	public function sortTeams(?string $ordering = Constants::POINTS, array $filters = []) : array;

	/**
	 * Filter teams using the specified filters
	 *
	 * @param array        $teams   Teams to filter through
	 * @param TeamFilter[] $filters Filters to use
	 *
	 * @return array
	 * @throws Exception
	 */
	public function filterTeams(array &$teams, array $filters) : array;
}
