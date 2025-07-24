<?php

declare(strict_types=1);

namespace TournamentGenerator\Interfaces;

use TournamentGenerator\Containers\ContainerQuery;
use TournamentGenerator\Group;

/**
 * Interface for objects that contain groups.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
interface WithGroups
{
    /**
     * Get all groups in this category.
     *
     * @return Group[]
     */
    public function getGroups() : array;

    /**
     * Get groups container query.
     */
    public function queryGroups() : ContainerQuery;
}
