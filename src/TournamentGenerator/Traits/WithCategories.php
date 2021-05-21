<?php


namespace TournamentGenerator\Traits;


use TournamentGenerator\Category;
use TournamentGenerator\Interfaces\WithCategories as WithCategoriesInterface;

/**
 * Definitions of methods for objects that contain categories
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator\Traits
 * @since   0.4
 */
trait WithCategories
{

	/** @var Category[] */
	protected array $categories = [];

	/**
	 * Get all categories
	 *
	 * @return Category[]
	 */
	public function getCategories() : array {
		return $this->categories;
	}


	/**
	 * Add one or more category to object
	 *
	 * @param Category ...$categories Category objects
	 *
	 * @return $this
	 */
	public function addCategory(Category ...$categories) : WithCategoriesInterface {
		foreach ($categories as $category) {
			$this->categories[] = $category;
		}
		return $this;
	}

	/**
	 * Creates a new category and adds it to the object
	 *
	 * @param string $name New category name
	 * @param string|int|null   $id   Id of the new category - if omitted -> it is generated automatically as unique string
	 *
	 * @return Category New category
	 */
	public function category(string $name = '', $id = null) : Category {
		$c = new Category($name, $id);
		$this->categories[] = $c->setSkip($this->allowSkip);
		return $c;
	}

}