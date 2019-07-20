<?php

/**
 *
 */
class BlankTeam extends Team
{

	function __construct(string $name = 'Blank team', Team $original) {
		$this->id = $original->id;
		$this->groupResults = $original->groupResults;
		$this->name = $name;
	}
}

?>
