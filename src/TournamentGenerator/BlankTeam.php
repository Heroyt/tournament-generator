<?php

namespace TournamentGenerator;

/**
 *
 */
class BlankTeam extends Team
{

	public function __construct(string $name = 'Blank team', Team $original) {
		$this->id = $original->id;
		$this->groupResults = $original->groupResults;
		$this->name = $name;
	}
}
