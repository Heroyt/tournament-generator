<?php

declare(strict_types=1);

namespace TournamentGenerator\Export\Hierarchy;

use Error;
use Override;
use TournamentGenerator\Export\ExporterBase;
use TournamentGenerator\Export\ExporterInterface;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithId;
use TournamentGenerator\Interfaces\WithTeams;

/**
 * Basic exporter.
 *
 * Basic exporter class for exporting all data from HierarchyBase objects. It uses all other specialized exporters and also inherits their modifiers specific.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
class Exporter extends ExporterBase
{
    /** @var ExporterBase[] Other exporters used */
    protected array $exporters = [];

    public function __construct(HierarchyBase $hierarchyBase) {
        if ($hierarchyBase instanceof WithTeams) {
            $this->exporters['teams'] = TeamsExporter::start($hierarchyBase);
        }
        if ($hierarchyBase instanceof WithGames) {
            $this->exporters['games'] = GamesExporter::start($hierarchyBase);
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
     *
     * @return Exporter
     */
    public static function start(WithId $withId) : ExporterInterface {
        return new self($withId);
    }

    /**
     * Try to call a modifier method on other used exporters.
     */
    public function __call(string $name, array $arguments) : Exporter {
        $called = false;
        foreach ($this->exporters as $exporter) {
            if (method_exists($exporter, $name)) {
                $exporter->{$name}(...$arguments);
                $called = true;
            }
        }
        if ($called) {
            return $this;
        }

        throw new Error('Call to undefined method ' . self::class . '::' . $name . '()');
    }

    /**
     * Finish the export query -> get the result.
     *
     * @return array The query result
     */
    #[Override]
    public function get() : array {
        $data = $this->getBasic();
        $this->applyModifiers($data);
        foreach ($this->exporters as $name => $exporter) {
            if ('setup' === $name) {
                $data += $exporter->get();
            } else {
                $data[$name] = $exporter->get();
            }
        }

        return $data;
    }

    /**
     * Gets the basic unmodified data.
     */
    public function getBasic() : array {
        return [];
    }

    /**
     * @defgroup ExporterQueryModifiers Query modifiers
     *
     * @brief    Modifier methods for the query
     */

    /**
     * Query modifier, adding a setup exporter.
     *
     * @return $this
     *
     * @ingroup ExporterQueryModifiers
     */
    public function withSetup() : ExporterInterface {
        $this->exporters['setup'] = SetupExporter::start($this->object);

        return $this;
    }
}
