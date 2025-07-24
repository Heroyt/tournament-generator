<?php

declare(strict_types=1);

namespace TournamentGenerator\Interfaces;

use TournamentGenerator\Category;
use TournamentGenerator\Containers\ContainerQuery;

/**
 * Interface for objects that contain categories.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
interface WithCategories
{
    /**
     * Get all categories.
     *
     * @return Category[]
     */
    public function getCategories() : array;

    /**
     * Get categories container query.
     */
    public function queryCategories() : ContainerQuery;

    /**
     * Creates a new category and adds it to the object.
     *
     * @param string          $name New category name
     * @param null|int|string $id   Id of the new category - if omitted -> it is generated automatically as unique
     *                              string
     *
     * @return Category New category
     */
    public function category(string $name = '', null|int|string $id = null) : Category;

    /**
     * Add one or more category to object.
     *
     * @param Category ...$categories Category objects
     */
    public function addCategory(Category ...$categories) : WithCategories;

    /**
     * Set the wait time between categories.
     */
    public function setCategoryWait(int $wait) : WithCategories;

    /**
     * Get the wait time between categories.
     */
    public function getCategoryWait() : int;
}
