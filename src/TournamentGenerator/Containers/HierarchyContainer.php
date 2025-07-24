<?php

declare(strict_types=1);

namespace TournamentGenerator\Containers;

use Exception;
use InvalidArgumentException;
use Override;
use TournamentGenerator\Base;

/**
 * Class HierarchyContainer.
 *
 * HierarchyContainer is a special type of container specifically for creating hierarchies on Tournament->Category->Round->Group.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
class HierarchyContainer extends BaseContainer
{
    /** @var HierarchyContainer[] Direct child containers */
    protected array $children = [];

    /** @var Base[] Any value that the container holds */
    protected array $values = [];
    protected ?string $type = null;

    #[Override]
    public function insert(...$values) : BaseContainer {
        if (is_null($this->type)) {
            $this->type = $values[0]::class;
        }
        foreach ($values as $value) {
            if (!$value instanceof $this->type) {
                throw new InvalidArgumentException('HierarchyContainer allows only one class type per level.');
            }
        }
        parent::insert(...$values);

        return $this;
    }

    /**
     * Returns a container query for a set hierarchy level.
     *
     * @throws Exception
     */
    public function getHierarchyLevelQuery(mixed $class) : ContainerQuery {
        $objects = $this->getHierarchyLevel($class);

        return BaseContainer::fromArray($objects)->getQuery();
    }

    /**
     * Returns a hierarchy level of objects that contains the given classes.
     *
     * @return Base[]
     */
    public function getHierarchyLevel(mixed $class) : array {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class %s does not exist.', $class));
        }
        if ($this->type === $class) {
            return $this->values;
        }
        if (count($this->children) > 0) {
            $values = [];
            foreach ($this->children as $child) {
                $values[] = $child->getHierarchyLevel($class);
            }

            return array_merge(...$values);
        }

        return [];
    }

    /**
     * Get current level's type.
     */
    public function getLevelType() : ?string {
        return $this->type;
    }
}
