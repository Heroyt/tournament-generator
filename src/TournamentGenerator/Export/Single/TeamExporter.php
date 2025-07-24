<?php

declare(strict_types=1);

/** @noinspection PhpDocFieldTypeMismatchInspection */

namespace TournamentGenerator\Export\Single;

use TournamentGenerator\Export\ExporterInterface;
use TournamentGenerator\Export\Modifiers\WithScoresModifier;
use TournamentGenerator\Export\SingleExporterBase;
use TournamentGenerator\Interfaces\WithId;
use TournamentGenerator\Team;

/**
 * Exporter for teams.
 *
 * A specific exporter, taking care of teams and their related data. Exports a single team object.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
class TeamExporter extends SingleExporterBase
{
    /**
     * SingleTeamExporter constructor.
     *
     * @param Team $object
     *
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(protected WithId $object) {}

    /**
     * Simple export query without any modifiers.
     *
     * @param Team $withId
     */
    public static function export(WithId $withId) : array {
        return self::start($withId)->get();
    }

    /**
     * Start an export query.
     *
     * @param Team $withId
     */
    public static function start(WithId $withId) : ExporterInterface {
        return new self($withId);
    }

    /**
     * Simple export query without any modifiers.
     *
     * @param Team $withId
     *
     * @return array The query result including the object reference
     */
    public static function exportBasic(WithId $withId) : array {
        return new self($withId)->getWithObject();
    }

    /**
     * @defgroup TeamExporterQueryModifiers Query modifiers
     *
     * @brief    Modifier methods for the query
     */
    /**
     * Gets the basic unmodified data.
     */
    public function getBasic() : array {
        return [
            'object' => $this->object, // Passed for reference in the modifier methods
            'id'     => $this->object->getId(),
            'name'   => $this->object->getName(),
        ];
    }

    /**
     * Include team scores in the result set.
     *
     * @ingroup TeamExporterQueryModifiers
     */
    public function withScores() : TeamExporter {
        $this->modifiers[] = WithScoresModifier::class;

        return $this;
    }
}
