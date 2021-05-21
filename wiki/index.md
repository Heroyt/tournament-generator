<h1 align="center">
<br>
Tournament Generator
<br>
</h1>

<h4 align="center">A set of multiple classes to generate and work with all different kinds of tournament brackets or defining a custom bracket.</h4>


<p align="center">
<a href="https://packagist.org/packages/heroyt/tournament-generator"><img src="https://poser.pugx.org/heroyt/tournament-generator/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/heroyt/tournament-generator"><img src="https://poser.pugx.org/heroyt/tournament-generator/downloads" alt="Total Downloads"></a>
<a href="https://scrutinizer-ci.com/g/heroyt/tournament-generator/?branch=master"><img src="https://scrutinizer-ci.com/g/heroyt/tournament-generator/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality" data-canonical-src="https://scrutinizer-ci.com/g/heroyt/tournament-generator/badges/quality-score.png?b=master" style="max-width:100%;"></a>
<a href="(https://scrutinizer-ci.com/g/Heroyt/tournament-generator/?branch=master"><img src="https://scrutinizer-ci.com/g/Heroyt/tournament-generator/badges/coverage.png?b=master" alt="Code Coverage" data-canonical-src="https://scrutinizer-ci.com/g/heroyt/tournament-generator/badges/quality-score.png?b=master" style="max-width:100%;"></a>
<a href="https://scrutinizer-ci.com/g/Heroyt/tournament-generator/build-status/master"><img src="https://scrutinizer-ci.com/g/Heroyt/tournament-generator/badges/build.png?b=master" alt="Scrutinizer Build" data-canonical-src="https://scrutinizer-ci.com/g/Heroyt/tournament-generator/badges/build.png?b=master" style="max-width:100%;"></a>

</p>

## [API documentation](https://heroyt.github.io/tournament-generator/)

## Features

- Creating a custom tournament bracket with any number of categories, rounds, groups and teams
- Defining a multiple different conditions
- Easily generating Robin-Robin tournaments
- Generating a tournament using a predefined preset (single elimination, double elimination, 2R2G) with any number of teams
- Generating brackets with 2 to 4 teams in one game against each other
- Filling your bracket with results and getting teams table with scores

## Installation

```shell
$ composer require heroyt/tournament-generator
```

## Basic Usage
```php
require 'vendor/autoload.php';

// Create a tournament
$tournament = new TournamentGenerator\Tournament('Tournament name');

// Set tournament lengths - could be omitted
$tournament
	->setPlay(7) // SET GAME TIME TO 7 MINUTES
	->setGameWait(2) // SET TIME BETWEEN GAMES TO 2 MINUTES
	->setRoundWait(0); // SET TIME BETWEEN ROUNDS TO 0 MINUTES

// Create a round and a final round
$round = $tournament->round("First's round's name");
$final = $tournament->round("Final's round's name");

// Create 2 groups for the first round
$group_1 = $round->group('Round 1')
	->setInGame(2) // 2 TEAMS PLAYING AGAINST EACH OTHER
	->setType(TournamentGenerator\Constants::ROUND_ROBIN); // ROBIN-ROBIN GROUP
$group_2 = $round->group('Round 2')
	->setInGame(2) // 2 TEAMS PLAYING AGAINST EACH OTHER
	->setType(TournamentGenerator\Constants::ROUND_ROBIN); // ROBIN-ROBIN GROUP

// Create a final group
$final_group = $final->group('Finale')
	->setInGame(2) // 2 TEAMS PLAYING AGAINST EACH OTHER
	->setType(TournamentGenerator\Constants::ROUND_ROBIN); // ROBIN-ROBIN GROUP
// CREATE 6 TEAMS
for ($i=1; $i <= 6; $i++) {
	$tournament->team('Team '.$i);
}

// SET PROGRESSIONS FROM GROUP 1 AND 2 TO FINAL GROUP
$group_1->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS
$group_2->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS

// Generate games in the first round
$round->genGames();
// Simulate results (or you can fill it with your own real results)
$round->simulate();
// Progress best teams from first round to final round
$round->progress();
// Generate games in the final round
$final->genGames();
// Simulate results (or you can fill it with your own real results)
$final->simulate();

// GET ALL TEAMS
$teams = $tournament->getTeams(true); // TRUE to get teams ordered by their results
```

### Creating a tournament from a template

```php
require 'vendor/autoload.php';

// Create a tournament
$tournament = new TournamentGenerator\Preset\SingleElimination('Tournament name');

// Set tournament lengths - could be omitted
$tournament
	->setPlay(7) // SET GAME TIME TO 7 MINUTES
	->setGameWait(2) // SET TIME BETWEEN GAMES TO 2 MINUTES
	->setRoundWait(0); // SET TIME BETWEEN ROUNDS TO 0 MINUTES

// CREATE 6 TEAMS
for ($i=1; $i <= 6; $i++) {
	$tournament->team('Team '.$i);
}

// GENERATE ALL GAMES
$tournament->generate();

// Simulate games
$tournament->genGamesSimulate(); // Simulate only games for example to only save bracket to DB
$tournament->genGamesSimulateReal(); // Simulate games with results like a real tournament

// GET ALL TEAMS
$teams = $tournament->getTeams(true); // TRUE to get teams ordered by their results
```
