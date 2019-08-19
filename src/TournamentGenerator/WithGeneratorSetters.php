<?php

namespace TournamentGenerator;

/**
 *
 */
interface WithGeneratorSetters
{
	public function setType(string $type = \TournamentGenerator\Constants::ROUND_ROBIN);
	public function getType();

	public function setMaxSize(int $size);
	public function getMaxSize();

	public function setInGame(int $inGame);
	public function getInGame();
}
