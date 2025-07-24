<?php

declare(strict_types=1);

namespace TournamentGenerator\Export;

use TournamentGenerator\Interfaces\WithId;

/**
 * Interface SingleExport.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
interface SingleExporter
{
    /**
     * Simple export query without any modifiers.
     *
     * @return array The query result including the object reference
     */
    public static function exportBasic(WithId $withId) : array;
}
