<?php


namespace TournamentGenerator\Interfaces;


use InvalidArgumentException;

/**
 * Identifies an object with an ID
 *
 * @package TournamentGenerator\Interfaces
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
interface WithId
{

	/**
	 * Gets the unique identifier of the object
	 *
	 * @return string|int  Unique identifier of the object
	 */
	public function getId();

	/**
	 * Sets the unique identifier of the object
	 *
	 * @param string|int $id Unique identifier of the object
	 *
	 * @return WithId
	 * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'int'
	 *
	 */
	public function setId($id) : WithId;

}