## Introduction

**[Group](/classes/group/)** is the main class for storing and generating games.

---

### Creating a new group class

#### Basic construction

```php
include_once 'vendor/autoload.php';

$round = new TournamentGenerator\Round('Round name');

$group = new TournamentGenerator\Group('Group name');

$round->addGroup($group);
```

#### From a group class

**Recomended**  
This automatically assigns the group to the group.

```php
include_once 'vendor/autoload.php';

$round = new TournamentGenerator\Round('Round name');

$group = $round->group('Group name');
```

---

## Properties

| Scope | Name | Type | Default | Description |
| :---: | :--: | :--: | :-----: | :---------: |
| protected | `$name` | `string` | `''` | Name of the group |
| protected | `$id` | `string|int` | `null` | Id of the group |
| private | `$generator` | `Utilis\Generator` | `null` | Generator class to generate all games |
| private | `$games` | `array` | `[]` | List of group games |
| private | `$teams` | `array` | `[]` | List of teams |
| private | `$progressed` | `array` | `[]` | List of all progressed teams |
| private | `$ordering` | `Constants::POINTS|SCORE` | `Constants::POINTS` | What to order teams by |
| private | `$progressions` | `array` | `[]` | List of all progressions to progress teams by |
| private | `$winPoints` | `3` | `int` | Points acquired from winning |
| private | `$drawPoints` | `1` | `int` | Points acquired from drawing |
| private | `$lostPoints` | `0` | `int` | Points acquired from losing |
| private | `$secondPoints` | `2` | `int` | Points acquired from being second (at least 3 teams in one game) |
| private | `$thirdPoints` | `1` | `int` | Points acquired from being third (at least 4 teams in one game) |
| private | `$progressPoints` | `50` | `int` | Points acquired from progressing |
| private | `$order` | `0` | `int` | Index to order groups by |

## Methods

### Method list

| Scope | Name | Return | Description |
| :---: | :--: | :----: | :---------: |
| public | [`__construct`](#construct) | `$this` | Construct method |
| public | [`__toString`](#toString) | `string` | Returns the name of the group |
| public | [`setName`](#setName) | `$this` | Sets name of the group. |
| public | [`getName`](#getName) | `string` | Gets name of the group. |
| public | [`setId`](#setId) | `$this` | Sets id of the group. |
| public | [`getId`](#getId) | `string|int` | Gets id of the group. |
| public | [`allowSkip`](#allowSkip) | `$this` | Allow skipping of unplayed games while progressing. |
| public | [`disallowSkip`](#disallowSkip) | `$this` | Disllow skipping of unplayed games while progressing. |
| public | [`setSkip`](#setSkip) | `$this` | Sets whether to skip unplayed games while progressing or not. |
| public | [`getSkip`](#getSkip) | `bool` | Gets whether to skip unplayed games while progressing or not. |
| public | [`addTeam`](#addTeam) | `$this` | Adds created **[Team](/classes/team/)** to **[Group](/classes/group/)**. |
| public | [`team`](#team) | `new TournamentGenerator\Team` | Creates and adds new **[Team](/classes/team/)** to **[Group](/classes/group/)**. |
| public | [`getTeams`](#getTeams) | `array` | Gets an array of all **[Teams](/classes/team/)** from **[Group](/classes/group/)**. |
| public | [`sortTeams`](#sortTeams) | `array` | Sorts all **[Teams](/classes/team/)** from **[Group](/classes/group/)** and returns them. |
| public | [`setWinPoints`](#setWinPoints) | `$this` | Sets  points acquired from winning. |
| public | [`getWinPoints`](#getWinPoints) | `int` | Gets points acquired from winning. |
| public | [`setDrawPoints`](#setDrawPoints) | `$this` | Sets  points acquired from drawing. |
| public | [`getDrawPoints`](#getDrawPoints) | `int` | Gets points acquired from drawing. |
| public | [`setLostPoints`](#setLostPoints) | `$this` | Sets  points acquired from losing. |
| public | [`getLostPoints`](#getLostPoints) | `int` | Gets points acquired from losing. |
| public | [`setSecondPoints`](#setSecondPoints) | `$this` | Sets points acquired from being second (at least 3 teams in one game). |
| public | [`getSecondPoints`](#getSecondPoints) | `int` | Gets points acquired from being second (at least 3 teams in one game). |
| public | [`setThirdPoints`](#setThirdPoints) | `$this` | Sets points acquired from being third (at least 4 teams in one game). |
| public | [`getThirdPoints`](#getThirdPoints) | `int` | Gets points acquired from being third (at least 4 teams in one game). |
| public | [`setProgressPoints`](#setProgressPoints) | `$this` | Sets points acquired from progressing. |
| public | [`getProgressPoints`](#getProgressPoints) | `int` | Gets points acquired from progressing. |
| public | [`setMaxSize`](#setMaxSize) | `$this` | Sets maximum amount of teams in group for generating (Only for **[ROUND_SPLIT](/classes/constants/#roundSplit)**). |
| public | [`getMaxSize`](#getMaxSize) | `int` | Gets maximum amount of teams in group for generating (Only for **[ROUND_SPLIT](/classes/constants/#roundSplit)**). |
| public | [`setType`](#setType) | `$this` | Sets [type](/classes/constants/#roundTypes) of the group for generating |
| public | [`getType`](#getType) | `string` | Gets [type](/classes/constants/#roundTypes) of the group for generating |
| public | [`setOrder`](#setOrder) | `$this` | Sets index to order the groups by |
| public | [`getOrder`](#getOrder) | `string` | Gets index to order the groups by |
| public | [`setOrdering`](#setOrdering) | `$this` | Sets [ordering](/classes/constants/#ordering) of the teams in a group |
| public | [`getOrdering`](#getOrdering) | `string` | Gets [ordering](/classes/constants/#ordering) of the teams in a group |
| public | [`setInGame`](#setInGame) | `$this` | Sets the number of teams in one game (2-4) |
| public | [`getInGame`](#getInGame) | `int` | Gets the number of teams in one game (2-4) |
| public | [`addProgression`](#addProgression) | `$this` | Adds created **[Progression](/classes/progression/)** to group |
| public | [`progression`](#progression) | `Progression` | Creates and adds a new **[Progression](/classes/progression/)** to group |
| public | [`progress`](#progress) | `$this` | Progresses all teams according to all created **[Progressions](/classes/progression/)** |
| public | [`addProgressed`](#addProgressed) | `$this` | Adds **[Teams](/classes/team/)** to progressed list. |
| public | [`isProgressed`](#isProgressed) | `bool` | Check if **[Team](/classes/team/)** have been progressed from this group |
| public | [`genGames`](#genGames) | `array` | Generates all the **[Games](/classes/game/)** of the **[Group](/classes/group/)**. |
| public | [`addGame`](#addGame) | `$this` | Adds created **[Game](/classes/game/)** to group |
| public | [`game`](#game) | `Game` | Creates and adds a new **[Game](/classes/game/)** to group |
| public | [`getGames`](#getGames) | `array` | Gets an array of all **[Games](/classes/game/)** from **[Group](/classes/group/)**. |
| public | [`orderGames`](#orderGames) | `array` | Orders all **[Games](/classes/games/)** so the **[Teams](/classes/teams/)** doesn't play that many **[Games](/classes/games/)** after one other |
| public | [`simulate`](#simulate) | `$this` | Simulate all **[Games](/classes/game/)** from this **[Group](/classes/group/)** just like it was really played. |
| public | [`resetGames`](#resetGames) | `$this` | Resets the results of all **[Games](/classes/game/)** in **[Group](/classes/group/)**. |
| public | [`isPlayed`](#isPlayed) | `bool` | Returns `true` if all **[Games](/classes/game/)** have been played. |

---

<a name="construct" id="construct"></a>
### Group \_\_construct(string $name, string|int $id = null)

Creates a new **[Group](/classes/group/)** class

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` | `''` | Name of the group |
| `$id` | `string|int` | `''` | Name of the group |

##### Return value
```php
new TournamentGenerator\Group();
```

#### Code

```php
public function __construct(string $name = '', $id = null) {
	$this->setName($name);
	$this->generator = new Utilis\Generator($this);
	$this->setId(isset($id) ? $id : uniqid());
}
```

---

<a name="toString" id="toString"></a>
### public string \_\_toString()

Returns group name

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
### public Group setName(string $name)

Sets the name of the group.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | `string` |  | Name of the group |

##### Return value
```php
TournamentGenerator\Group $this
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

Gets the name of the group.

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
### public Group setId(string|int $id)

Sets the name of the group.

##### Parameters
| Id | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$id` | `string|int` |  | Id of the group |

##### Return value
```php
TournamentGenerator\Group $this
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

Gets the name of the group.

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
### public Group allowSkip()

Allow skipping of unplayed games while progressing.

##### Return value
```php
TournamentGenerator\Group $this
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
### public Group disallowSkip()

Disallow skipping of unplayed games while progressing.

##### Return value
```php
TournamentGenerator\Group $this
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
### public Group setSkip(bool $skip)

Sets whether an unplayed games should be skipped while progressing or not.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$skip` | `bool` |  | Skip or not |

##### Return value
```php
TournamentGenerator\Group $this
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

<a name="addTeam" id="addTeam"></a>
### public Group addTeam(TournamentGenerator\\Team ...$teams)

Adds created **[Team](/classes/team/)** to **[Group](/classes/group/)**.

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

Creates and adds new **[Team](/classes/team/)** to **[Group](/classes/group/)**.

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

Gets an array of all **[Teams](/classes/team/)** from **[Group](/classes/group/)**.
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

	if ($ordered) return $this->sortTeams($ordering, $filters);

	// APPLY FILTERS
	$filter = new Filter([$this], $filters);
	$filter->filter($teams);

	return $teams;
}
```

---

<a name="sortTeams" id="sortTeams"></a>
### public array sortTeams($ordering = POINTS)

Sorts all **[Teams](/classes/team/)** from **[Group](/classes/group/)** and returns them.

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
	if (!isset($ordering)) $ordering = $this->ordering;
	$this->teams = Utilis\Sorter\Teams::sortGroup($this->teams, $this, $ordering);
	return $this->getTeams(false, null, $filters);
}
```

---

<a name="setWinPoints" id="setWinPoints"></a>
### public Group setWinPoints(int $points)

Sets points acquired from winning.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$points` | `int` |  | Get all points |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setWinPoints(int $points) {
	$this->winPoints = $points;
	return $this;
}
```

---

<a name="getWinPoints" id="getWinPoints"></a>
### public int getWinPoints()

Gets points acquired from winning.

##### Return value
```php
int $this->winPoints
```
#### Code

```php
public function getWinPoints() {
	return $this->winPoints;
}
```

---

<a name="setDrawPoints" id="setDrawPoints"></a>
### public Group setDrawPoints(int $points)

Sets points acquired from drawing.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$points` | `int` |  | Get all points |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setDrawPoints(int $points) {
	$this->drawPoints = $points;
	return $this;
}
```

---

<a name="getDrawPoints" id="getDrawPoints"></a>
### public int getDrawPoints()

Gets points acquired from drawing.

##### Return value
```php
int $this->drawPoints
```
#### Code

```php
public function getDrawPoints() {
	return $this->drawPoints;
}
```

---

<a name="setLostPoints" id="setLostPoints"></a>
### public Group setLostPoints(int $points)

Sets points acquired from losing.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$points` | `int` |  | Get all points |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setLostPoints(int $points) {
	$this->lostPoints = $points;
	return $this;
}
```

---

<a name="getLostPoints" id="getLostPoints"></a>
### public int getLostPoints()

Gets points acquired from losing.

##### Return value
```php
int $this->lostPoints
```
#### Code

```php
public function getLostPoints() {
	return $this->lostPoints;
}
```

---

<a name="setSecondPoints" id="setSecondPoints"></a>
### public Group setSecondPoints(int $points)

Sets points acquired from being second (at least 3 teams in one game).

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$points` | `int` |  | Get all points |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setSecondPoints(int $points) {
	$this->secondPoints = $points;
	return $this;
}
```

---

<a name="getSecondPoints" id="getSecondPoints"></a>
### public int getSecondPoints()

Gets points acquired from being second (at least 3 teams in one game).

##### Return value
```php
int $this->secondPoints
```
#### Code

```php
public function getSecondPoints() {
	return $this->secondPoints;
}
```

---

<a name="setThirdPoints" id="setThirdPoints"></a>
### public Group setThirdPoints(int $points)

Sets points acquired from being third (at least 4 teams in one game).

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$points` | `int` |  | Get all points |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setThirdPoints(int $points) {
	$this->thirdPoints = $points;
	return $this;
}
```

---

<a name="getThirdPoints" id="getThirdPoints"></a>
### public int getThirdPoints()

Gets points acquired from being third (at least 4 teams in one game).

##### Return value
```php
int $this->thirdPoints
```
#### Code

```php
public function getThirdPoints() {
	return $this->thirdPoints;
}
```

---

<a name="setProgressPoints" id="setProgressPoints"></a>
### public Group setProgressPoints(int $points)

Sets points acquired from progressing.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$points` | `int` |  | Get all points |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setProgressPoints(int $points) {
	$this->progressPoints = $points;
	return $this;
}
```

---

<a name="getProgressPoints" id="getProgressPoints"></a>
### public int getProgressPoints()

Gets points acquired from progressing.

##### Return value
```php
int $this->progressPoints
```
#### Code

```php
public function getProgressPoints() {
	return $this->progressPoints;
}
```

---

<a name="setMaxSize" id="setMaxSize"></a>
### public Group setMaxSize(int $size)

Sets maximum amount of teams in group for generating (Only for **[ROUND_SPLIT](/classes/constants/#roundSplit)**).

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$size` | `int` |  | Max size of the group |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setMaxSize(int $size) {
	$this->generator->setMaxSize($size);
	return $this;
}
```

---

<a name="getMaxSize" id="getMaxSize"></a>
### public int getMaxSize()

Gets maximum amount of teams in group for generating (Only for **[ROUND_SPLIT](/classes/constants/#roundSplit)**).

##### Return value
```php
int $this->generator->maxSize
```
#### Code

```php
public function getMaxSize() {
	return $this->generator->getMaxSize();
}
```

---

<a name="setType" id="setType"></a>
### public Group setType(string $type)

Sets [type](/classes/constants/#roundTypes) of the group for generating

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$type` | `string` |  | Type of the group |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setType(string $type) {
	$this->generator->setType($type);
	return $this;
}
```

---

<a name="getType" id="getType"></a>
### public string getType()

Gets [type](/classes/constants/#roundTypes) of the group for generating

##### Return value
```php
int $this->generator->type
```
#### Code

```php
public function getType() {
	return $this->generator->getType();
}
```

---

<a name="setOrder" id="setOrder"></a>
### public Group setOrder(int $order)

Sets index to order the groups by

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$order` | `int` |  | Group index |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setOrder(int $order) {
	$this->order = $order;
	return $this;
}
```

---

<a name="getOrder" id="getOrder"></a>
### public string getOrder()

Gets index to order the groups by

##### Return value
```php
int $this->order
```
#### Code

```php
public function getOrder() {
	return $this->order;
}
```

---

<a name="setOrdering" id="setOrdering"></a>
### public Group setOrdering(string $ordering)

Sets [ordering](/classes/constants/#ordering) of the teams in a group

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$ordering` | `string` | `Constants::POINTS` | Ordering |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setOrdering(int $ordering) {
	if (!in_array($ordering, \TournamentGenerator\Constants::OrderingTypes)) throw new \Exception('Unknown group ordering: '.$ordering);
	$this->ordering = $ordering;
	return $this;
}
```

---

<a name="getOrdering" id="getOrdering"></a>
### public string getOrdering()

Gets [ordering](/classes/constants/#ordering) of the teams in a group

##### Return value
```php
string $this->ordering
```
#### Code

```php
public function getOrdering() {
	return $this->ordering;
}
```

---

<a name="setInGame" id="setInGame"></a>
### public Group setInGame(int $inGame)

Sets the number of teams in one game

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$inGame` | `int` |  | Number of teams in one game |

##### Return value
```php
TournamentGenerator\Group $this
```
#### Code

```php
public function setInGame(int $inGame) {
	$this->generator->setInGame($inGame);
	return $this;
}
```

---

<a name="getInGame" id="getInGame"></a>
### public int getInGame()

Gets the number of teams in one game

##### Return value
```php
int $this->generator->inGame;
```
#### Code

```php
public function getInGame() {
	return $this->generator->getInGame();
}
```

---

<a name="addProgression" id="addProgression"></a>
### public Group addProgression(Progression $progression)

Adds a **[Progression](/classes/progression/)** to a **[Group](/classes/group/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$progression` | `Progression` |  | An instantiated **[Progression](/classes/progression/)** to add |

##### Return value

```php
$this
```

#### Code

```php
public function addProgression(bool $blank = false){
	$this->progressions[] = $progression;
	return $this;
}
```

---

<a name="progression" id="progression"></a>
### public Group progression(Group $to, int $start = 0, int $len = null)

Creates and adds new **[Progression](/classes/progression/)** to a **[Group](/classes/group/)**.

See: [Using progressions example](/examples/progressions/)

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$to` | `Group` |  | An instantiated **[Group](/classes/group/)** to progress the [teams](/classes/team/) into |
| `$start` | `int` | `0` | Offset to start picking teams (if negative, the start of the progressed portion is at that offset from the end of the teams array) |
| `$len` | `int` | `null` | How many teams to progress from the offset |

##### Return value

```php
$this
```

#### Code

```php
public function progression(Group $to, int $start = 0, int $len = null) {
	$p = new Progression($this, $to, $start, $len);
	$this->progressions[] = $p;
	return $p;
}
```

---

<a name="progress" id="progress"></a>
### public Group progress(bool $blank = false)

Progresses all the **[Teams](/classes/team/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$blank` | `bool` | `false` | Whether to progress [teams](/classes/team/) as [blank](/classes/blankTeam/) |

##### Return value

```php
$this
```

#### Code

```php
public function progress(bool $blank = false){
	foreach ($this->progressions as $progression) {
		$progression->progress($blank);
	}
	return $this;
}
```

---

<a name="addProgressed" id="addProgressed"></a>
### public Group addProgressed(...$teams)

Marks **[Teams](/classes/team/)** as progressed.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$teams` | `array|Team` |  | A list of [teams](/classes/team/) which have already been progressed |

##### Return value

```php
$this
```

#### Code

```php
public function addProgressed(bool $blank = false){
	$this->progressed = array_merge($this->progressed, array_map(function ($a) {return $a->getId();}, array_filter($teams, function($a){return $a instanceof Team;})));
	foreach (array_filter($teams, function($a){return is_array($a);}) as $team) {
		$this->progressed = array_merge($this->progressed, array_map(function ($a) {return $a->getId();}, array_filter($team, function($a) {
			return ($a instanceof Team);
		})));
	}
	return $this;
}
```

---

<a name="isProgressed" id="isProgressed"></a>
### public bool isProgressed(Team $team)

Checks if **[Team](/classes/team/)** have already been progressed.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$team` | `Team` |  | [Teams](/classes/team/) to check |

##### Return value

```php
bool
```

#### Code

```php
public function isProgressed(Team $team) {
	return in_array($team->getId(), $this->progressed);
}
```

---

<a name="genGames" id="genGames"></a>
### public array genGames()

Generates all **[Games](/classes/game/)** from **[Group](/classes/group/)**.

##### Return value
```php
array of TournamentGenerator\Game
```
#### Code

```php
public function genGames() {
	$this->generator->genGames();
	return $this->games;
}
```

---

<a name="game" id="game"></a>
### public Game game(array $teams = [])

Creates and adds a new **[Game](/classes/game)** to **[Group](/classes/group/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$teams` | `array` | `[]` | List of [Teams](/classes/team/) playing the game |

##### Return value
```php
new TournamentGenerator\Game
```
#### Code

```php
public function game(array $teams = []) {
	$g = new Game($teams, $this);
	$this->games[] = $g;
	return $g;
}
```

---

<a name="addGame" id="addGame"></a>
### public Group addGame(...$games)

Adds **[Games](/classes/game)** to **[Group](/classes/group/)**.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$games` | `array|Game` | | [Games](/classes/game/) to add |

##### Return value
```php
$this
```
#### Code

```php
public function addGame(...$games){
	$this->games = array_merge($this->games, array_filter($games, function($a){ return ($a instanceof Game); }));

	foreach (array_filter($games, function($a){return is_array($a);}) as $key => $game) {
		$this->games = array_merge($this->games, array_filter($game, function($a){ return ($a instanceof Game); }));
	}
	return $this;
}
```

---

<a name="getGames" id="getGames"></a>
### public array getGames()

Gets an array of all **[Games](/classes/game/)** from **[Group](/classes/group/)**.

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

<a name="orderGames" id="orderGames"></a>
### public array orderGames()

Orders all **[Games](/classes/games/)** so the **[Teams](/classes/teams/)** doesn't play that many **[Games](/classes/games/)** after one other.

##### Return value
```php
array of TournamentGenerator\Game
```
#### Code

```php
public function orderGames() {
	if (count($this->games) <= 4) return $this->games;
	$this->games = $this->generator->orderGames();
	return $this->games;
}
```

---

<a name="simulate" id="simulate"></a>
### public array simulate(array $filters = [], bool $reset = true)

Simulate all **[Games](/classes/game/)** from this **[Group](/classes/group/)** just like it was really played.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$filters` | `array` | `[]` | Array of [TeamFilters](/classes/teamFilter/) to filter the teams |
| `$reset` | `bool` | `false` | If `true` only return the generated games and not store them |

##### Return value

```php
array of TournamentGenerator\Game
```

#### Code

```php
public function simulate(array $filters = [], bool $reset = true) {
	return Utilis\Simulator::simulateGroup($this, $filters, $reset);
}
```

---

<a name="resetGames" id="resetGames"></a>
### public Group resetGames()

Resets the results of all **[Games](/classes/game/)** in **[Group](/classes/group/)**.

##### Return value

```php
$this
```

#### Code

```php
public function resetGames() {
	foreach ($this->getGames() as $game) {
		$game->resetResults();
	}
	return $this;
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
	foreach ($this->games as $game) {
		if (!$game->isPlayed()) return false;
	}
	return true;
}
```

---
