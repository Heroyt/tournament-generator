<?php

declare(strict_types=1);

namespace TournamentGenerator;

/**
 * Constants used in the library.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.3
 */
class Constants
{
    /** @var string Dummy team used in bracket generation */
    public const string DUMMY_TEAM = 'dummyRozlosTeam';

    /** @var string Robin - Robin style bracket */
    public const string ROUND_ROBIN = 'Robin-Robin group type';

    /** @var string One team plays only one game in a group bracket */
    public const string ROUND_TWO = 'Two-Two group type';

    /** @var string Split into two groups if number of teams exceeds maximum size */
    public const string ROUND_SPLIT = 'Conditional split group type';

    /** @var string Order teams based on their points acquired by winning, losing,.. */
    public const string POINTS = 'Ordering based on points';

    /** @var string Order teams based on their score acquired in each game */
    public const string SCORE = 'Ordering based on score';

    /** @var string Order teams based on their seed points */
    public const string SEED = 'Ordering based on seed';

    /** @var array<string,string> List of all available order by types */
    public const array OrderingTypes = [
        'POINTS' => 'Ordering based on points',
        'SCORE'  => 'Ordering based on score',
        'SEED'   => 'Ordering based on seed',
    ];

    /** @var string[] List of all available group bracket types */
    public const array GroupTypes = [
        Constants::ROUND_ROBIN,
        Constants::ROUND_TWO,
        Constants::ROUND_SPLIT,
    ];
}
