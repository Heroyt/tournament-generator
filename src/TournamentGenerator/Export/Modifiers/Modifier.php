<?php

declare(strict_types=1);

namespace TournamentGenerator\Export\Modifiers;

/**
 * Interface Modifier.
 *
 * Modifiers are used to modify the exported data in some way.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
interface Modifier
{
    /**
     * Do the modifying action on data.
     *
     * @return array Modified data
     */
    public static function process(array &$data) : array;
}
