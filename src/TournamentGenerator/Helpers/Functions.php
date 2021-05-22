<?php


namespace TournamentGenerator\Helpers;


/**
 * Static helper functions
 *
 * @package TournamentGenerator\Helpers
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
class Functions
{
	/**
	 * Checks if the number is a power of 2
	 *
	 * @param int $x
	 *
	 * @return bool
	 */
	public static function isPowerOf2(int $x) : bool {
		return ($x !== 0) && ($x & ($x - 1)) === 0;
	}

	/**
	 * Get the next power of 2 larger than input
	 *
	 * @param int $x
	 *
	 * @return int
	 */
	public static function nextPowerOf2(int $x) : int {
		// Left bit shift by the bit length of the previous number
		return 1 << strlen(decbin($x));
	}

	/**
	 * Get the previous power of 2 smaller or equal than input
	 *
	 * @param int $x
	 *
	 * @return int
	 */
	public static function previousPowerOf2(int $x) : int {
		// Left bit shift by the bit length of the previous number
		return 1 << (strlen(decbin($x)) - 1);
	}

	/**
	 * Calculate a count of 2D array
	 *
	 * @param array[] $array
	 *
	 * @return int
	 */
	public static function nestedCount(array $array) : int {
		$count = 0;
		foreach ($array as $inner) {
			$count += count($inner);
		}
		return $count;
	}
}