## Introduction

**[Team](/classes/team/)** is a class of a team which holds its scores from the whole tournament.

---

### Creating a new team class

#### Basic construction

```php
include_once 'vendor/autoload.php';

$tournament = new TournamentGenerator\Tournament('Tournament name');

$team = new TournamentGenerator\Team('Team name');

$tournament->addTeam($team);
```

#### From a tournament / category / round / group class

**Recomended**  
This automatically assigns the team to the tournament / category / round / group.

```php
include_once 'vendor/autoload.php';

$round = new TournamentGenerator\Tournament('Tournament name');

$team = $round->team('Team name');
```

Either a tournament, category, round or group could be used (any class that implements [WithTeams](/interface/WithTeams) interface).

---

## Properties

| Scope | Name | Type | Default | Description |
| :---: | :--: | :--: | :-----: | :---------: |
| protected | `$name` | `string` | `''` | Name of the team |
| protected | `$id` | `string|int` | `null` | Id of the team |
| protected | `$games` | `array` | `[]` | A list of games played by this team |
| protected | [`$gamesWith`](#gamesWith) | `array` | `[]` | Multi-dimensional associative array of number of games together with other teams |
| protected | `$sumPoints` | `int` | `0` | Sum of all points acquired through the whole tournament |
| protected | `$sumScore` | `int` | `0` | Sum of all score acquired through the whole tournament |
| public | [`$groupResults`](#groupResults) | `array` | `[]` | Associative array of results in each group |

<a id="gamesWith" name="gamesWith"></a>
### $gamesWith

Multi-dimensional associative array of number of games together with other teams.

```php
array(
	groupId => array(
		teamId => int gamesWith, // Number of games together in one group
		...
	),
	...
)
```

<a id="groupResults" name="groupResults"></a>
### $groupResults

Associative array of results in each group.

#### Parameters

- **group** &rarr; Group object
- **points** &rarr; Points acquired from this group
- **score** &rarr; Score acquired from this group
- **wins** &rarr; Number of wins from this group
- **draws** &rarr; Number of draws from this group
- **losses** &rarr; Number of losses from this group
- **second** &rarr; Number of being second from this group
- **third** &rarr; Number of being third from this group

```php
array (
	groupId => array (
		"group"  => Group, # GROUP OBJECT
		"points" => int 0, # NUMBER OF POINTS AQUIRED
		"score"  => int 0, # SUM OF SCORE AQUIRED
		"wins"   => int 0, # NUMBER OF WINS
		"draws"  => int 0, # NUMBER OF DRAWS
		"losses" => int 0, # NUMBER OF LOSSES
		"second" => int 0, # NUMBER OF TIMES BEING SECOND (ONLY FOR INGAME OPTION OF 3 OR 4)
		"third"  => int 0  # NUMBER OF TIMES BEING THIRD  (ONLY FOR INGAME OPTION OF 4)
	),
	...
)
```


## Methods

### Method list

| Scope | Name | Return | Description |
| :---: | :--: | :----: | :---------: |
| public | [`__construct`](#construct) | `$this` | Construct method |
| public | [`__toString`](#toString) | `string` | Returns the name of the team |
| public | [`setName`](#setName) | `$this` | Sets name of the team. |
| public | [`getName`](#getName) | `string` | Gets name of the team. |
| public | [`setId`](#setId) | `$this` | Sets id of the team. |
| public | [`getId`](#getId) | `string|int` | Gets id of the team. |
| public | [`getGamesInfo`](#getGamesInfo) | `array` | Gets statistics of the team from given group without the group object. |
| public | [`addGroupResults`](#addGroupResults) | `$this` | Creates a new data-array to store statistics for a new group. |
| public | [`getGroupResults`](#getGroupResults) | `array` | Gets team statistics from the given group. |
| public | [`addGameWith`](#addGameWith) | `$this` | Adds a record of a game with another team in a group. |
| public | [`getGameWith`](#getGameWith) | `array|int` | Gets a record of a game with another team or teams. |
| public | [`addGroup`](#addGroup) | `$this` | Adds a group to a team and creates an array for all games to be played. |
| public | [`addGame`](#addGame) | `$this` | Adds a game to this team. |
| public | [`getGames`](#getGames) | `array` | Gets all game from given group. |
| public | [`getSumPoints`](#getSumPoints) | `int` | Gets all points that the team has acquired through the tournament. |
| public | [`getSumScore`](#getSumScore) | `int` | Gets all score that the team has acquired through the tournament. |
| public | [`addWin`](#addWin) | `$this` | Adds a win points to the team. |
| public | [`removeWin`](#removeWin) | `$this` | Removes a win points to the team. |
| public | [`addDraw`](#addDraw) | `$this` | Adds a draw points to the team. |
| public | [`removeDraw`](#removeDraw) | `$this` | Removes a draw points to the team. |
| public | [`addLoss`](#addLoss) | `$this` | Adds a loss points to the team. |
| public | [`removeLoss`](#removeLoss) | `$this` | Removes a loss points to the team. |
| public | [`addSecond`](#addSecond) | `$this` | Adds points for being second to the team. |
| public | [`removeSecond`](#removeSecond) | `$this` | Removes points for being second to the team. |
| public | [`addThird`](#addThird) | `$this` | Adds points for being third to the team. |
| public | [`removeWin`](#removeWin) | `$this` | Removes points for being third to the team. |
| public | [`sumPoints`](#sumPoints) | `int` | Calculate all the points acquired from given group ids. |
| public | [`sumScore`](#sumScore) | `int` | Calculate all score acquired from given group ids. |
| public | [`addScore`](#addScore) | `$this` | Adds score to the total sum. |
| public | [`removeScore`](#removeScore) | `$this` | Removes score to the total sum. |
| public | [`addPoints`](#addPoints) | `$this` | Adds points to the total sum. |
| public | [`removePoints`](#removePoints) | `$this` | Removes points to the total sum. |
