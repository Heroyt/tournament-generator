<?php

namespace TournamentGenerator;

/**
 *
 */
class BlankTeam extends Team
{

	protected $from;
	protected $progression;

	public function __construct(string $name = 'Blank team', Team $original, Group $from, Progression $progression) {
		$this->id = $original->getId();
		$this->groupResults = $original->groupResults;
		$this->name = $name;
		$this->from = $from;
		$this->progression = $progression;
	}
}
