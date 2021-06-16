<?php

namespace TournamentGenerator;

/**
 * Constants used in the library
 *
 * @package TournamentGenerator
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.3
 */
class Constants
{
	/** @var string Dummy team used in bracket generation */
	public const DUMMY_TEAM = 'dummyRozlosTeam';

	/** @var string Robin - Robin style bracket */
	public const ROUND_ROBIN = 'Robin-Robin group type';

	/** @var string One team plays only one game in a group bracket */
	public const ROUND_TWO = 'Two-Two group type';

	/** @var string Split into two groups if number of teams exceeds maximum size */
	public const ROUND_SPLIT = 'Conditional split group type';

	/** @var string Order teams based on their points acquired by winning, losing,.. */
	public const POINTS = 'Ordering based on points';

	/** @var string Order teams based on their score acquired in each game */
	public const SCORE = 'Ordering based on score';

	/** @var array List of all available order by types */
	public const OrderingTypes = [
		'POINTS' => 'Ordering based on points',
		'SCORE'  => 'Ordering based on score'
	];

	/** @var array List of all available group bracket types */
	public const GroupTypes = [
		Constants::ROUND_ROBIN,
		Constants::ROUND_TWO,
		Constants::ROUND_SPLIT,
	];
}
