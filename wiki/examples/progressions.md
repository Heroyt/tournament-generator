##Introduction

**[Progressions](classes/progression/)** are used in order to move teams from one group to another. This can be used to progress the winning teams to semi-finals and finals round, but you can also progress teams between groups in different categories and even tournaments if you ever needed to.

---

##Creating a progression

**[Progression](classes/progression/)** always takes teams from a group and sorts them by their results in that group. It works the same and uses an [array_splice()](https://www.php.net/manual/en/function.array-splice.php) function.

```php
$progression = new \TournamentGenerator\Progression($from, $to, $start = 0, $length = count($from->teams));
$group->addProgression($progression);
```

But the **recommended** way is to initialize a **[Progression](classes/progression/)** class straight on the **[Group](classes/group/)** class.

```php
$group->progression($to, $start = 0, $length = count($group->teams));
```

* **$from**
	- group to progress from
* **$to**
	- group to progress to
* **$start**
	- offset to start picking teams
	- if the offset is **positive** then the start of the progressed portion is at that offset from the beginning of the teams array
	- if the offset is **negative** then the start of the progressed portion is at that offset from the end of the teams array
* **$length**
	- how many teams to progress from the offset
	- If length is **omitted**, removes everything from offset to the end of the teams array
	- If length is specified and is **positive**, then that many teams	 will be progressed
	- If length is specified and is **negative**, then the end of the progressed portion will be that many teams	 from the end of the teams array
	- If length is specified and is **zero**, no teams will be progressed

##Using a progression

Once you setup a **[Progression](classes/progression/)** on a **[Group](classes/group/)**, you can call `progress()` method an the **[Group](classes/group/)** and all teams will be moved based on the specified rules.

```php

$group->progression($finalGroup, 0, 2); // Progress 2 best teams

/*
Play the first group
*/

$group->progress();  // Progress all the teams based on their results in $group

/*
$finalGroup now has 2 teams from $group and can be played
*/

```

##Progressing with blank teams

Blank teams are just "team frames" that are used in generating a bracket in advance. They occupy an empty space in a game and can be used to only generate and save the bracket without any results being saved.

You can make a progression create these blank teams on `progress()` method just by giving it an argument `true`;
```php
$group->progress(true);
```

##Using filters and progression

You can also use filters in **[Progressions](classes/progression/)**.

Please refer to [Filters example](/examples/filters/#progressions)
