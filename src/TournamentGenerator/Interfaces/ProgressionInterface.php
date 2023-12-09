<?php

namespace TournamentGenerator\Interfaces;

use TournamentGenerator\Group;
use TournamentGenerator\TeamFilter;

interface ProgressionInterface
{

	public function __toString();

	/**
	 * @param TeamFilter[] $filters
	 *
	 * @return $this
	 */
	public function setFilters(array $filters): static;

	public function addFilter(TeamFilter ...$filters): static;

	public function progress(bool $blank = false): static;

	public function reset(): static;

	public function getTo(): Group;

	public function getStart(): int;

	public function getLen(): ?int;

	/**
	 * @return TeamFilter[]
	 */
	public function getFilters(): array;

	public function isProgressed(): bool;

	public function setPoints(?int $points): static;

	public function getPoints(): ?int;

	public function setProgressed(bool $progressed): void;

	public function getProgressedTeams(): array;

}