## Export - Introduction

**TournamentGenerator** provides a simple classes to help export your tournament and save it. This could be used to create a persistent storage for your **Tournament**.

There are 2 main types of exporters available: `\TournamentGenerator\Export\Hierarchy` and `\TournamentGenerator\Export\Single`.

The **Hierarchy** takes care of exporting multiple objects at once. It can export setup of any **HierarchyBase** object, its games and teams. There is a general **Exporter** class, that can export any one of these, and 3 specialized exporters: **GamesExporter**, **TeamsExporter** and **SetupsExporter**.

The **Single** exports only one object at a time. Currently, you can export a **Game** using a **GameExporter**, and a **Team** using a **TeamExporter**.

### Basic export example

```php
use TournamentGenerator\Tournament;
use TournamentGenerator\Export\Hierarchy\Exporter;

$tournament = new Tournament('My tournament');

// Some tournament setup...

$export = Exporter::export($tournament); // This will export all teams and games from the tournament
$export = $tournament->export()->get(); // This will also export all teams and games from the tournament

print_r($export);
```

Will print something like this:

```
Array
(
    [teams] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 0
                    [name] => Team 0
                )

            [1] => stdClass Object
                (
                    [id] => 1
                    [name] => Team 1
                )

            [2] => stdClass Object
                (
                    [id] => 2
                    [name] => Team 2
                )
        )

    [games] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 1
                    [teams] => Array
                        (
                            [0] => 0
                            [1] => 1
                        )

                    [scores] => Array
                        (
                            [0] => Array
                                (
                                    [score] => 100
                                    [points] => 0
                                    [type] => loss
                                )

                            [1] => Array
                                (
                                    [score] => 200
                                    [points] => 3
                                    [type] => win
                                )

                        )

                )
                
            [1] => stdClass Object
                (
                    [id] => 2
                    [teams] => Array
                        (
                            [0] => 0
                            [1] => 2
                        )

                    [scores] => Array
                        (
                            [0] => Array
                                (
                                    [score] => 300
                                    [points] => 3
                                    [type] => win
                                )

                            [2] => Array
                                (
                                    [score] => 200
                                    [points] => 0
                                    [type] => loss
                                )

                        )

                )

            [2] => stdClass Object
                (
                    [id] => 3
                    [teams] => Array
                        (
                            [0] => 1
                            [1] => 2
                        )

                    [scores] => Array
                        (
                            [1] => Array
                                (
                                    [score] => 0
                                    [points] => 0
                                    [type] => loss
                                )

                            [2] => Array
                                (
                                    [score] => 100
                                    [points] => 3
                                    [type] => win
                                )

                        )

                )

        )

)
```

### Export JSON

You can also export your tournament as JSON string.

```php
use TournamentGenerator\Tournament;
use TournamentGenerator\Export\Hierarchy\Exporter;

$tournament = new Tournament('My tournament');

// Some tournament setup...

$export = Exporter::start($tournament)->getJson();
$export = $tournament->export()->getJson();
```

### Export with setup


```php
use TournamentGenerator\Tournament;
use TournamentGenerator\Export\Hierarchy\Exporter;

$tournament = new Tournament('My tournament');

// Some tournament setup...

$export = Exporter::start($tournament)->withSetup()->get(); // This will export all teams, games and setup from the tournament
$export = $tournament->export()->withSetup()->get(); // This will also export all teams, games and setup from the tournament

print_r($export);
```

This will print something like this:

```
Array
(
    [teams] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 0
                    [name] => Team 0
                )

            [1] => stdClass Object
                (
                    [id] => 1
                    [name] => Team 1
                )

            [2] => stdClass Object
                (
                    [id] => 2
                    [name] => Team 2
                )

            [3] => stdClass Object
                (
                    [id] => 3
                    [name] => Team 3
                )

            [4] => stdClass Object
                (
                    [id] => 4
                    [name] => Team 4
                )

            [5] => stdClass Object
                (
                    [id] => 5
                    [name] => Team 5
                )

            [6] => stdClass Object
                (
                    [id] => 6
                    [name] => Team 6
                )

            [7] => stdClass Object
                (
                    [id] => 7
                    [name] => Team 7
                )

        )

    [games] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 1
                    [teams] => Array
                        (
                            [0] => 0
                            [1] => 1
                        )

                    [scores] => Array
                        (
                            [0] => Array
                                (
                                    [score] => 100
                                    [points] => 0
                                    [type] => loss
                                )

                            [1] => Array
                                (
                                    [score] => 200
                                    [points] => 3
                                    [type] => win
                                )

                        )

                )

            [1] => stdClass Object
                (
                    [id] => 2
                    [teams] => Array
                        (
                            [0] => 2
                            [1] => 3
                        )

                    [scores] => Array
                        (
                            [2] => Array
                                (
                                    [score] => 0
                                    [points] => 0
                                    [type] => loss
                                )

                            [3] => Array
                                (
                                    [score] => 500
                                    [points] => 3
                                    [type] => win
                                )

                        )

                )

            [2] => stdClass Object
                (
                    [id] => 3
                    [teams] => Array
                        (
                            [0] => 0
                            [1] => 2
                        )

                    [scores] => Array
                        (
                            [0] => Array
                                (
                                    [score] => 300
                                    [points] => 3
                                    [type] => win
                                )

                            [2] => Array
                                (
                                    [score] => 200
                                    [points] => 0
                                    [type] => loss
                                )

                        )

                )

            [3] => stdClass Object
                (
                    [id] => 4
                    [teams] => Array
                        (
                            [0] => 1
                            [1] => 3
                        )

                    [scores] => Array
                        (
                            [1] => Array
                                (
                                    [score] => 200
                                    [points] => 1
                                    [type] => draw
                                )

                            [3] => Array
                                (
                                    [score] => 200
                                    [points] => 1
                                    [type] => draw
                                )

                        )

                )

            [4] => stdClass Object
                (
                    [id] => 5
                    [teams] => Array
                        (
                            [0] => 0
                            [1] => 3
                        )

                    [scores] => Array
                        (
                            [0] => Array
                                (
                                    [score] => 800
                                    [points] => 3
                                    [type] => win
                                )

                            [3] => Array
                                (
                                    [score] => 200
                                    [points] => 0
                                    [type] => loss
                                )

                        )

                )

            [5] => stdClass Object
                (
                    [id] => 6
                    [teams] => Array
                        (
                            [0] => 1
                            [1] => 2
                        )

                    [scores] => Array
                        (
                            [1] => Array
                                (
                                    [score] => 0
                                    [points] => 0
                                    [type] => loss
                                )

                            [2] => Array
                                (
                                    [score] => 100
                                    [points] => 3
                                    [type] => win
                                )

                        )

                )

            [6] => stdClass Object
                (
                    [id] => 7
                    [teams] => Array
                        (
                            [0] => 4
                            [1] => 5
                        )

                    [scores] => Array
                        (
                            [4] => Array
                                (
                                    [score] => 100
                                    [points] => 1
                                    [type] => draw
                                )

                            [5] => Array
                                (
                                    [score] => 100
                                    [points] => 1
                                    [type] => draw
                                )

                        )

                )

            [7] => stdClass Object
                (
                    [id] => 8
                    [teams] => Array
                        (
                            [0] => 6
                            [1] => 7
                        )

                    [scores] => Array
                        (
                            [6] => Array
                                (
                                    [score] => 0
                                    [points] => 0
                                    [type] => loss
                                )

                            [7] => Array
                                (
                                    [score] => 500
                                    [points] => 3
                                    [type] => win
                                )

                        )

                )

            [8] => stdClass Object
                (
                    [id] => 9
                    [teams] => Array
                        (
                            [0] => 4
                            [1] => 6
                        )

                    [scores] => Array
                        (
                            [4] => Array
                                (
                                    [score] => 100
                                    [points] => 1
                                    [type] => draw
                                )

                            [6] => Array
                                (
                                    [score] => 100
                                    [points] => 1
                                    [type] => draw
                                )

                        )

                )

            [9] => stdClass Object
                (
                    [id] => 10
                    [teams] => Array
                        (
                            [0] => 5
                            [1] => 7
                        )

                    [scores] => Array
                        (
                            [5] => Array
                                (
                                    [score] => 800
                                    [points] => 3
                                    [type] => win
                                )

                            [7] => Array
                                (
                                    [score] => 0
                                    [points] => 0
                                    [type] => loss
                                )

                        )

                )

            [10] => stdClass Object
                (
                    [id] => 11
                    [teams] => Array
                        (
                            [0] => 4
                            [1] => 7
                        )

                    [scores] => Array
                        (
                            [4] => Array
                                (
                                    [score] => 600
                                    [points] => 3
                                    [type] => win
                                )

                            [7] => Array
                                (
                                    [score] => 200
                                    [points] => 0
                                    [type] => loss
                                )

                        )

                )

            [11] => stdClass Object
                (
                    [id] => 12
                    [teams] => Array
                        (
                            [0] => 5
                            [1] => 6
                        )

                    [scores] => Array
                        (
                            [5] => Array
                                (
                                    [score] => 300
                                    [points] => 3
                                    [type] => win
                                )

                            [6] => Array
                                (
                                    [score] => 200
                                    [points] => 0
                                    [type] => loss
                                )

                        )

                )

        )

    [tournament] => stdClass Object
        (
            [type] => general
            [name] => Tournament
            [skip] => 
            [timing] => stdClass Object
                (
                    [play] => 0
                    [gameWait] => 0
                    [categoryWait] => 0
                    [roundWait] => 0
                    [expectedTime] => 0
                )

            [categories] => Array
                (
                )

            [rounds] => Array
                (
                    [0] => 1
                    [1] => 2
                )

            [groups] => Array
                (
                    [0] => 1
                    [1] => 2
                    [2] => 3
                    [3] => 4
                )

            [teams] => Array
                (
                    [0] => 0
                    [1] => 1
                    [2] => 2
                    [3] => 3
                    [4] => 4
                    [5] => 5
                    [6] => 6
                    [7] => 7
                )

            [games] => Array
                (
                    [0] => 1
                    [1] => 2
                    [2] => 3
                    [3] => 4
                    [4] => 5
                    [5] => 6
                    [6] => 7
                    [7] => 8
                    [8] => 9
                    [9] => 10
                    [10] => 11
                    [11] => 12
                )

        )

    [categories] => Array
        (
        )

    [rounds] => Array
        (
            [1] => stdClass Object
                (
                    [id] => 1
                    [name] => Round 1
                    [skip] => 
                    [played] => 1
                    [groups] => Array
                        (
                            [0] => 1
                            [1] => 2
                        )

                    [teams] => Array
                        (
                            [0] => 0
                            [1] => 1
                            [2] => 2
                            [3] => 3
                            [4] => 4
                            [5] => 5
                            [6] => 6
                            [7] => 7
                        )

                    [games] => Array
                        (
                            [0] => 1
                            [1] => 2
                            [2] => 3
                            [3] => 4
                            [4] => 5
                            [5] => 6
                            [6] => 7
                            [7] => 8
                            [8] => 9
                            [9] => 10
                            [10] => 11
                            [11] => 12
                        )

                )

            [2] => stdClass Object
                (
                    [id] => 2
                    [name] => Round 2
                    [skip] => 
                    [played] => 
                    [groups] => Array
                        (
                            [0] => 3
                            [1] => 4
                        )

                    [teams] => Array
                        (
                        )

                    [games] => Array
                        (
                        )

                )

        )

    [groups] => Array
        (
            [1] => stdClass Object
                (
                    [id] => 1
                    [name] => Group 1
                    [type] => Robin-Robin group type
                    [skip] => 
                    [points] => stdClass Object
                        (
                            [win] => 3
                            [loss] => 0
                            [draw] => 1
                            [second] => 2
                            [third] => 1
                            [progression] => 50
                        )

                    [played] => 1
                    [inGame] => 2
                    [maxSize] => 4
                    [teams] => Array
                        (
                            [0] => 0
                            [1] => 1
                            [2] => 2
                            [3] => 3
                        )

                    [games] => Array
                        (
                            [0] => 1
                            [1] => 2
                            [2] => 3
                            [3] => 4
                            [4] => 5
                            [5] => 6
                        )

                )

            [2] => stdClass Object
                (
                    [id] => 2
                    [name] => Group 2
                    [type] => Robin-Robin group type
                    [skip] => 
                    [points] => stdClass Object
                        (
                            [win] => 3
                            [loss] => 0
                            [draw] => 1
                            [second] => 2
                            [third] => 1
                            [progression] => 50
                        )

                    [played] => 1
                    [inGame] => 2
                    [maxSize] => 4
                    [teams] => Array
                        (
                            [0] => 4
                            [1] => 5
                            [2] => 6
                            [3] => 7
                        )

                    [games] => Array
                        (
                            [0] => 7
                            [1] => 8
                            [2] => 9
                            [3] => 10
                            [4] => 11
                            [5] => 12
                        )

                )

            [3] => stdClass Object
                (
                    [id] => 3
                    [name] => Group 3
                    [type] => Robin-Robin group type
                    [skip] => 
                    [points] => stdClass Object
                        (
                            [win] => 3
                            [loss] => 0
                            [draw] => 1
                            [second] => 2
                            [third] => 1
                            [progression] => 50
                        )

                    [played] => 
                    [inGame] => 2
                    [maxSize] => 4
                    [teams] => Array
                        (
                        )

                    [games] => Array
                        (
                        )

                )

            [4] => stdClass Object
                (
                    [id] => 4
                    [name] => Group 4
                    [type] => Robin-Robin group type
                    [skip] => 
                    [points] => stdClass Object
                        (
                            [win] => 3
                            [loss] => 0
                            [draw] => 1
                            [second] => 2
                            [third] => 1
                            [progression] => 50
                        )

                    [played] => 
                    [inGame] => 2
                    [maxSize] => 4
                    [teams] => Array
                        (
                        )

                    [games] => Array
                        (
                        )

                )

        )

    [progressions] => Array
        (
            [0] => stdClass Object
                (
                    [from] => 1
                    [to] => 3
                    [offset] => 0
                    [length] => 2
                    [progressed] => 
                    [filters] => Array
                        (
                        )

                )

            [1] => stdClass Object
                (
                    [from] => 1
                    [to] => 4
                    [offset] => -2
                    [length] => 
                    [progressed] => 
                    [filters] => Array
                        (
                        )

                )

            [2] => stdClass Object
                (
                    [from] => 2
                    [to] => 3
                    [offset] => 0
                    [length] => 2
                    [progressed] => 
                    [filters] => Array
                        (
                        )

                )

            [3] => stdClass Object
                (
                    [from] => 2
                    [to] => 4
                    [offset] => -2
                    [length] => 
                    [progressed] => 
                    [filters] => Array
                        (
                        )

                )

        )

)
```