<?php

namespace TournamentGenerator;

/**
 *
 */
class Constants
{
	/** @var string Round robin style tournament */
	public const DUMMY_TEAM = 'dummyRozlosTeam';

	/** @var string Robin - Robin style bracket */
	public const ROUND_ROBIN = 'Robin-Robin group type';

	/** @var string One team plays only one game in a group bracket */
	public const ROUND_TWO = 'Two-Two group type';

	 /** @var string Split into two groups if number of teams exceedes maximum size */
	public const ROUND_SPLIT = 'Conditional split group type';

	/** @var string Order teams based on their points aquired by winning, losing,.. */
	public const POINTS = 'Ordering based on points';

	/** @var string Order teams based on their score aquired in each game */
	public const SCORE = 'Ordering based on score';

	/** @var string List of all available order by types */
	public const OrderingTypes = [
		'POINTS' => 'Ordering based on points',
		'SCORE' => 'Ordering based on score'
	];

	/** @var string List of all available group bracket types */
	public const GroupTypes = [
		'ROUND_ROBIN' => 'Robin-Robin group type',
		'ROUND_TWO' => 'Two-Two group type',
		'ROUND_SPLIT' => 'Conditional split group type'
	];
}
