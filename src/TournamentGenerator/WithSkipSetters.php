<?php

namespace TournamentGenerator;

/**
 *
 */
interface WithSkipSetters
{
	public function allowSkip();
	public function disallowSkip();
	public function setSkip(bool $skip);
	public function getSkip();
}
