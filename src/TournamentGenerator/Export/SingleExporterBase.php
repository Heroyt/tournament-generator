<?php

declare(strict_types=1);

namespace TournamentGenerator\Export;

use Override;

/**
 * Class SingleExportBase.
 *
 * Base class for all "Single" exporters = exporting only one specific class (Team, Game) and not a hierarchy class (Tournament, Category, Round, Group).
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
abstract class SingleExporterBase extends ExporterBase implements SingleExporter
{
    /**
     * Finish the export query -> get the result.
     *
     * @return array The query result
     */
    #[Override]
    public function get() : array {
        $data = $this->getBasic();
        $this->applyModifiers($data);
        unset($data['object']);

        return $data;
    }

    /**
     * Finish the export query -> get the result including an object reference.
     */
    public function getWithObject() : array {
        $data = $this->getBasic();
        $this->applyModifiers($data);

        return $data;
    }
}
