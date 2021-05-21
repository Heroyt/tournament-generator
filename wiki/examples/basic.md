
## Creating a tournament

Start with instantiating a new **[Tournament](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Tournament.html)** class

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

Next, you create new **[Rounds](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Round.html)** for your tournaments.  
**[Rounds](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Round.html)** group multiple **[Group](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Group.html)** classes that will be played at the same time.

```php
// Create a round and a final round
$round = $tournament->round("First's round's name");
$final = $tournament->round("Final's round's name");
```

In rounds, you create new **[Groups](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Group.html)**.

```php
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
```
We set a **[Progression](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Progression.html)** conditions from first groups to our final group.  
This determines how many and which team

```php
// SET PROGRESSIONS FROM GROUP 1 AND 2 TO FINAL GROUP
$group_1->progression($final_group, 0, 2); // PROGRESS 2 BEST TEAMS
$group_2->progression($final_group, 0, 2); // PROGRESS 2 BEST TEAMS
```

---

## Adding teams

Now, you can add **[Teams](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Team.html)** to your tournament.

```php
// CREATE 6 TEAMS
for ($i=1; $i <= 6; $i++) {
	$tournament->team('Team '.$i);
}
```

---

## Generating games

At last, we split all teams randomly over our starting **[Round](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Round.html)** and generate **[Games](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Game.html)** in our first **[Round](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Round.html)**.

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
		(string|int) 'teamID' => (int) 25, // FIRST  TEAM SCORE
		(string|int) 'teamID' => (int) 50  // SECOND TEAM SCORE
	]
);
// Continue for all other games
```

or simulate it, if you want just the bracket with no real results.

```php
// Simulate results (or you can fill it with your own real results)
$round->simulate();
```

After all **[Games](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Game.html)** in a **[Round](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Round.html)** are played, you should progress it and all **[Teams](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Team.html)** from the first **[Round](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Round.html)** will be moved to the next **[Round](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Round.html)** specified before.

```php
// Progress best teams from first round to final round
$round->progress();
```

Now, it's all the same for the final **[Round](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Round.html)**.
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
		(string|int) 'teamID' => (int) 25, // FIRST  TEAM SCORE
		(string|int) 'teamID' => (int) 50  // SECOND TEAM SCORE
	]
);
// Continue for all other games
```
```php
// Simulate results (or you can fill it with your own real results)
$final->simulate();
```

You can also simulate a whole **[Tournament](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Tournament.html)** at once.

```php
// Simulate games
$tournament->genGamesSimulate(); // Simulate only games for example to only save bracket to DB
$tournament->genGamesSimulateReal(); // Simulate games with results like a real tournament
```

## Getting results

Finally, you can get all the **[Teams](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Team.html)** ordered by their results.

```php
// GET ALL TEAMS
$teams = $tournament->getTeams(true); // TRUE to get teams ordered by their results
```
