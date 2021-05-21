
##Introduction

Filters are special classes used in order to filter out teams only by some given criteria.

You can apply filters to `getTeams()` and `sortTeams()` methods and **[Progressions](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Progression.html)**.

---

##Creating a filter

```php
$filter = new \TournamentGenerator\TeamFilter($criteria = 'points', $comparator = '>', $value = 0, $groups = [])
```

* **$criteria**
	- what to filter by
* **$comparator**
	- `>` greater than
	- `<` less than
	- `>=` greater or equal than
	- `<=` less or equal then
	- `=` equal to
	- `!=` not equal to
* **$value**
* **$groups**
	- array of groups to consider results from
	- other groups will be ignored

---

##List of criteria

List of criteria to filter teams by

| Criteria | Description |
| :------: | :---------: |
| points | points acquired |
| score | score acquired |
| wins | number of wins |
| losses | number of losses |
| draws | number of draws |
| second | number of times where the team was second (at least 3 teams in game) |
| third | number of times where the team was third (at least 4 teams in game) |
| team | filter a specific team |
| not-progressed | filter only teams that have not been progressed yet from certain group |
| progressed | filter only teams that have been progressed from certain group |

---

##Using a filter

###Getting teams

A simple filter might be used in order to get for example only the teams that have won at least 3 times

```php

$filter = new \TournamentGenerator\TeamFilter('wins', '>=', 3, $tournament->getGroups());

$filteredTeams = $tournament->getTeams(false, null, [$filter]);

```

This will filter all teams in a tournament and give you only the ones that satisfy given filter.

<a title="progressions" id="progressions"></a>
###Progressions

You can also use filters to progress teams. Imagine a situation where you progress 2 best teams from one group, and you want to progress all others to another. We can do this, no matter the number of teams with a filter.

```php

$group->progression($nextGroup, 0, 2); // Setup progression to progress only the best 2 teams
$group->progress(); // Progress the teams

$filter = new \TournamentGenerator\TeamFilter('not-progressed', '', 0, [$group]); // Setup the filter

$group->progression($anotherGroup)->addFilter($filter); // Setup a empty progression with a filter
$group->progress(); // Progress the teams

```

This will move 2 best teams from `$group` to `$nextGroup` and all others to `$anotherGroup`;

---

##More complex filters

If you need to, you can also combine filters to create more complex filters. This uses a helper **[Filter class](https://heroyt.github.io/tournament-generator/classes/TournamentGenerator-Helpers-Filter.html)**.

One way of combining filters is creating a list of them. Then it will require the teams to satisfy all of them.

```php

$filter1 = new \TournamentGenerator\TeamFilter('wins', '>', 2, $tournament->getGroups()); // More than 2 wins
$filter2 = new \TournamentGenerator\TeamFilter('losses', '<', 4, $tournament->getGroups()); // Less than 4 losses

$filteredTeams = $tournament->getTeams(false, null, [$filter1, $filter2]);

```

This will filter only the teams that have more than 2 wins **and** less than 4 losses.

---

If you want to combine the filters in much more complex way, you can create a multi-dimensional array of your filters using **and** and **or** keys;

```php

$filter1 = new \TournamentGenerator\TeamFilter('wins', '>', 2, $tournament->getGroups()); // More than 2 wins
$filter2 = new \TournamentGenerator\TeamFilter('losses', '<', 4, $tournament->getGroups()); // Less than 4 losses
$filter3 = new \TournamentGenerator\TeamFilter('score', '>=', 400, $tournament->getGroups()); // More or equal than 400 score

$filteredTeams = $tournament->getTeams(false, null, [
	'and' => [
		'or' => [$filter1, $filter2],
		$filter3
	]
]);

```

This will give you all the teams that have more than 400 score **and** more than 2 wins **or** less than 4 losses.

If you ever want to filter teams with double **or** or **and**, you can wrap it in an array like so.

```php
$filteredTeams = $tournament->getTeams(false, null, [
	'and' => [
		['or' => [$filter1, $filter2]],
		['or' => [$filter3, $filter4]]
	]
]);
```

### Applying more complex filters to progressions

If you want to add complex filters to progressions you can just add more with:

```php
$group->progression($anotherGroup)->addFilter($filter1, $filter2);
```

and this filter teams that satisfy both: filter1 **and** filter2.

---

However, if you want to use complex filters with set operators, you have to use `setFilters()` method.

```php
$group->progression($anotherGroup)->setFilters([
	'and' => [
		'or' => [$filter1, $filter2],
		$filter3
	]
]);
```
