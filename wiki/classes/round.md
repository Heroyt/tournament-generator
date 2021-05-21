## Introduction

**[Round](/classes/round/)** stores all groups that should play roughly at the same time.

---

### Creating a new round class

#### Basic construction

```php
include_once 'vendor/autoload.php';

$tournament = new TournamentGenerator\Tournament('Tournament name');

$round = new TournamentGenerator\Round('Round name');

$tournament->addRound($round);
```

#### From a round class

**Recomended**  
This automatically assigns the round to the round.

```php
include_once 'vendor/autoload.php';

$tournament = new TournamentGenerator\Tournament('Tournament name');

$round = $tournament->round('Round name');
```

**[Tournament](/classes/tournament/)** class can be also replaced by a **[Category](/classes/category)**

```php
include_once 'vendor/autoload.php';

$tournament = new TournamentGenerator\Tournament('Tournament name');

$category = $tournament->category('Category name');

$round = $category->round('Round name');
```

---

## Properties

| Scope | Name | Type | Default | Description |
| :---: | :--: | :--: | :-----: | :---------: |
| protected | `$name` | `string` | `''` | Name of the round |
| protected | `$id` | `string|int` | `null` | Id of the round |
| private | `$groups` | `array` | `[]` | List of round groups |
| private | `$games` | `array` | `[]` | List of round games |
| private | `$teams` | `array` | `[]` | List of teams |
| private | `$allowSkip` | `bool` | `false` | If `true`, generator will skip unplayed games without throwing exception |

## Methods

### Method list

| Scope | Name | Return | Description |
| :---: | :--: | :----: | :---------: |
| public | [`__construct`](#construct) | `$this` | Construct method |
| public | [`__toString`](#toString) | `string` | Returns the name of the round |
| public | [`setName`](#setName) | `$this` | Sets name of the round. |
| public | [`getName`](#getName) | `string` | Gets name of the round. |
| public | [`setId`](#setId) | `$this` | Sets id of the round. |
| public | [`getId`](#getId) | `string|int` | Gets id of the round. |
| public | [`allowSkip`](#allowSkip) | `$this` | Allow skipping of unplayed games while progressing. |
| public | [`disallowSkip`](#disallowSkip) | `$this` | Disllow skipping of unplayed games while progressing. |
| public | [`setSkip`](#setSkip) | `$this` | Sets whether to skip unplayed games while progressing or not. |
| public | [`getSkip`](#getSkip) | `bool` | Gets whether to skip unplayed games while progressing or not. |
| public | [`addGroup`](#addGroup) | `$this` | Adds created **[Group](/classes/group/)** to **[Round](/classes/round/)**. |
| public | [`group`](#group) | `TournamentGenerator\Round` | Creates and adds new **[Group](/classes/group/)** to **[Round](/classes/round/)**. |
| public | [`getGroups`](#getGroups) | `array` | Gets an array of all **[Groups](/classes/group/)** from **[Round](/classes/round/)**. |
| public | [`getGroupsIds`](#getGroupsIds) | `array` | Gets an array of all **[Group](/classes/group/)** ids from **[Round](/classes/round/)**. |
| public | [`orderGroups`](#orderGroups) | `array` | Sorts all **[Groups](/classes/group/)** from **[Round](/classes/round/)**. |
| public | [`addTeam`](#addTeam) | `$this` | Adds created **[Team](/classes/team/)** to **[Round](/classes/round/)**. |
| public | [`team`](#team) | `new TournamentGenerator\Team` | Creates and adds new **[Team](/classes/team/)** to **[Round](/classes/round/)**. |
| public | [`getTeams`](#getTeams) | `array` | Gets an array of all **[Teams](/classes/team/)** from **[Round](/classes/round/)**. |
| public | [`sortTeams`](#sortTeams) | `array` | Sorts all **[Teams](/classes/team/)** from **[Round](/classes/round/)** and returns them. |
| public | [`genGames`](#genGames) | `array` | Generates all the **[Games](/classes/game/)** of the **[Round](/classes/round/)**. |
| public | [`getGames`](#getGames) | `array` | Gets an array of all **[Games](/classes/game/)** from **[Round](/classes/round/)**. |
| public | [`isPlayed`](#isPlayed) | `bool` | Returns `true` if all **[Games](/classes/game/)** have been played. |
| public | [`splitTeams`](#splitTeams) | `$this` | Splits all **[Teams](/classes/team/)** from **[Round](/classes/round/)** to given **[Groups](/classes/group/)** (or all **[Groups](/classes/group/)** from a **[Round](/classes/round/)**). |
| public | [`progress`](#progress) | `$this` | Progresses all the **[Teams](/classes/team/)**. |
| public | [`simulate`](#simulate) | `$this` | Simulate all **[Games](/classes/game/)** from this **[Round](/classes/round/)** just like it was really played. |
| public | [`resetGames`](#resetGames) | `$this` | Resets the results of all **[Games](/classes/game/)** in **[Round](/classes/round/)**. |

---

<a name="construct" id="construct"></a>
### Round \_\_construct(string $name, string|int $id = null)

Creates a new **[Round](/classes/round/)** class

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` | `''` | Name of the round |
| `$id` | `string|int` | `''` | Name of the round |

##### Return value
```php
new TournamentGenerator\Round();
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

Returns round name

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
### public Round setName(string $name)

Sets the name of the round.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` |  | Name of the round |

##### Return value
```php
TournamentGenerator\Round $this
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

Gets the name of the round.

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

<a name="setName" id="setId"></a>
### public Round setId(string|int $id)

Sets the name of the round.

##### Parameters
| Id | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$id` | `string|int` |  | Id of the round |

##### Return value
```php
TournamentGenerator\Round $this
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

Gets the name of the round.

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
### public Round allowSkip()

Allow skipping of unplayed games while progressing.

##### Return value
```php
TournamentGenerator\Round $this
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
### public Round disallowSkip()

Disallow skipping of unplayed games while progressing.

##### Return value
```php
TournamentGenerator\Round $this
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
### public Round setSkip(bool $skip)

Sets whether an unplayed games should be skipped while progressing or not.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$skip` | `bool` |  | Skip or not |

##### Return value
```php
TournamentGenerator\Round $this
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

<a name="addGroup" id="addGroup"></a>
### public Round addGroup(TournamentGenerator\\Group ...$groups)

Adds created **[Group](/classes/group/)** to **[Round](/classes/round/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$groups` | `TournamentGenerator\Group` | `[]` | One or more instances of **[Group](/classes/group/)** class. |

##### Return value
```php
$this
```
#### Code

```php
public function addGroup(Group ...$groups){
	foreach ($groups as $group) {
		$this->groups[] = $group;
	}
	return $this;
}
```

---

<a name="group" id="group"></a>
### public Group group(string $name)

Creates and adds new **[Group](/classes/group/)** to **[Round](/classes/round/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` | `''` | Name of the new **[Group](/classes/group/)**. |
| `$id` | `string|int` | `null` | Id of the new **[Group](/classes/group/)**. |

##### Return value
```php
new TournamentGenerator\Group
```
#### Code

```php
public function group(string $name, $id = null) {
	$g = new Group($name, $id);
	$this->groups[] = $g->setSkip($this->allowSkip);
	return $g;
}
```

---

<a name="getGroups" id="getGroups"></a>
### public array getGroups()

Gets an array of all **[Groups](/classes/round/)** from **[Round](/classes/round/)**.

##### Return value
```php
array of TournamentGenerator\Group
```
#### Code

```php
public function getGroups(){
	$this->orderGroups();
	return $this->groups;
}
```

---

<a name="getGroupsIds" id="getGroupsIds"></a>
### public array getGroupsIds()

Gets an array of all **[Group](/classes/round/)** ids from **[Round](/classes/round/)**.

##### Return value
```php
array of TournamentGenerator\Group::$id
```
#### Code

```php
public function getGroupsIds() {
	$this->orderGroups();
	return array_map(function($a) { return $a->getId(); }, $this->groups);
}
```

---

<a name="orderGroups" id="orderGroups"></a>
### public array orderGroups()

Sorts all **[Groups](/classes/round/)** from **[Round](/classes/round/)**.

##### Return value
```php
array of TournamentGenerator\Group
```
#### Code

```php
public function orderGroups() {
	usort($this->groups, function($a, $b){
		return $a->getOrder() - $b->getOrder();
	});
	return $this->groups;
}
```

---

<a name="addTeam" id="addTeam"></a>
### public Round addTeam(TournamentGenerator\\Team ...$teams)

Adds created **[Team](/classes/team/)** to **[Round](/classes/round/)**.

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

Creates and adds new **[Team](/classes/team/)** to **[Round](/classes/round/)**.

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

Gets an array of all **[Teams](/classes/team/)** from **[Round](/classes/round/)**.
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

Sorts all **[Teams](/classes/team/)** from **[Round](/classes/round/)** and returns them.

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
	$teams = Utilis\Sorter\Teams::sortRound($this->teams, $this, $ordering);

	// APPLY FILTERS
	$filter = new Filter($this->getGroups(), $filters);
	$filter->filter($teams);

	return $teams;
}
```

---

<a name="genGames" id="genGames"></a>
### public array genGames()

Generates all **[Games](/classes/game/)** from **[Round](/classes/round/)**.

##### Return value
```php
array of TournamentGenerator\Game
```
#### Code

```php
public function genGames(){
	foreach ($this->groups as $group) {
		$group->genGames();
		$this->games = array_merge($this->games, $group->orderGames());
	}
	return $this->games;
}
```

---

<a name="getGames" id="getGames"></a>
### public array getGames()

Gets an array of all **[Games](/classes/game/)** from **[Round](/classes/round/)**.

##### Return value
```php
array of TournamentGenerator\Game
```
#### Code

```php
public function getGames() {
	return $this->games;
}
```

---

<a name="isPlayed" id="isPlayed"></a>
### public bool isPlayed()

Returns `true` if all **[Games](/classes/game/)** have been played.

##### Return value
```php
bool
```
#### Code

```php
public function isPlayed(){
	if (count($this->games) === 0) return false;
	foreach ($this->groups as $group) {
		if (!$group->isPlayed()) return false;
	}
	return true;
}
```

---

<a name="splitTeams" id="splitTeams"></a>
### public bool splitTeams(TournamentGenerator\\Round ...$wheres)

Split all **[Teams](/classes/team/)** from **[Round](/classes/round/)** into given **[Rounds](/classes/round/)**. If no argument is given, method will split into all available **[Rounds](/classes/round/)** in **[Round](/classes/round/)**.

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
public function splitTeams(Group ...$groups) {

	if (count($groups) === 0) $groups = $this->getGroups();

	$teams = $this->getTeams();
	shuffle($teams);

	while (count($teams) > 0) {
		foreach ($groups as $group) {
			if (count($teams) > 0) $group->addTeam(array_shift($teams));
		}
	}
	return $this;
}
```

---

<a name="progress" id="progress"></a>
### public Round progress(bool $returnTime)

Progresses all the **[Teams](/classes/team/)**.

##### Return value

```php
$this
```

#### Code

```php
public function progress(bool $blank = false){
	foreach ($this->groups as $group) {
		$group->progress($blank);
	}
	return $this;
}
```

---

<a name="simulate" id="simulate"></a>
### public Round simulate(bool $returnTime)

Simulate all **[Games](/classes/game/)** from this **[Round](/classes/round/)** just like it was really played.

##### Return value

```php
$this
```

#### Code

```php
public function simulate() {
	Utilis\Simulator::simulateRound($this);
	return $this;
}
```

---

<a name="resetGames" id="resetGames"></a>
### public Round resetGames(bool $returnTime)

Resets the results of all **[Games](/classes/game/)** in **[Round](/classes/round/)**.

##### Return value

```php
$this
```

#### Code

```php
public function resetGames() {
	foreach ($this->groups as $group) {
		$group->resetGames();
	}
	return $this;
}
```

---
