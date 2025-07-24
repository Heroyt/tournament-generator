<?php

declare(strict_types=1);

namespace TournamentGenerator\Helpers;

/**
 * Static helper functions.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
class Functions
{
    /**
     * Checks if the number is a power of 2.
     */
    public static function isPowerOf2(int $x) : bool {
        return (0 !== $x) && ($x & ($x - 1)) === 0;
    }

    /**
     * Get the next power of 2 larger than input.
     */
    public static function nextPowerOf2(int $x) : int {
        // Left bit shift by the bit length of the previous number
        return 1 << strlen(decbin($x));
    }

    /**
     * Get the previous power of 2 smaller or equal than input.
     */
    public static function previousPowerOf2(int $x) : int {
        // Left bit shift by the bit length of the previous number
        return 1 << (strlen(decbin($x)) - 1);
    }

    /**
     * Calculate a count of 2D array.
     *
     * @param array[] $array
     */
    public static function nestedCount(array $array) : int {
        $count = 0;
        foreach ($array as $inner) {
            $count += count($inner);
        }

        return $count;
    }

    public static function sortAlternate(array &$array) : array {
        $new = [];
        $new2 = [];
        $count = count($array) / 2;
        for ($i = 0; $i < $count; ++$i) {
            if (0 === $i % 2) {
                $new[] = array_shift($array);
                $new[] = array_pop($array);
            } else {
                $new2[] = array_shift($array);
                $new2[] = array_pop($array);
            }
        }
        $array = array_values(array_filter(array_merge($new, array_reverse($new2))));

        return $array;
    }
}
