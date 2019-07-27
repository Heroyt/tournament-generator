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
define('R_R', 'Robin-Robin');
define('TWO_TWO', 'team plays only once');
define('COND_SPLIT', 'dependent on condition');
define('POINTS', 'order based on points aqquired');
define('SCORE', 'order based on total score');

// IF NUMBER IS POWER OF 2
function isPowerOf2(int $x) {
	return ($x !== 0) && ($x&($x-1)) === 0;
}
