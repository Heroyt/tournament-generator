<?php

declare(strict_types=1);

namespace TournamentGenerator\Export;

use JsonException;
use TournamentGenerator\Interfaces\WithId;

/**
 * Interface for exporters.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
interface ExporterInterface
{
    /**
     * Simple export query without any modifiers.
     *
     * @return array The query result
     */
    public static function export(WithId $withId) : array;

    /**
     * Start an export query.
     */
    public static function start(WithId $withId) : ExporterInterface;

    /**
     * Return result as json.
     *
     * @throws JsonException
     */
    public function getJson() : string;

    /**
     * Finish the export query -> get the result.
     *
     * @return array The query result
     */
    public function get() : array;

    /**
     * Gets the basic unmodified data.
     */
    public function getBasic() : array;
}
