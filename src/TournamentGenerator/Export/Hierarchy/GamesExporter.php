<?php

declare(strict_types=1);

/** @noinspection PhpDocFieldTypeMismatchInspection */

namespace TournamentGenerator\Export\Hierarchy;

use InvalidArgumentException;
use stdClass;
use TournamentGenerator\Export\ExporterBase;
use TournamentGenerator\Export\ExporterInterface;
use TournamentGenerator\Export\Single\GameExporter;
use TournamentGenerator\Game;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithId;

/**
 * Exporter for games.
 *
 * A specific exporter, taking care of games and their related data. Exports all games from a hierarchy object.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
class GamesExporter extends ExporterBase
{
    /** @var Game */
    protected WithId $object;

    public function __construct(HierarchyBase $hierarchyBase) {
        if (!$hierarchyBase instanceof WithGames) {
            throw new InvalidArgumentException('Object must be instance of WithGames.');
        }
        parent::__construct($hierarchyBase);
    }

    /**
     * Simple export query without any modifiers.
     *
     * @param HierarchyBase $withId
     */
    public static function export(WithId $withId) : array {
        return self::start($withId)->get();
    }

    /**
     * Start an export query.
     *
     * @param HierarchyBase $withId
     */
    public static function start(WithId $withId) : ExporterInterface {
        return new self($withId);
    }

    /**
     * Gets the basic unmodified data.
     *
     * @see GameExporter::export()
     */
    public function getBasic() : array {
        return array_map(
            static fn (Game $game) : stdClass => (object) GameExporter::exportBasic($game),
            $this->object->getGames()
        );
    }
}
