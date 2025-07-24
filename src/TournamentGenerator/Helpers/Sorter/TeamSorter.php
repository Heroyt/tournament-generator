<?php

declare(strict_types=1);

namespace TournamentGenerator\Helpers\Sorter;

use InvalidArgumentException;
use TournamentGenerator\Constants;
use TournamentGenerator\Containers\BaseContainer;
use TournamentGenerator\Team;

/**
 * TournamentGenerator sorter for teams.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.3
 */
class TeamSorter implements BaseSorter
{
    /** @var int[]|string[] Array of Group ids */
    protected static array $ids;

    /** @var string What to sort by */
    protected string $ordering;

    /**
     * TeamSorter constructor.
     *
     * @param string $ordering What to order by (\TournamentGenerator\Constants::POINTS /
     *                         \TournamentGenerator\Constants::SCORE)
     *
     * @throws InvalidArgumentException
     */
    public function __construct(protected BaseContainer $container, string $ordering = Constants::POINTS) {
        if (!in_array($ordering, Constants::OrderingTypes, true)) {
            throw new InvalidArgumentException('Unknown ordering type `' . $ordering . '`');
        }
        $this->ordering = $ordering;
    }

    /**
     * Sorter function for usort by points.
     *
     * @param Team $a First team
     * @param Team $b Second team
     */
    protected static function sortTeamsByPoints(Team $a, Team $b) : int {
        $groupsIds = self::$ids;
        if ($a->sumPoints($groupsIds) === $b->sumPoints($groupsIds) && $a->sumScore($groupsIds) === $b->sumScore(
            $groupsIds
        )) {
            return 0;
        }
        if ($a->sumPoints($groupsIds) === $b->sumPoints($groupsIds)) {
            return $a->sumScore($groupsIds) > $b->sumScore($groupsIds) ? -1 : 1;
        }

        return $a->sumPoints($groupsIds) > $b->sumPoints($groupsIds) ? -1 : 1;
    }

    /**
     * Sorter function for usort by score.
     *
     * @param Team $a First team
     * @param Team $b Second team
     */
    protected static function sortTeamsByScore(Team $a, Team $b) : int {
        $groupsIds = self::$ids;

        return $b->sumScore($groupsIds) <=> $a->sumScore($groupsIds);
    }

    /**
     * Sorter function for usort by seed.
     *
     * @param Team $a First team
     * @param Team $b Second team
     */
    protected static function sortTeamsBySeed(Team $a, Team $b) : int {
        return $b->getSeed() <=> $a->getSeed();
    }

    /**
     * Sort function to call.
     */
    public function sort(array $data) : array {
        $this::$ids = $this->container->getLeafIds();
        match ($this->ordering) {
            Constants::POINTS => usort($data, [self::class, 'sortTeamsByPoints']),
            Constants::SCORE  => usort($data, [self::class, 'sortTeamsByScore']),
            Constants::SEED   => usort($data, [self::class, 'sortTeamsBySeed']),
            default           => $data,
        };

        return $data;
    }
}
