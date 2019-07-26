
## Creating a tournament

Start with instantiating a new **[Tournament](/classes/tournament.md)** class

```php
require 'vendor/autoload.php';

// Create a tournament
$tournament = new TournamentGenerator\Tournament('Tournament name');
```

You can also set play time, time between games and time between rounds to later calculate a timetable of the whole tournament

```php
// Set tournament lengths - could be omitted
$tournament
	->setPlay(7) // SET GAME TIME TO 7 MINUTES
	->setGameWait(2) // SET TIME BETWEEN GAMES TO 2 MINUTES
	->setRoundWait(0); // SET TIME BETWEEN ROUNDS TO 0 MINUTES
```

Next, you create new **[Rounds](/classes/round.php)** for your tournaments.  
**[Rounds](/classes/round.php)** group multiple **[Group](/classes/group.php)** classes that will be played at the same time.

```php
// Create a round and a final round
$round = $tournament->round("First's round's name");
$final = $tournament->round("Final's round's name");
```

In rounds, you create new **[Groups](/classes/group.php)**.

```php
// Create 2 groups for the first round
$group_1 = $round->group([
	'name' => 'Round 1',
	'inGame' => 2, // 2 TEAMS PLAYING AGAINST EACH OTHER
	'type' => R_R // ROBIN-ROBIN GROUP
]);
$group_2 = $round->group([
	'name' => 'Round 2',
	'inGame' => 2, // 2 TEAMS PLAYING AGAINST EACH OTHER
	'type' => R_R // ROBIN-ROBIN GROUP
]);

// Create a final group
$final_group = $final->group([
	'name' => 'Finale',
	'inGame' => 2, // 2 TEAMS PLAYING AGAINST EACH OTHER
	'type' => R_R // ROBIN-ROBIN GROUP
]);
```
We set a **[Progression](/classes/progression.php)** conditions from first groups to our final group.  
This determines how many and which team

```php
// SET PROGRESSIONS FROM GROUP 1 AND 2 TO FINAL GROUP
$group_1->progression($final_group, 0, 2); // PROGRESS 2 BEST TEAMS
$group_2->progression($final_group, 0, 2); // PROGRESS 2 BEST TEAMS
```

---

## Adding teams

Now, you can add **[Teams](/classes/team.php)** to your tournament.

```php
// CREATE 6 TEAMS
for ($i=1; $i <= 6; $i++) {
	$tournament->team('Team '.$i);
}
```

---

## Generating games

At last, we split all teams randomly over our starting **[Round](/classes/round.php)** and generate **[Games](/classes/game.php)** in our first **[Round](/classes/round.php)**.

```php
// Split all teams to Groups in the first round
$tournament->splitTeams($round);
// Generate games in the first round
$round->genGames();
```

---

## Generating results

Now, you can either fill in all your results

```php
// Get all games
$games = $round->getGames();

// Set game results
$games[0]->setResults(
	[
		mixed 'teamID' => int 25, // FIRST  TEAM SCORE
		mixed 'teamID' => int 50  // SECOND TEAM SCORE
	]
);
// Continue for all other games
```

or simulate it, if you want just the bracket with no real results.

```php
// Simulate results (or you can fill it with your own real results)
$round->simulate();
```

After all **[Games](/classes/game.php)** in a **[Round](/classes/round.php)** are played, you should progress it and all **[Teams](/classes/team.php)** from the first **[Round](/classes/round.php)** will be moved to the next **[Round](/classes/round.php)** specified before.

```php
// Progress best teams from first round to final round
$round->progress();
```

Now, it's all the same for the final **[Round](/classes/round.php)**.
```php
// Generate games in the final round
$final->genGames();
```
```php
// Get all games
$games = $round->getGames();

// Set game results
$games[0]->setResults(
	[
		mixed 'teamID' => int 25, // FIRST  TEAM SCORE
		mixed 'teamID' => int 50  // SECOND TEAM SCORE
	]
);
// Continue for all other games
```
```php
// Simulate results (or you can fill it with your own real results)
$final->simulate();
```

You can also simulate a whole **[Tournament](/classes/tournament.php)** at once.

```php
// Simulate games
$tournament->genGamesSimulate(); // Simulate only games for example to only save bracket to DB
$tournament->genGamesSimulateReal(); // Simulate games with results like a real tournament
```

## Getting results

Finally, you can get all the **[Teams](/classes/team.php)** ordered by their results.

```php
// GET ALL TEAMS
$teams = $tournament->getTeams(true); // TRUE to get teams ordered by their results
```
