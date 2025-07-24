<?php

declare(strict_types=1);

namespace TournamentGenerator\Containers;

use Closure;
use Countable;
use Exception;
use Iterator;
use TournamentGenerator\Helpers\Sorter\BaseSorter;

/**
 * Class BaseContainer.
 *
 * Container is a helper class for a tree-like structure. It can be used to create a hierarchy and store objects.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @implements Iterator<int, BaseContainer>
 *
 * @since   0.4
 */
class BaseContainer implements Countable, Iterator
{
    /** @var BaseContainer[] Direct child containers */
    protected array $children = [];

    /** @var array Any value that the container holds */
    protected array $values = [];

    /** @var int Current iterator index */
    protected int $currentIndex = 0;

    /**
     * BaseContainer constructor.
     */
    public function __construct(
        public int|string $id,
        /** @var null|BaseContainer Parent container reference */
        protected ?BaseContainer $parent = null
    ) {}

    /**
     * Create a new container from array.
     *
     * @throws Exception
     */
    public static function fromArray(array $data) : BaseContainer {
        $container = new self(0);
        $container->insertFlat(...$data);

        return $container;
    }

    /**
     * Insert a value into container.
     *
     * @param array $values Any value to insert into container
     *
     * @throws Exception
     */
    public function insertFlat(...$values) : BaseContainer {
        foreach ($values as $value) {
            $this->values[] = $value;
        }

        return $this;
    }

    /**
     * Returns the value count.
     */
    public function count() : int {
        return count($this->get());
    }

    /**
     * Get all values from the container - including child nodes.
     *
     * @return array All values
     */
    public function get() : array {
        if (count($this->children) > 0) {
            $values = [$this->values];
            foreach ($this->children as $child) {
                $values[] = $child->get();
            }

            return array_merge(...$values);
        }

        return $this->values;
    }

    /**
     * Get all top-level values from the container.
     *
     * @return array All values
     */
    public function getTopLevel() : array {
        return $this->values;
    }

    public function getTopLevelQuery() : ContainerQuery {
        return new ContainerQuery($this, true);
    }

    public function getQuery() : ContainerQuery {
        return new ContainerQuery($this);
    }

    /**
     * Get the current value.
     */
    public function current() : mixed {
        return $this->get()[$this->currentIndex];
    }

    /**
     * Move pointer to next.
     *
     * @infection-ignore-all
     */
    public function next() : void {
        ++$this->currentIndex;
    }

    /**
     * Return the current key.
     */
    public function key() : int {
        return $this->currentIndex;
    }

    /**
     * Check if the current value exists.
     */
    public function valid() : bool {
        return isset($this->get()[$this->currentIndex]);
    }

    /**
     * Rewind the iterator.
     *
     * @infection-ignore-all
     */
    public function rewind() : void {
        $this->currentIndex = 0;
    }

    /**
     * Insert a value into container.
     *
     * @param array $values Any value to insert into container
     *
     * @post If the value has a container -> add it to the hierarchy
     *
     * @throws Exception
     */
    public function insert(...$values) : BaseContainer {
        foreach ($values as $value) {
            $this->values[] = $value;
            if (is_object($value) && method_exists($value, 'getContainer')) {
                $this->addChild($value->getContainer());
            }
        }

        return $this;
    }

    /**
     * Adds a child container.
     *
     * @param BaseContainer[] $containers
     *
     * @post Parent container is set for the added children
     *
     * @throws Exception
     */
    public function addChild(BaseContainer ...$containers) : BaseContainer {
        foreach ($containers as $container) {
            if (!isset($this->children[$container->id])) {
                $container->setParent($this);
                $this->children[$container->id] = $container;
            }
        }

        return $this;
    }

    /**
     * Gets all ids of the leaf containers.
     *
     * @return int[]|string[]
     */
    public function getLeafIds() : array {
        if (count($this->children) > 0) {
            $ids = [];
            foreach ($this->children as $child) {
                $ids[] = $child->getLeafIds();
            }

            return array_merge(...$ids);
        }

        return [$this->id];
    }

    /**
     * Add a filter callback.
     */
    public function filter(Closure $callback) : ContainerQuery {
        $containerQuery = new ContainerQuery($this);
        $containerQuery->filter($callback);

        return $containerQuery;
    }

    /**
     * Filter results to only contain those with a specific ID.
     */
    public function whereId(int|string $id) : ContainerQuery {
        $containerQuery = new ContainerQuery($this);
        $containerQuery->whereId($id);

        return $containerQuery;
    }

    /**
     * Sort a result using a callback - maintaining the index association.
     */
    public function sort(?Closure $callback = null) : ContainerQuery {
        $containerQuery = new ContainerQuery($this);
        $containerQuery->sort($callback);

        return $containerQuery;
    }

    /**
     * Sort a result set by a given property.
     *
     * @warning Sort callback has a priority.
     */
    public function sortBy(string $property) : ContainerQuery {
        $containerQuery = new ContainerQuery($this);
        $containerQuery->sortBy($property);

        return $containerQuery;
    }

    public function addSorter(BaseSorter $baseSorter) : ContainerQuery {
        $containerQuery = new ContainerQuery($this);
        $containerQuery->addSorter($baseSorter);

        return $containerQuery;
    }

    /**
     * Get only unique values.
     */
    public function unique() : ContainerQuery {
        $containerQuery = new ContainerQuery($this);
        $containerQuery->unique();

        return $containerQuery;
    }

    /**
     * Pluck a specific key from all values.
     *
     * @param string $property Property, array key or method to extract from values
     *
     * @throws Exception
     */
    public function only(string $property) : ContainerQuery {
        $containerQuery = new ContainerQuery($this);
        $containerQuery->only($property);

        return $containerQuery;
    }

    /**
     * Get only the object's ids.
     *
     * @throws Exception
     */
    public function ids() : ContainerQuery {
        $containerQuery = new ContainerQuery($this);
        $containerQuery->ids();

        return $containerQuery;
    }

    /**
     * Get a parent container.
     *
     * @since 0.5
     */
    public function getParent() : ?BaseContainer {
        return $this->parent;
    }

    /**
     * Set a container's parent.
     *
     * @param null|BaseContainer $baseContainer
     *
     * @throws Exception
     *
     * @since 0.5
     */
    public function setParent(BaseContainer $baseContainer) : BaseContainer {
        if ($baseContainer !== $this->parent && !is_null($this->parent)) {
            throw new Exception('Parent container can only be set once!');
        }
        $this->parent = $baseContainer;

        return $this;
    }
}
