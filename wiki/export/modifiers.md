## Export modifiers

Modifiers are used to extend or control the exported data.

All modifiers must implement the `\TournamentGenerator\Export\Modifiers\Modifier` interface.

### Available modifiers

There is currently only one modifier.

#### WithScoresModifier

This modifier adds team's scores to the exported data.

##### Available exporters:

- `\TournamentGenerator\Export\Hierarchy\TeamsExporter`
- `\TournamentGenerator\Export\Single\TeamExporter`

##### Example

```php
use TournamentGenerator\Export\Single\TeamExporter;
use TournamentGenerator\Export\Hierarchy\TeamsExporter;
use TournamentGenerator\Group;
use TournamentGenerator\Team;

/** @var Team $team */
/** @var Group $group */

TeamExporter::start($team)->withScores()->get();
TeamsExporter::start($group)->withScores()->get();
```