<?php

declare(strict_types=1);

/** @noinspection PhpDocFieldTypeMismatchInspection */

namespace TournamentGenerator\Export\Single;

use TournamentGenerator\Export\ExporterInterface;
use TournamentGenerator\Export\SingleExporterBase;
use TournamentGenerator\Game;
use TournamentGenerator\Interfaces\WithId;

/**
 * Exporter for games.
 *
 * A specific exporter, taking care of games and their related data. Exports data from a single Game object.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
class GameExporter extends SingleExporterBase
{
    /**
     * SingleGameExporter constructor.
     *
     * @param Game $object
     *
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(protected WithId $object) {}

    /**
     * Simple export query without any modifiers.
     *
     * @param Game $withId
     */
    public static function export(WithId $withId) : array {
        return self::start($withId)->get();
    }

    /**
     * Start an export query.
     *
     * @param Game $withId
     */
    public static function start(WithId $withId) : ExporterInterface {
        return new self($withId);
    }

    /**
     * Simple export query without any modifiers.
     *
     * @param Game $withId
     *
     * @return array The query result including the object reference
     */
    public static function exportBasic(WithId $withId) : array {
        return new self($withId)->getWithObject();
    }

    /**
     * Gets the basic unmodified data.
     */
    public function getBasic() : array {
        return [
            'object' => $this->object, // Passed for reference in the modifier methods
            'id'     => $this->object->getId(),
            'teams'  => $this->object->getTeamsIds(),
            'scores' => $this->object->getResults(),
        ];
    }
}
