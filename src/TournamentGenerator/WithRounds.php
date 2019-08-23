<?php

namespace TournamentGenerator;

/**
 *
 */
interface WithRounds
{
	public function addRound(Round ...$rounds);
	public function round(string $name = '');
	public function getRounds();
	public function getGroups();
}
