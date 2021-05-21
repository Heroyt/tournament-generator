## Introduction

**[Category](/classes/category/)** is a class that is used in order to separate a category into multiple categories with an ability to progress teams from one category to the other.

---

### Creating a new category class

#### Basic construction

```php
include_once 'vendor/autoload.php';

$tournament = new TournamentGenerator\Tournament('Tournament name');

$category = new TournamentGenerator\Category('Category name');

$tournament->addCategory($category);
```

#### From a category class

**Recomended**  
This automatically assigns the category to the category.

```php
include_once 'vendor/autoload.php';

$tournament = new TournamentGenerator\Tournament('Tournament name');

$category = $tournament->category('Category name');
```

---

## Properties

| Scope | Name | Type | Default | Description |
| :---: | :--: | :--: | :-----: | :---------: |
| protected | `$name` | `string` | `''` | Name of the category |
| protected | `$id` | `string|int` | `null` | Id of the category |
| private | `$rounds` | `array` | `[]` | List of category rounds |
| private | `$teams` | `array` | `[]` | List of teams |
| private | `$allowSkip` | `bool` | `false` | If `true`, generator will skip unplayed games without throwing exception |

## Methods

### Method list

| Scope | Name | Return | Description |
| :---: | :--: | :----: | :---------: |
| public | [`__construct`](#construct) | `$this` | Construct method |
| public | [`__toString`](#toString) | `string` | Returns the name of the category |
| public | [`setName`](#setName) | `$this` | Sets name of the category. |
| public | [`getName`](#getName) | `string` | Gets name of the category. |
| public | [`setId`](#setId) | `$this` | Sets id of the category. |
| public | [`getId`](#getId) | `string|int` | Gets id of the category. |
| public | [`allowSkip`](#allowSkip) | `$this` | Allow skipping of unplayed games while progressing. |
| public | [`disallowSkip`](#disallowSkip) | `$this` | Disllow skipping of unplayed games while progressing. |
| public | [`setSkip`](#setSkip) | `$this` | Sets whether to skip unplayed games while progressing or not. |
| public | [`getSkip`](#getSkip) | `bool` | Gets whether to skip unplayed games while progressing or not. |
| public | [`addRound`](#addRound) | `$this` | Adds created **[Round](/classes/round/)** to **[Category](/classes/category/)**. |
| public | [`round`](#round) | `TournamentGenerator\Round` | Creates and adds new **[Round](/classes/round/)** to **[Category](/classes/category/)**. |
| public | [`getRounds`](#getRounds) | `array` | Gets an array of all **[Rounds](/classes/round/)** from **[Category](/classes/category/)**. |
| public | [`getGroups`](#getGroups) | `array` | Gets an array of all **[Groups](/classes/group/)** from **[Category](/classes/category/)**. |
| public | [`addTeam`](#addTeam) | `$this` | Adds created **[Team](/classes/team/)** to **[Category](/classes/category/)**. |
| public | [`team`](#team) | `new TournamentGenerator\Team` | Creates and adds new **[Team](/classes/team/)** to **[Category](/classes/category/)**. |
| public | [`getTeams`](#getTeams) | `array` | Gets an array of all **[Teams](/classes/team/)** from **[Category](/classes/category/)**. |
| public | [`sortTeams`](#sortTeams) | `array` | Sorts all **[Teams](/classes/team/)** from **[Category](/classes/category/)** and returns them. |
| public | [`getGames`](#getGames) | `array` | Gets an array of all **[Games](/classes/game/)** from **[Category](/classes/category/)**. |
| public | [`splitTeams`](#splitTeams) | `$this` | Splits all **[Teams](/classes/team/)** from **[Category](/classes/category/)** to given **[Rounds](/classes/round/)** (or all **[Rounds](/classes/round/)** from a **[Category](/classes/category/)**). |
| public | [`genGamesSimulate`](#genGamesSimulate) | `array` | Generate and simulate all **[Games](/classes/game/)** from **[Category](/classes/category/)** without real teams (just to export) and returns array of all **[Games](/classes/game/)** or caculated category time. |
| public | [`genGamesSimulateReal`](#genGamesSimulateReal) | `array` | Generate and simulate all **[Games](/classes/game/)** from **[Category](/classes/category/)** with real teams (just as it was played for real) and returns array of all **[Games](/classes/game/)** or caculated category time. |

---

<a name="construct" id="construct"></a>
### Category \_\_construct(string $name, string|int $id = null)

Creates a new **[Category](/classes/category/)** class

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` | `''` | Name of the category |
| `$id` | `string|int` | `''` | Name of the category |

##### Return value
```php
new TournamentGenerator\Category();
```

#### Code

```php
public function __construct(string $name = '', $id = null) {
	$this->setName($name);
	$this->setId(isset($id) ? $id : uniqid());
}
```

---

<a name="toString" id="toString"></a>
### public string \_\_toString()

Returns category name

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
### public Category setName(string $name)

Sets the name of the category.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` |  | Name of the category |

##### Return value
```php
TournamentGenerator\Category $this
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

Gets the name of the category.

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

<a name="setId" id="setId"></a>
### public Category setId(string|int $id)

Sets the name of the category.

##### Parameters
| Id | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$id` | `string|int` |  | Id of the category |

##### Return value
```php
TournamentGenerator\Category $this
```
#### Code

```php
public function setId($id) {
	if (!is_string($id) && !is_int($id)) {
		$this->id = uniqid();
		throw new \Exception('Unsupported id type ('.gettype($id).') - expected type of string or int');
	}
	$this->id = $id;
}
```

---

<a name="getId" id="getId"></a>
### public string|int getId()

Gets the name of the category.

##### Return value
```php
string|int $this->id;
```
#### Code

```php
public function getId() {
	return $this->id;
}
```

---

<a name="allowSkip" id="allowSkip"></a>
### public Category allowSkip()

Allow skipping of unplayed games while progressing.

##### Return value
```php
TournamentGenerator\Category $this
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
### public Category disallowSkip()

Disallow skipping of unplayed games while progressing.

##### Return value
```php
TournamentGenerator\Category $this
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
### public Category setSkip(bool $skip)

Sets whether an unplayed games should be skipped while progressing or not.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$skip` | `bool` |  | Skip or not |

##### Return value
```php
TournamentGenerator\Category $this
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

<a name="addRound" id="addRound"></a>
### public Category addRound(TournamentGenerator\\Round ...$rounds)

Adds created **[Round](/classes/round/)** to **[Category](/classes/category/)**.

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
		$this->rounds[] = $round;
	}
	return $this;
}
```

---

<a name="round" id="round"></a>
### public Round round(string $name)

Creates and adds new **[Round](/classes/round/)** to **[Category](/classes/category/)**.

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

Gets an array of all **[Rounds](/classes/round/)** from **[Category](/classes/category/)**.

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

Gets an array of all **[Groups](/classes/round/)** from **[Category](/classes/category/)**.

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
### public Category addTeam(TournamentGenerator\\Team ...$teams)

Adds created **[Team](/classes/team/)** to **[Category](/classes/category/)**.

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

Creates and adds new **[Team](/classes/team/)** to **[Category](/classes/category/)**.

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

Gets an array of all **[Teams](/classes/team/)** from **[Category](/classes/category/)**.
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
	foreach ($this->rounds as $round) {
		$teams = \array_merge($teams, $round->getTeams());
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

Sorts all **[Teams](/classes/team/)** from **[Category](/classes/category/)** and returns them.

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

Gets an array of all **[Games](/classes/game/)** from **[Category](/classes/category/)**.

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
### public Category splitTeams(TournamentGenerator\\Round ...$wheres)

Split all **[Teams](/classes/team/)** from **[Category](/classes/category/)** into given **[Rounds](/classes/round/)**. If no argument is given, method will split into all available **[Rounds](/classes/round/)** in **[Category](/classes/category/)**.

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
### public array genGamesSimulate(bool $returnTime)

Generate and simulate all **[Games](/classes/game/)** from **[Category](/classes/category/)** without real teams (just to export) and returns array of all **[Games](/classes/game/)**. It uses **[BlankTeam](/classes/blankTeam/)** class for progressing.

##### Return value

```php
array of TournamentGenerator\Games
```

#### Code

```php
public function genGamesSimulate() {
	$games = Utilis\Simulator::simulateCategory($this);
	return $games;
}
```

---

<a name="genGamesSimulateReal" id="genGamesSimulateReal"></a>
### public array genGamesSimulateReal(bool $returnTime)

Generate and simulate all **[Games](/classes/game/)** from **[Category](/classes/category/)** with real teams (just as it was played for real) and returns array of all **[Games](/classes/game/)**.  
Could be used for testing and demonstration purposes.

##### Return value

```php
array of TournamentGenerator\Games
```

#### Code

```php
public function genGamesSimulateReal() {
	$games = Utilis\Simulator::simulateCategoryReal($this);
	return $games;
}
```

---
