## Introduction

**[Tournament](/classes/tournament/)** class is a main class, you will be working with most of the time. It's used to generate brackets, create new games and store all information about the tournament.

---

### Creating a new tournament class

```php
include_once 'vendor/autoload.php';

$tournament = new TournamentGenerator\Tournament('Tournament name');
```

### Setting up a basic tournament

See [Examples](/examples/basic/)

---

## Properties

| Scope | Name | Type | Default | Description |
| :---: | :--: | :--: | :-----: | :---------: |
| protected | `$name` | `string` | `''` | Name of the tournament |
| private | `$categories` | `array` | `[]` | List of tournament categories |
| private | `$rounds` | `array` | `[]` | List of tournament rounds |
| private | `$teams` | `array` | `[]` | List of teams |
| private | `$expectedPlay` | `int` | `0` | Time to play one game in minutes |
| private | `$expectedGameWait` | `int` | `0` | Pause between 2 games in minutes |
| private | `$expectedRoundWait` | `int` | `0` | Pause between 2 rounds in minutes |
| private | `$allowSkip` | `bool` | `false` | If `true`, generator will skip unplayed games without throwing exception |

## Methods

### Method list

| Scope | Name | Return | Description |
| :---: | :--: | :----: | :---------: |
| public | [`__construct`](#construct) | `$this` | Construct method |
| public | [`__toString`](#toString) | `string` | Returns the name of the tournament |
| public | [`setName`](#setName) | `$this` | Sets name of the tournament. |
| public | [`getName`](#getName) | `string` | Gets name of the tournament. |
| public | [`setPlay`](#setPlay) | `$this` | Sets time to play one game in minutes. |
| public | [`getPlay`](#getPlay) | `int` | Gets time to play one game in minutes. |
| public | [`setGameWait`](#setGameWait) | `$this` | Sets time to wait between games in minutes. |
| public | [`getGameWait`](#getGameWait) | `int` | Gets time to wait between games in minutes. |
| public | [`setRoundWait`](#setRoundWait) | `$this` | Sets time to wait between rounds in minutes. |
| public | [`getRoundWait`](#getRoundWait) | `int` | Gets time to wait between rounds in minutes. |
| public | [`setCategoryWait`](#setCategoryWait) | `$this` | Sets time to wait between category in minutes. |
| public | [`getCategoryWait`](#getCategoryWait) | `int` | Gets time to wait between category in minutes. |
| public | [`getTournamentTime`](#getTournamentTime) | `int` | Gets time to play the whole tournament in minutes. |
| public | [`allowSkip`](#allowSkip) | `$this` | Allow skipping of unplayed games while progressing. |
| public | [`disallowSkip`](#disallowSkip) | `$this` | Disllow skipping of unplayed games while progressing. |
| public | [`setSkip`](#setSkip) | `$this` | Sets whether to skip unplayed games while progressing or not. |
| public | [`getSkip`](#getSkip) | `bool` | Gets whether to skip unplayed games while progressing or not. |
| public | [`addCategory`](#addCategory) | `$this` | Adds created **[Category](/classes/category/)** to **[Tournament](/classes/tournament/)**. |
| public | [`category`](#category) | `TournamentGenerator\Category` | Creates and adds new **[Category](/classes/category/)** to **[Tournament](/classes/tournament/)**. |
| public | [`getCategories`](#getCategories) | `array` | Gets an array of all **[Categories](/classes/category/)** from **[Tournament](/classes/tournament/)**. |
| public | [`addRound`](#addRound) | `$this` | Adds created **[Round](/classes/round/)** to **[Tournament](/classes/tournament/)**. |
| public | [`round`](#round) | `TournamentGenerator\Round` | Creates and adds new **[Round](/classes/round/)** to **[Tournament](/classes/tournament/)**. |
| public | [`getRounds`](#getRounds) | `array` | Gets an array of all **[Rounds](/classes/round/)** from **[Tournament](/classes/tournament/)**. |
| public | [`getGroups`](#getGroups) | `array` | Gets an array of all **[Groups](/classes/group/)** from **[Tournament](/classes/tournament/)**. |
| public | [`addTeam`](#addTeam) | `$this` | Adds created **[Team](/classes/team/)** to **[Tournament](/classes/tournament/)**. |
| public | [`team`](#team) | `new TournamentGenerator\Team` | Creates and adds new **[Team](/classes/team/)** to **[Tournament](/classes/tournament/)**. |
| public | [`getTeams`](#getTeams) | `array` | Gets an array of all **[Teams](/classes/team/)** from **[Tournament](/classes/tournament/)**. |
| public | [`sortTeams`](#sortTeams) | `array` | Sorts all **[Teams](/classes/team/)** from **[Tournament](/classes/tournament/)** and returns them. |
| public | [`getGames`](#getGames) | `array` | Gets an array of all **[Games](/classes/game/)** from **[Tournament](/classes/tournament/)**. |
| public | [`splitTeams`](#splitTeams) | `$this` | Splits all **[Teams](/classes/team/)** from **[Tournament](/classes/tournament/)** to given **[Rounds](/classes/round/)** (or all **[Rounds](/classes/round/)** from a **[Tournament](/classes/tournament/)**). |
| public | [`genGamesSimulate`](#genGamesSimulate) | `array`/`int` | Generate and simulate all **[Games](/classes/game/)** from **[Tournament](/classes/tournament/)** without real teams (just to export) and returns array of all **[Games](/classes/game/)** or caculated tournament time. |
| public | [`genGamesSimulateReal`](#genGamesSimulateReal) | `array`/`int` | Generate and simulate all **[Games](/classes/game/)** from **[Tournament](/classes/tournament/)** with real teams (just as it was played for real) and returns array of all **[Games](/classes/game/)** or caculated tournament time. |

---

<a name="construct" id="construct"></a>
### Tournament \_\_construct(string $name)

Creates a new **[Tournament](/classes/tournament/)** class

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` | `''` | Name of the tournament |

##### Return value
```php
new TournamentGenerator\Tournament();
```

#### Code

```php
function __construct(string $name = ''){
	$this->name = $name;
}
```

---

<a name="toString" id="toString"></a>
### public string \_\_toString()

Returns tournament name

##### Return value
```php
string $this->name;
```
#### Code

```php
public function __toString() {
	return $this->name;
}
```

---

<a name="setName" id="setName"></a>
### public Tournament setName(string $name)

Sets the name of the tournament.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` |  | Name of the tournament |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

```php
public function setName(string $name) {
	$this->name = $name;
	return $this;
}
```

---

<a name="getName" id="getName"></a>
### public string getName()

Gets the name of the tournament.

##### Return value
```php
string $this->name;
```
#### Code

```php
public function getName() {
	return $this->name;
}
```

---

<a name="setPlay" id="setPlay"></a>
### public Tournament setPlay(int $play)

Sets time to play one game in minutes.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$play` | `int` |  | Time of one game in minutes |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

```php
public function setPlay(int $play) {
	$this->expectedPlay = $play;
	return $this;
}
```

---

<a name="getPlay" id="getPlay"></a>
### public int getPlay()

Gets time to play one game in minutes.

##### Return value
```php
int $this->expectedPlay;
```
#### Code

```php
public function getPlay() {
	return $this->expectedPlay;
}
```

---

<a name="setGameWait" id="setGameWait"></a>
### public Tournament setGameWait(int $wait)

Sets time to wait between games in minutes.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$wait` | `int` |  | Pause between two games in minutes |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

```php
public function setGameWait(int $wait) {
	$this->expectedGameWait = $wait;
	return $this;
}
```

---

<a name="getGameWait" id="getGameWait"></a>
### public int getGameWait()

Sets time to play one game in minutes.

##### Return value
```php
int $this->expectedGameWait;
```
#### Code

```php
public function getGameWait() {
	return $this->expectedGameWait;
}
```

---

<a name="setRoundWait" id="setRoundWait"></a>
### public Tournament setRoundWait(int $wait)

Sets time to wait between rounds in minutes.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$wait` | `int` |  | Pause between rounds in minutes |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

```php
public function setRoundWait(int $wait) {
	$this->expectedRoundWait = $wait;
	return $this;
}
```

---

<a name="getRoundWait" id="getRoundWait"></a>
### public int getRoundWait()

Gets time between rounds in minutes.

##### Return value
```php
int $this->expectedRoundWait
```
#### Code

```php
public function getRoundWait() {
	return $this->expectedRoundWait;
}
```

---

<a name="setCategoryWait" id="setCategoryWait"></a>
### public Tournament setCategoryWait(int $wait)

Sets time to wait between categories in minutes.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$wait` | `int` |  | Pause between categories in minutes |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

```php
public function setCategoryWait(int $wait) {
	$this->expectedCategoryWait = $wait;
	return $this;
}
```

---

<a name="getCategoryWait" id="getCategoryWait"></a>
### public int getCategoryWait()

Gets time between categories in minutes.

##### Return value
```php
int $this->expectedCategoryWait
```
#### Code

```php
public function getCategoryWait() {
	return $this->expectedCategoryWait;
}
```

---

<a name="getTournamentTime" id="getTournamentTime"></a>
### public int getTournamentTime()

Gets expected time to play the whole tournament.

##### Return value
```php
int
```
#### Code

```php
public function getTournamentTime(){
	$games = count($this->getGames());
	return $games*$this->expectedPlay+$games*$this->expectedGameWait+count($this->getRounds())*$this->expectedRoundWait+count($this->getCategories())*$this->expectedCategoryWait;
  	}
```

---

<a name="allowSkip" id="allowSkip"></a>
### public Tournament allowSkip()

Allow skipping of unplayed games while progressing.

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

```php
public function allowSkip(){
	$this->allowSkip = true;
	return $this;
}
```

---

<a name="disallowSkip" id="disallowSkip"></a>
### public Tournament disallowSkip()

Disallow skipping of unplayed games while progressing.

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

```php
public function disallowSkip(){
	$this->allowSkip = false;
	return $this;
}
```

---

<a name="setSkip" id="setSkip"></a>
### public Tournament setSkip(bool $skip)

Sets whether an unplayed games should be skipped while progressing or not.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$skip` | `bool` |  | Skip or not |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

```php
public function setSkip(bool $skip) {
	$this->allowSkip = $skip;
	return $this;
}
```

---

<a name="getSkip" id="getSkip"></a>
### public bool getSkip()

Gets whether an unplayed games should be skipped while progressing or not.

##### Return value
```php
bool $this->allowSkip
```
#### Code

```php
public function getSkip(bool $skip) {
	return $this->allowSkip;
}
```

---

<a name="addCategory" id="addCategory"></a>
### public Tournament addCategory(TournamentGenerator\\Category ...$categories)

Adds created **[Categories](/classes/category/)** to **[Tournament](/classes/tournament/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$categories` | `TournamentGenerator\Category` | `[]` | One or more instances of **[Category](/classes/category/)** class |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

```php
public function addCategory(Category ...$categories){
	foreach ($categories as $category) {
		if ($category instanceof Category) $this->categories[] = $category;
		else throw new \Exception('Trying to add category which is not an instance of the Category class.');
	}
	return $this;
}
```

---

<a name="category" id="category"></a>
### public Category category(string $name)

Creates and adds new **[Category](/classes/category/)** to **[Tournament](/classes/tournament/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` | `''` | Name of the new **[Category](/classes/category/)**. |
| `$id` | `string|int` | `null` | Id of the new **[Category](/classes/category/)**. |

##### Return value
```php
new TournamentGenerator\Category
```
#### Code

```php
public function category(string $name = '', $id = null) {
	$c = new Category($name, $id);
	$this->categories[] = $c->setSkip($this->allowSkip);
	return $c;
}
```

---

<a name="getCategories" id="getCategories"></a>
### public array getCategories()

Gets an array of all **[Categories](/classes/category/)** from **[Tournament](/classes/tournament/)**.

##### Return value
```php
array of TournamentGenerator\Category
```
#### Code

```php
public function getCategories() {
	return $this->categories;
}
```

---

<a name="addRound" id="addRound"></a>
### public Tournament addRound(TournamentGenerator\\Round ...$rounds)

Adds created **[Round](/classes/round/)** to **[Tournament](/classes/tournament/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$rounds` | `TournamentGenerator\Round` | `[]` | One or more instances of **[Round](/classes/round/)** class. |

##### Return value
```php
$this
```
#### Code

```php
public function addRound(Round ...$rounds) {
	foreach ($rounds as $round) {
		if ($round instanceof Round) $this->rounds[] = $round;
		else throw new \Exception('Trying to add round which is not an instance of the Round class.');
	}
	return $this;
}
```

---

<a name="round" id="round"></a>
### public Round round(string $name)

Creates and adds new **[Round](/classes/round/)** to **[Tournament](/classes/tournament/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` | `''` | Name of the new **[Round](/classes/round/)**. |
| `$id` | `string|int` | `null` | Id of the new **[Round](/classes/round/)**. |

##### Return value
```php
new TournamentGenerator\Round
```
#### Code

```php
public function round(string $name = '', $id = null) {
	$r = new Round($name, $id);
	$this->rounds[] = $r->setSkip($this->allowSkip);
	return $r;
}
```

---

<a name="getRounds" id="getRounds"></a>
### public array getRounds()

Gets an array of all **[Rounds](/classes/round/)** from **[Tournament](/classes/tournament/)**.

##### Return value
```php
array of TournamentGenerator\Round
```
#### Code

```php
public function getRounds() {
	if (count($this->categories) > 0) {
		$rounds = [];
		foreach ($this->categories as $category) {
			$rounds = array_merge($rounds, $category->getRounds());
		}
		return $rounds;
	}
	return $this->rounds;
}
```

---

<a name="getGroups" id="getGroups"></a>
### public array getGroups()

Gets an array of all **[Groups](/classes/round/)** from **[Tournament](/classes/tournament/)**.

##### Return value
```php
array of TournamentGenerator\Group
```
#### Code

```php
public function getGroups() {
	$groups = [];
	foreach ($this->getRounds() as $round) {
		$groups = array_merge($groups, $round->getGroups());
	}
	return $groups;
}
```

---

<a name="addTeam" id="addTeam"></a>
### public Tournament addTeam(TournamentGenerator\\Team ...$teams)

Adds created **[Team](/classes/team/)** to **[Tournament](/classes/tournament/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$teams` | `TournamentGenerator\Team` | `[]` | One or more instances of **[Team](/classes/team/)** class. |

##### Return value
```php
$this
```
#### Code

```php
public function addTeam(Team ...$teams) {
	foreach ($teams as $team) {
		$this->teams[] = $team;
	}
	return $this;
}
```

---

<a name="team" id="team"></a>
### public Team team(string $name)

Creates and adds new **[Team](/classes/team/)** to **[Tournament](/classes/tournament/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` | `''` | Name of the new **[Team](/classes/team/)**. |
| `$id` | `string|int` | `null` | Id of the new **[Team](/classes/team/)**. |

##### Return value
```php
new TournamentGenerator\Team
```
#### Code

```php
public function team(string $name = '', $id = null) {
	$t = new Team($name, $id);
	$this->teams[] = $t;
	return $t;
}
```

---

<a name="getTeams" id="getTeams"></a>
### public array getTeams(bool $ordered = false, $ordering = POINTS)

Gets an array of all **[Teams](/classes/team/)** from **[Tournament](/classes/tournament/)**.
If passed `true` as the first argument, teams will be ordered.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$ordered` | `bool` | `false` | If teams should be ordered. |
| `$ordering` | `\TournamentGenerator\Constants::POINTS | \TournamentGenerator\Constants::SCORE` | `\TournamentGenerator\Constants::POINTS` | What to order the teams by. |
| `$filters` | `array` | `[]` | **[Filters](/classes/teamFilter/)** to filter the teams. |

##### Return value
```php
array of TournamentGenerator\Team
```
#### Code

```php
public function getTeams(bool $ordered = false, $ordering = \TournamentGenerator\Constants::POINTS, array $filters = []) {
	$teams = $this->teams;
	foreach ($this->categories as $category) {
		$teams = array_merge($teams, $category->getTeams());
	}
	foreach ($this->rounds as $round) {
		$teams = array_merge($teams, $round->getTeams());
	}
	$teams = \array_unique($teams);
	$this->teams = $teams;
	if ($ordered) $teams = $this->sortTeams($ordering);

	// APPLY FILTERS
	$filter = new Filter($this->getGroups(), $filters);
	$filter->filter($teams);

	return $teams;
}
```

---

<a name="sortTeams" id="sortTeams"></a>
### public array sortTeams($ordering = POINTS)

Sorts all **[Teams](/classes/team/)** from **[Tournament](/classes/tournament/)** and returns them.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$ordering` | `\TournamentGenerator\Constants::POINTS | \TournamentGenerator\Constants::SCORE` | `\TournamentGenerator\Constants::POINTS` | What to order the teams by. |
| `$filters` | `array` | `[]` | **[Filters](/classes/teamFilter/)** to filter the teams. |

##### Return value
```php
array of TournamentGenerator\Team
```
#### Code

```php
public function sortTeams($ordering = \TournamentGenerator\Constants::POINTS, array $filters = []) {
	$teams = [];
	for ($i = count($this->rounds)-1; $i >= 0; $i--) {
		$rTeams = array_filter($this->rounds[$i]->getTeams(true, $ordering), function($a) use ($teams) { return !in_array($a, $teams); });
		$teams = array_merge($teams, $rTeams);
	}
	$this->teams = $teams;

	// APPLY FILTERS
	$filter = new Filter($this->getGroups(), $filters);
	$filter->filter($teams);

	return $teams;
}
```

---

<a name="getGames" id="getGames"></a>
### public array getGames()

Gets an array of all **[Games](/classes/game/)** from **[Tournament](/classes/tournament/)**.

##### Return value
```php
array of TournamentGenerator\Game
```
#### Code

```php
public function getGames() {
	$games = [];
	foreach ($this->getRounds() as $round) {
		$games = array_merge($games, $round->getGames());
	}
	return $games;
}
```

---

<a name="splitTeams" id="splitTeams"></a>
### public Tournament splitTeams(TournamentGenerator\\Round ...$wheres)

Split all **[Teams](/classes/team/)** from **[Tournament](/classes/tournament/)** into given **[Rounds](/classes/round/)**. If no argument is given, method will split into all available **[Rounds](/classes/round/)** in **[Tournament](/classes/tournament/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$wheres` | `TournamentGenerator\Round` | `[]` | One or more instances of **[Round](/classes/round/)** class to split the teams into. |

##### Return value
```php
$this
```
#### Code

```php
public function splitTeams(Round ...$wheres) {

	if (count($wheres) === 0) $wheres = $this->getRounds();

	$teams = $this->getTeams();
	shuffle($teams);

	while (count($teams) > 0) {
		foreach ($wheres as $where) {
			if (count($teams) > 0) $where->addTeam(array_shift($teams));
		}
	}
	foreach ($wheres as $where) {
		$where->splitTeams();
	}
	return $this;
}
```

---

<a name="genGamesSimulate" id="genGamesSimulate"></a>
### public array|int genGamesSimulate(bool $returnTime)

Generate and simulate all **[Games](/classes/game/)** from **[Tournament](/classes/tournament/)** without real teams (just to export) and returns array of all **[Games](/classes/game/)** or caculated tournament time. It uses **[BlankTeam](/classes/blankTeam/)** class for progressing.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$returnTime` | `bool` | `false` | If true, returns calculated tournament time in minutes. |

##### Return value

If `$returnTime` parameter empty or `false`
```php
array of TournamentGenerator\Games
```
If `$returnTime` parameter `true`
```php
int $this->getTournamentTime()
```

#### Code

```php
public function genGamesSimulate(bool $returnTime = false) {
	$games = Utilis\Simulator::simulateTournament($this);

	if ($returnTime) return $this->getTournamentTime();
	return $games;
}
```

---

<a name="genGamesSimulateReal" id="genGamesSimulateReal"></a>
### public array|int genGamesSimulateReal(bool $returnTime)

Generate and simulate all **[Games](/classes/game/)** from **[Tournament](/classes/tournament/)** with real teams (just as it was played for real) and returns array of all **[Games](/classes/game/)** or caculated tournament time.  
Could be used for testing and demonstration purposes.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$returnTime` | `bool` | `false` | If true, returns calculated tournament time in minutes. |

##### Return value

If `$returnTime` parameter empty or `false`
```php
array of TournamentGenerator\Games
```
If `$returnTime` parameter `true`
```php
int $this->getTournamentTime()
```

#### Code

```php
public function genGamesSimulateReal(bool $returnTime = false) {
	$games = Utilis\Simulator::simulateTournamentReal($this);

	if ($returnTime) return $this->getTournamentTime();
	return $games;
}
```

---
