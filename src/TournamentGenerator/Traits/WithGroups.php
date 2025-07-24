<?php

declare(strict_types=1);

namespace TournamentGenerator\Traits;

use TournamentGenerator\Containers\ContainerQuery;
use TournamentGenerator\Group;

/**
 * Trait WithGroups.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
trait WithGroups
{
    /**
     * Get all groups in this object.
     *
     * @return Group[]
     */
    public function getGroups() : array {
        return $this->container->getHierarchyLevel(Group::class);
    }

    /**
     * Get groups container query.
     */
    public function queryGroups() : ContainerQuery {
        return $this->container->getHierarchyLevelQuery(Group::class);
    }
}
