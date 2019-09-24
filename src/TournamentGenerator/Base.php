<?php

namespace TournamentGenerator;

/**
 * Abstract class with basic setters and getters
 *
 * @author Tomáš Vojík <vojik@wboy.cz>
 *
 * @since v0.3
 *
 */
abstract class Base
{

	/** @var string $name  The name of the object */
	protected $name = '';
	/** @var string|int $id  The unique identifier of the object */
	protected $id = null;

	/** @return string Name of the object */
	public function __toString() {
		return $this->name;
	}

	/**
	* Sets the name of the object
	*
	* @param string $name  Name of the object
	*
	* @return self
	*/
	public function setName(string $name) {
		$this->name = $name;
		return $this;
	}
	/**
	* Gets the name of the object
	*
	* @return string  Name of the object
	*/
	public function getName() {
		return $this->name;
	}
	/**
	* Sets the unique identifier of the object
	*
	* @param string|int $id  Unique identifier of the object
	*
	* @throws \InvalidArgumentException if the provided argument is not of type 'string' or 'int'
	*
	* @return self
	*/
	public function setId($id) {
		if (!is_string($id) && !is_int($id)) {
			$this->id = uniqid();
			throw new \InvalidArgumentException('Unsupported id type ('.gettype($id).') - expected type of string or int');
		}
		$this->id = $id;
		return $this;
	}
	/**
	* Gets the unique identifier of the object
	*
	* @return string  Unique identifier of the object
	*/
	public function getId() {
		return $this->id;
	}

}
