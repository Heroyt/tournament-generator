<?php

namespace TournamentGenerator;

define('DUMMY_TEAM', 'dummyRozlosTeam');
define('groupTypes', [
	'R_R' => 'Robin-Robin',
	'TWO_TWO' => 'team plays only once',
	'COND_SPLIT' => 'dependent on condition'
]);
define('orderingTypes', [
	'POINTS' => 'order based on points aqquired',
	'SCORE' => 'order based on total score'
]);
foreach (groupTypes as $key => $value) {
	define($key, $value);
}
foreach (orderingTypes as $key => $value) {
	define($key, $value);
}

function circle_genGames2(array $teams = [], Group $group = null) {
	$bracket = []; // ARRAY OF GAMES

	if (count($teams) % 2 != 0) $teams[] = DUMMY_TEAM; // IF NOT EVEN NUMBER OF TEAMS, ADD DUMMY

	shuffle($teams); // SHUFFLE TEAMS FOR MORE RANDOMNESS

	for ($i=0; $i < count($teams)-1; $i++) {
		$bracket = array_merge($bracket, circle_saveBracket($teams, $group)); // SAVE CURRENT ROUND

		$teams = circle_rotateBracket($teams); // ROTATE TEAMS IN BRACKET
	}

	return $bracket;

}

// CREATE GAMES FROM BRACKET
function circle_saveBracket(array $teams, Group $group) {

	$bracket = [];

	for ($i=0; $i < count($teams)/2; $i++) { // GO THROUGH HALF OF THE TEAMS

		$home = $teams[$i];
		$reverse = array_reverse($teams);
		$away = $reverse[$i];

		if (($home == DUMMY_TEAM || $away == DUMMY_TEAM)) continue; // SKIP WHEN DUMMY_TEAM IS PRESENT

		$bracket[] = new Game([$home, $away], $group);

	}

	return $bracket;

}

// ROTATE TEAMS IN BRACKET
function circle_rotateBracket(array $teams) {

	$temp1 = null;
	$temp2 = null;

	$first = array_shift($teams); // THE FIRST TEAM REMAINS FIRST
	$last = array_shift($teams); // THE SECOND TEAM MOVES TO LAST PLACE

	$teams = array_merge([$first], $teams, [$last]); // MERGE BACK TOGETHER

	return $teams;

}

// IF NUMBER IS POWER OF 2
function isPowerOf2(int $x) {
	return ($x !== 0) && ($x&($x-1)) === 0;
}

?>
