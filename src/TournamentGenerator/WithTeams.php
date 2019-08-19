<?php

namespace TournamentGenerator;

/**
 *
 */
interface WithTeams
{
	public function addTeam(Team ...$teams);
	public function team(string $name = '', $id = null);
	public function getTeams(bool $ordered = false, $ordering = \TournamentGenerator\Constants::POINTS, array $filters = []);
	public function sortTeams($ordering = \TournamentGenerator\Constants::POINTS, array $filters = []);
}
