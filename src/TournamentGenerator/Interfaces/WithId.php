<?php

declare(strict_types=1);

namespace TournamentGenerator\Interfaces;

use InvalidArgumentException;

/**
 * Identifies an object with an ID.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
interface WithId
{
    /**
     * Gets the unique identifier of the object.
     *
     * @return int|string Unique identifier of the object
     */
    public function getId() : int|string;

    /**
     * Sets the unique identifier of the object.
     *
     * @param int|string $id Unique identifier of the object
     *
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'int'
     */
    public function setId(int|string $id) : WithId;
}
