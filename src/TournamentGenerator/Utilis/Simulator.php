<?php

namespace TournamentGenerator\Utilis;

/**
 *
 */
class Simulator
{

	public static function simulateGroup(\TournamentGenerator\Group $group, array $filters = [], bool $reset = true) {
		foreach ($group->getGames() as $game) {
			$teams = $game->getTeams();
			$results = [];
			foreach ($teams as $team) {
				$results[$team->getId()] = floor(rand(0, 500));
			}
			$game->setResults($results);
		}
		$return = $group->sortTeams(null, $filters);
		if (!$reset) return $return;
		foreach ($group->getGames() as $game) {
			$game->resetResults();
		}
		return $return;
	}

	public static function simulateRound(\TournamentGenerator\Round $round) {
		foreach ($round->getGroups() as $group) {
			if ($group->isPlayed()) continue;
			$group->simulate([], false);
		}
		return true;
	}

	public static function simulateTournament(\TournamentGenerator\Tournament $tournament) {
		if (count($tournament->getCategories()) === 0 && count($tournament->getRounds()) === 0) throw new \Exception('There are no rounds or categories to simulate games from.');

		$games = [];

		foreach ($tournament->getRounds() as $round) {
			$games = array_merge($games, $round->genGames());
			$round->simulate()->progress(true);
		}
		foreach ($tournament->getRounds() as $round) {
			$round->resetGames();
		}

		return $games;
	}

	public static function simulateTournamentReal(\TournamentGenerator\Tournament $tournament) {
		$games = [];
		if (count($tournament->getCategories()) === 0 && count($tournament->getRounds()) === 0) throw new \Exception('There are no rounds or categories to simulate games from.');

		foreach ($tournament->getRounds() as $round) {
			$games = array_merge($games, $round->genGames());
			$round->simulate();
			$round->progress();
		}
		return $games;
	}

	public static function simulateCategory(\TournamentGenerator\Category $category) {
		if (count($category->getRounds()) === 0) throw new \Exception('There are no rounds to simulate games from.');

		$games = [];

		foreach ($category->getRounds() as $round) {
			$games = array_merge($games, $round->genGames());
			$round->simulate()->progress(true);
		}
		foreach ($category->getRounds() as $round) {
			$round->resetGames();
		}

		return $games;
	}

	public static function simulateCategoryReal(\TournamentGenerator\Category $category) {
		$games = [];
		if (count($category->getRounds()) === 0) throw new \Exception('There are no rounds to simulate games from.');

		foreach ($category->getRounds() as $round) {
			$games = array_merge($games, $round->genGames());
			$round->simulate();
			$round->progress();
		}
		return $games;
	}

}
