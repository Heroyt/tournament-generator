<?php

declare(strict_types=1);

namespace TournamentGenerator;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * Abstract class with basic setters and getters.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.3
 */
abstract class Base implements Interfaces\WithId, JsonSerializable, Stringable
{
    /** @var string The name of the object */
    protected string $name = '';

    /** @var int|string The unique identifier of the object */
    protected int|string $id;

    /**
     * @return string Name of the object
     */
    public function __toString() : string {
        return $this->name;
    }

    /**
     * Gets the name of the object.
     *
     * @return string Name of the object
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Sets the name of the object.
     *
     * @param string $name Name of the object
     */
    public function setName(string $name) : Base {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the unique identifier of the object.
     *
     * @return int|string Unique identifier of the object
     */
    public function getId() : int|string {
        return $this->id;
    }

    /**
     * Sets the unique identifier of the object.
     *
     * @param int|string $id Unique identifier of the object
     *
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'int'
     */
    public function setId(int|string $id) : Base {
        $this->id = $id;

        return $this;
    }
}
