<?php

declare(strict_types=1);

/** @noinspection PhpDocFieldTypeMismatchInspection */

namespace TournamentGenerator\Export\Hierarchy;

use InvalidArgumentException;
use stdClass;
use TournamentGenerator\Export\ExporterBase;
use TournamentGenerator\Export\ExporterInterface;
use TournamentGenerator\Export\Modifiers\WithScoresModifier;
use TournamentGenerator\Export\Single\TeamExporter;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithId;
use TournamentGenerator\Interfaces\WithTeams;
use TournamentGenerator\Team;

/**
 * Exporter for teams.
 *
 * A specific exporter for teams and their related data. Exports all teams from a hierarchy object.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
class TeamsExporter extends ExporterBase
{
    /** @var WithTeams */
    protected WithId $object;

    /**
     * TeamExporter constructor.
     */
    public function __construct(HierarchyBase $hierarchyBase) {
        if (!$hierarchyBase instanceof WithTeams) {
            throw new InvalidArgumentException('Object must be instance of WithTeams.');
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
     */
    public function getBasic() : array {
        return array_map(
            static fn (Team $team) : stdClass => (object) TeamExporter::exportBasic($team),
            $this->object->getTeams()
        );
    }

    /**
     * @defgroup TeamExporterQueryModifiers Query modifiers
     *
     * @brief    Modifier methods for the query
     */
    /**
     * Include team scores in the result set.
     *
     * @ingroup TeamExporterQueryModifiers
     */
    public function withScores() : TeamsExporter {
        $this->modifiers[] = WithScoresModifier::class;

        return $this;
    }
}
