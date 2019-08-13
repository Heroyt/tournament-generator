<?php

namespace TournamentGenerator;

// IF NUMBER IS POWER OF 2
function isPowerOf2(int $x) {
	return ($x !== 0) && ($x&($x-1)) === 0;
}
