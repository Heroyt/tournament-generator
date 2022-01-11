<?php

namespace TournamentGenerator;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Abstract class with basic setters and getters
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator
 *
 * @since   0.3
 */
abstract class Base implements Interfaces\WithId, JsonSerializable
{

	/** @var string $name The name of the object */
	protected string $name = '';
	/** @var string|int $id The unique identifier of the object */
	protected $id;

	/**
	 * @return string Name of the object
	 */
	public function __toString() {
		return $this->name;
	}

	/**
	 * Gets the name of the object
	 *
	 * @return string  Name of the object
	 */
	public function getName() : string {
		return $this->name;
	}

	/**
	 * Sets the name of the object
	 *
	 * @param string $name Name of the object
	 *
	 * @return self
	 */
	public function setName(string $name) : Base {
		$this->name = $name;
		return $this;
	}

	/**
	 * Gets the unique identifier of the object
	 *
	 * @return string|int  Unique identifier of the object
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Sets the unique identifier of the object
	 *
	 * @param string|int $id Unique identifier of the object
	 *
	 * @return self
	 * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'int'
	 *
	 */
	public function setId($id) : Base {
		if (!is_string($id) && !is_int($id)) {
			/** @infection-ignore-all */
			$this->id = uniqid('', false);
			throw new InvalidArgumentException('Unsupported id type ('.gettype($id).') - expected type of string or int');
		}
		else {
			$this->id = $id;
		}
		return $this;
	}

}
