## Game containers

Game containers are specialized containers for **Game** objects. Apart from normal **BaseContainer** features, it allows for automatic game ID setting. This id increment is propagated through the whole container hierarchy.

```php
use TournamentGenerator\Containers\GameContainer;
use TournamentGenerator\Game;
use TournamentGenerator\Group;
use TournamentGenerator\Team;

$parentContainer = new GameContainer(1);

$childContainer1 = new GameContainer(2);
$childContainer2 = new GameContainer(3);
$parentContainer->addChild($childContainer1, $childContainer2);

$parentContainer->getAutoIncrement(); // 1
$childContainer1->getAutoIncrement(); // 1
$childContainer2->getAutoIncrement(); // 1

/** @var Team $team1 */
/** @var Team $team2 */
/** @var Group $group */

$game = new Game([$team1, $team2], $group);
$game->setId($childContainer1->getAutoIncrement()); // Set game's id to 1
$childContainer1->insert($game);
$childContainer1->incrementId();

$parentContainer->getAutoIncrement(); // 2
$childContainer1->getAutoIncrement(); // 2
$childContainer2->getAutoIncrement(); // 2

$childContainer2->incrementId();

$parentContainer->getAutoIncrement(); // 3
$childContainer1->getAutoIncrement(); // 3
$childContainer2->getAutoIncrement(); // 3
```

### HasGames objects

Objects that implement the **HasGames** interface will have a game container.

```php
use TournamentGenerator\Round;
use TournamentGenerator\Team;

$round = new Round();
$groupA = $round->group('A');
$groupB = $round->group('B');

// $round, $groupA and $groupB all have a GameContainer that are linked the same as the objects itself

$round->getGameContainer()->getAutoIncrement(); // 1
$groupA->getGameContainer()->getAutoIncrement(); // 1
$groupB->getGameContainer()->getAutoIncrement(); // 1

/** @var Team $team1 */
/** @var Team $team2 */

$game = $groupA->game([$team1, $team2]); // This automatically sets the game's id and auto-increments
$game->getId(); // 1

$round->getGameContainer()->getAutoIncrement(); // 2
$groupA->getGameContainer()->getAutoIncrement(); // 2
$groupB->getGameContainer()->getAutoIncrement(); // 2
```

### Setting the autoincrement start

If your database already has some games stored, you can pass its autoincrement value to the **GameContainer**.

```php
use TournamentGenerator\Containers\GameContainer;

$container = new GameContainer(1);
$container->setAutoIncrement(999);

$container->getAutoIncrement(); // 999
$container->incrementId();
$container->getAutoIncrement(); // 1000
```