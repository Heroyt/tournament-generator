##Introduction

Simulating a tournament|category|round|group can help you to generate in advance, or just to test your bracket.

There are two types of simulating a tournament

1. Simulaing without results
	- This is used only to generate brackets for example to save them for later
	- It simulates each group being played, progresses blank teams and then resets the results
2. Simulating with results = *real*
	- This is used to test out your bracket and scoring
	- It generates random results for each game, progressing groups with normal teams and finishing the whole tournament like it was played for real

##Simulating round or group

**[Round](/classes/round/)** and **[Group](/classes/group/)** has a `simulate()` method which by default simulates its games with results. The `simulate()` method will however not generate any games, it will only generate random results for all the games. You have to generate the games beforehand.

```php
$group->simulate();
$round->simulate();
```

And if you want to progress them and then reset the results, you can do this with a `progress()` and `resetGames()` methods.

Please note that if you want to progress blank teams, you have to give `progressed()` an argument of `true`;

```php
$group->progress(true)->resetGames();
$round->progress(true)->resetGames();
```

This will progress blank teams and reset all the results.

---

##Simulating the whole tournament or category

**[Tournament](/classes/tournament/)** and **[Category](/classes/category/)** has a build in methods to simulate itself with or without results. These methods will also generate all necessary games.

```php
$games = $tournament->genGamesSimulate();
$games = $category->genGamesSimulate();
```

This will generate all the results, games, progress blank teams and then reset all results.

---

```php
$games = $tournament->genGamesSimulateReal();
$games = $category->genGamesSimulateReal();
```

This will generate all the results, games, progress normal teams and keep the results.
