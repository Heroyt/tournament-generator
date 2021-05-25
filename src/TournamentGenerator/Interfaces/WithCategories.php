<?php


namespace TournamentGenerator\Interfaces;


use TournamentGenerator\Category;
use TournamentGenerator\Containers\ContainerQuery;

/**
 * Interface for objects that contain categories
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator\Interfaces
 * @since   0.4
 */
interface WithCategories
{

	/**
	 * Get all categories
	 *
	 * @return Category[]
	 */
	public function getCategories() : array;

	/**
	 * Get categories container query
	 *
	 * @return ContainerQuery
	 */
	public function queryCategories() : ContainerQuery;

	/**
	 * Creates a new category and adds it to the object
	 *
	 * @param string          $name New category name
	 * @param string|int|null $id   Id of the new category - if omitted -> it is generated automatically as unique string
	 *
	 * @return Category New category
	 */
	public function category(string $name = '', $id = null) : Category;

	/**
	 * Add one or more category to object
	 *
	 * @param Category ...$categories Category objects
	 *
	 * @return WithCategories
	 */
	public function addCategory(Category ...$categories) : WithCategories;

	/**
	 * Set the wait time between categories
	 *
	 * @param int $wait
	 *
	 * @return WithCategories
	 */
	public function setCategoryWait(int $wait) : WithCategories;

	/**
	 * Get the wait time between categories
	 *
	 * @return int
	 */
	public function getCategoryWait() : int;
}