<?php

declare(strict_types=1);

namespace TournamentGenerator;

use Exception;
use InvalidArgumentException;
use Stringable;
use TournamentGenerator\Helpers\FilterComparator;

/**
 * TeamFilter is a wrapper class for rules that filter teams.
 *
 * Filtering teams can be useful in progressions, or in getting teams that need to pass some condition.
 *
 * @since   0.1
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 */
class TeamFilter implements Stringable
{
    /**
     * @var string What to consider
     *
     * @details Available values:
     * * points
     * * score
     * * wins
     * * losses
     * * draws
     * * second
     * * third
     * * team
     * * not-progressed
     * * progressed
     */
    private readonly string $what;

    /**
     * @var string How to compare values
     *
     * @details Available values:
     * * >
     * * <
     * * >=
     * * <=
     * * =
     * * !=
     */
    private readonly string $how;

    /** @var int|Team Value */
    private readonly int|Team $val;

    /**
     * @var array|int[]|string[]
     */
    private readonly array $groups;

    /**
     * TeamFilter constructor.
     *
     * @param string   $what   What to compare
     * @param string   $how    How to compare values
     * @param int|Team $val    Value to compare to
     * @param Group[]  $groups Groups to get the statistics from
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $what = 'points', string $how = '>', int|Team $val = 0, array $groups = []) {
        if (!in_array(strtolower($what), ['points', 'score', 'wins', 'draws', 'losses', 'second', 'third', 'team', 'not-progressed', 'progressed'])) {
            throw new InvalidArgumentException('Trying to filter nonexistent type (' . $what . ')');
        }
        $this->what = strtolower($what);
        if (!in_array($how, ['>', '<', '>=', '<=', '=', '!='])) {
            throw new InvalidArgumentException('Trying to filter with nonexistent operator (' . $how . ')');
        }
        $this->how = $how;
        if (!(is_int($val) && 'team' !== strtolower($what)) && !($val instanceof Team && 'team' === strtolower($what))) {
            throw new InvalidArgumentException('Unsupported filter value type (' . gettype($val) . ')');
        }
        $this->val = $val;
        $this->groups = array_map(static fn ($a) : int|string => $a->getId(), array_filter($groups, static fn ($a) : true => $a instanceof Group));
    }

    /**
     * Returns filter description.
     */
    public function __toString() : string {
        return 'Filter: ' . $this->what . ' ' . ('not-progressed' !== $this->what && 'progressed' !== $this->what ? $this->how . ' ' . $this->val : '');
    }

    /**
     * Check if a team passes the filter.
     *
     * @param Team           $team      Team to check
     * @param int[]|string[] $groupsId  Ids of groups to consider
     * @param string         $operation Aggregate operation (sum, avg, max, min)
     * @param null|Group     $group     If checking for progression -> what group to check progression from
     *
     * @throws Exception
     */
    public function validate(Team $team, array $groupsId, string $operation = 'sum', ?Group $group = null) : bool {
        if (count($this->groups) > 0) {
            $groupsId = array_unique(array_merge($this->groups, $groupsId), SORT_REGULAR);
        }

        if ('team' === $this->what) {
            return '!=' === $this->how ? !$this->validateTeam($team) : $this->validateTeam($team);
        }

        if ('not-progressed' === $this->what) {
            return !$this->validateProgressed($team, $group);
        }

        if ('progressed' === $this->what) {
            return $this->validateProgressed($team, $group);
        }

        return $this->validateCalc($team, $groupsId, $operation);
    }

    /**
     * Validate a specific team.
     */
    protected function validateTeam(Team $team) : bool {
        return $this->val === $team;
    }

    /**
     * Check if a team is progressed from some group.
     *
     * @throws Exception
     */
    protected function validateProgressed(Team $team, ?Group $group = null) : bool {
        if (!$group instanceof Group) {
            throw new InvalidArgumentException('Group $from was not defined.');
        }

        return $group->isProgressed($team);
    }

    /**
     * Check a value using an aggregate operation.
     *
     * @param Team           $team      Team to check
     * @param int[]|string[] $groupsId  Groups' ids to aggregate from
     * @param string         $operation Aggregate operation (sum, avg, max, min)
     *
     * @throws Exception
     *
     * @see FilterComparator::compare()
     */
    protected function validateCalc(Team $team, array $groupsId, string $operation = 'sum') : bool {
        if (!in_array(strtolower($operation), ['sum', 'avg', 'max', 'min'])) {
            throw new InvalidArgumentException('Unknown operation of ' . $operation . '. Only "sum", "avg", "min", "max" possible.');
        }

        return FilterComparator::compare($operation, $this->val, $this->how, $this->what, $team, $groupsId);

    }

    public function getWhat() : string {
        return $this->what;
    }

    public function getHow() : string {
        return $this->how;
    }

    public function getVal() : int|Team {
        return $this->val;
    }

    /**
     * @return array|int[]|string[]
     */
    public function getGroups() : array {
        return $this->groups;
    }
}
