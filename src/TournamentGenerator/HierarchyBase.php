<?php

declare(strict_types=1);

namespace TournamentGenerator;

use Exception;
use TournamentGenerator\Containers\HierarchyContainer;
use TournamentGenerator\Export\ExporterInterface;
use TournamentGenerator\Interfaces\Exportable;
use TournamentGenerator\Interfaces\WithGames as WithGamesInterface;
use TournamentGenerator\Interfaces\WithTeams as WithTeamsInterface;

/**
 * Class HierarchyBase.
 *
 * Extended base for hierarchy objects (Tournament, Category, Round, Group).
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 */
abstract class HierarchyBase extends Base implements Exportable
{
    protected HierarchyContainer $container;

    /**
     * Get the hierarchy container.
     */
    public function getContainer() : HierarchyContainer {
        return $this->container;
    }

    /**
     * Insert into hierarchical container.
     *
     * @post Object is added to hierarchy
     * @post If the object has teams -> add other team container to hierarchy
     * @post If the object has games -> add other game container to hierarchy
     *
     * @return $this
     *
     * @throws Exception
     */
    public function insertIntoContainer(Base $base) : Base {
        $this->container->insert($base);
        if ($this instanceof WithGamesInterface && $base instanceof WithGamesInterface) {
            $this->addGameContainer($base->getGameContainer());
        }
        if ($this instanceof WithTeamsInterface && $base instanceof WithTeamsInterface) {
            $this->addTeamContainer($base->getTeamContainer());
        }

        return $this;
    }

    /**
     * Prepares a general hierarchy exporter for this hierarchy class.
     */
    public function export() : ExporterInterface {
        return Export\Hierarchy\Exporter::start($this);
    }
}
