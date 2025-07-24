<?php

declare(strict_types=1);

namespace TournamentGenerator\Traits;

use TournamentGenerator\Category;
use TournamentGenerator\Containers\ContainerQuery;
use TournamentGenerator\Interfaces\WithCategories as WithCategoriesInterface;

/**
 * Definitions of methods for objects that contain categories.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
trait WithCategories
{
    /**
     * Get all categories.
     *
     * @return Category[]
     */
    public function getCategories() : array {
        return $this->container->getHierarchyLevel(Category::class);
    }

    /**
     * Get categories container query.
     */
    public function queryCategories() : ContainerQuery {
        return $this->container->getHierarchyLevelQuery(Category::class);
    }

    /**
     * Add one or more category to object.
     *
     * @param Category ...$categories Category objects
     *
     * @return $this
     */
    public function addCategory(Category ...$categories) : WithCategoriesInterface {
        foreach ($categories as $category) {
            $this->insertIntoContainer($category);
        }

        return $this;
    }

    /**
     * Creates a new category and adds it to the object.
     *
     * @param string          $name New category name
     * @param null|int|string $id   Id of the new category - if omitted -> it is generated automatically as unique
     *                              string
     *
     * @return Category New category
     */
    public function category(string $name = '', null|int|string $id = null) : Category {
        $category = new Category($name, $id);
        $this->insertIntoContainer($category->setSkip($this->allowSkip));

        return $category;
    }
}
