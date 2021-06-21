## Hierarchy containers

Hierarchy containers are specialized containers for **HierarchyBase** objects. Apart from normal **BaseContainer** features, it allows for getting only a specific hierarchy level.

**HierarchyBase** objects are **Tournament**, **Category**, **Round** and **Group**.

### Getting a specific hierarchy level

```php
use TournamentGenerator\Tournament;
use TournamentGenerator\Category;
use TournamentGenerator\Round;

// First, create a tournament hierarchy. This hierarchy is stored in HierarchyContainer objects in each HierarchyBase object.
$tournament = new Tournament();

$category1 = $tournament->category('Beginners');
$category2 = $tournament->category('Advanced');

$round1 = $category1->round('Round 1');
$round2 = $category1->round('Round 2');
$round3 = $category2->round('Round 1');
$round4 = $category2->round('Round 2');

$group1 = $round1->group('A');
$group2 = $round1->group('B');
$group3 = $round2->group('C');
$group4 = $round2->group('D');
$group5 = $round3->group('A');
$group6 = $round3->group('B');
$group7 = $round4->group('C');
$group8 = $round4->group('D');

// Get all categories from a tournament
$tournament->getCategories();
$tournament
  ->getContainer()
  ->getHierarchyLevel(Category::class);
$tournament
  ->getContainer()
  ->getHierarchyLevelQuery(Category::class)
  // Modifiers
  ->get();

// Get all rounds from a tournament
$tournament->getRounds();
$tournament
  ->getContainer()
  ->getHierarchyLevel(Round::class);
$tournament
  ->getContainer()
  ->getHierarchyLevelQuery(Round::class)
  // Modifiers
  ->get();
```

### Getting hierarchy level type

```php

use TournamentGenerator\Tournament;
use TournamentGenerator\Category;
use TournamentGenerator\Round;
use TournamentGenerator\Group;

// First, create a tournament hierarchy. This hierarchy is stored in HierarchyContainer objects in each HierarchyBase object.
$tournament = new Tournament();

$category1 = $tournament->category('Beginners');
$category2 = $tournament->category('Advanced');

$round1 = $category1->round('Round 1');
$round2 = $category1->round('Round 2');
$round3 = $category2->round('Round 1');
$round4 = $category2->round('Round 2');

$group1 = $round1->group('A');
$group2 = $round1->group('B');
$group3 = $round2->group('C');
$group4 = $round2->group('D');
$group5 = $round3->group('A');
$group6 = $round3->group('B');
$group7 = $round4->group('C');
$group8 = $round4->group('D');

$tournament->getContainer()->getLevelType(); // Category::class
$category1->getContainer()->getLevelType(); // Round::class
$round1->getContainer()->getLevelType(); // Group::class
```