<?php

declare(strict_types=1);

namespace TournamentGenerator\Helpers;

use TournamentGenerator\Team;

/**
 * Class responsible for processing filters.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.3
 */
class FilterComparator
{
    protected static string $what = '';
    protected static int $val = 0;

    /**
     * Compare a given team metric.
     *
     * @param string    $operation How to aggregate the values (sum, avg, max, min)
     * @param float|int $val       Value to compare to
     * @param string    $how       How to compare (<, >, <=, >=, =, !=)
     * @param string    $what      What team's metric to consider
     * @param Team      $team      Team to get the value from
     * @param array     $groupsId  What groups to consider
     */
    public static function compare(
        string $operation,
        float|int $val,
        string $how,
        string $what,
        Team $team,
        array $groupsId
    ) : bool {

        self::$what = $what;
        self::$val = $val;

        $return = false;

        $comp = self::calcComparisonValue($operation, $team, $groupsId);

        return match ($how) {
            '>'     => $comp > $val,
            '<'     => $comp < $val,
            '<='    => $comp <= $val,
            '>='    => $comp >= $val,
            '='     => $comp === $val,
            '!='    => $comp !== $val,
            default => $return,
        };
    }

    /**
     * Get the team's value to compare.
     *
     * @param string $operation How to aggregate the values (sum, avg, max, min)
     * @param Team   $team      Team to get the value from
     * @param array  $groupsId  What groups to consider
     */
    private static function calcComparisonValue(string $operation, Team $team, array $groupsId) : null|float|int {
        $return = 0;

        return match (strtolower($operation)) {
            'sum'   => self::calcSum($team, $groupsId),
            'avg'   => self::calcAvg($team, $groupsId),
            'max'   => self::calcMax($team, $groupsId),
            'min'   => self::calcMin($team, $groupsId),
            default => $return,
        };
    }

    /**
     * Calculate a sum of given metric.
     *
     * @param Team  $team     Team to get the values from
     * @param array $groupsId What groups to consider
     */
    private static function calcSum(Team $team, array $groupsId) : float|int {
        $sum = 0;
        foreach ($groupsId as $groupId) {
            if (isset($team->groupResults[$groupId])) {
                $sum += $team->groupResults[$groupId][self::$what];
            }
        }

        return $sum;
    }

    /**
     * Calculate a average of given metric.
     *
     * @param Team  $team     Team to get the values from
     * @param array $groupsId What groups to consider
     */
    private static function calcAvg(Team $team, array $groupsId) : float|int {
        $games = 0;
        foreach ($groupsId as $groupId) {
            $games += count($team->getGames(null, $groupId));
        }

        return self::calcSum($team, $groupsId) / $games;
    }

    /**
     * Find a maximum of given metric.
     *
     * @param Team  $team     Team to get the values from
     * @param array $groupsId What groups to consider
     */
    private static function calcMax(Team $team, array $groupsId) : float|int {
        $max = null;
        if (1 === count($groupsId) && in_array(self::$what, ['score', 'points'])) {
            $games = $team->getGames(null, reset($groupsId));
            foreach ($games as $game) {
                $results = $game->getResults()[$team->getId()];
                if ($results[self::$what] > $max || null === $max) {
                    $max = $results[self::$what];
                }
            }

            return $max;
        }
        foreach ($groupsId as $groupId) {
            if (isset($team->groupResults[$groupId]) && ($team->groupResults[$groupId][self::$what] > $max || null === $max)) {
                $max = $team->groupResults[$groupId][self::$what];
            }
        }

        return $max;
    }

    /**
     * Find a minimum of given metric.
     *
     * @param Team  $team     Team to get the values from
     * @param array $groupsId What groups to consider
     */
    private static function calcMin(Team $team, array $groupsId) : float|int {
        $min = null;
        if (1 === count($groupsId) && in_array(self::$what, ['score', 'points'])) {
            $games = $team->getGames(null, reset($groupsId));
            foreach ($games as $game) {
                $results = $game->getResults()[$team->getId()];
                if ($results[self::$what] < $min || null === $min) {
                    $min = $results[self::$what];
                }
            }

            return $min;
        }
        foreach ($groupsId as $groupId) {
            if (isset($team->groupResults[$groupId]) && ($team->groupResults[$groupId][self::$what] < $min || null === $min)) {
                $min = $team->groupResults[$groupId][self::$what];
            }
        }

        return $min;
    }
}
