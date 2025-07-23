<?php

declare(strict_types=1);

namespace TournamentGenerator\Containers;

use Closure;
use Exception;
use TournamentGenerator\Helpers\Sorter\BaseSorter;
use TournamentGenerator\Interfaces\WithId;

/**
 * Class ContainerQuery.
 *
 * Container query is a helper class to filter, sort, etc. the values of the container hierarchy.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
class ContainerQuery
{
    /** @var Closure[] Filter closures */
    protected array $filters = [];
    protected Closure $sortClosure;
    protected string $sortProperty;
    protected bool $desc = false;
    protected BaseSorter $sorter;
    protected bool $uniqueOnly = false;
    protected ?string $pluck = null;

    /**
     * ContainerQuery constructor.
     *
     * @param BaseContainer $container Queried container
     */
    public function __construct(protected BaseContainer $container, protected bool $topLevelOnly = false) {}

    /**
     * Gets the first result of container query.
     *
     * @return null|mixed
     */
    public function getFirst() : mixed {
        $data = $this->get();
        if (0 === count($data)) {
            return null;
        }

        return reset($data);
    }

    /**
     * Get the result.
     */
    public function get() : array {
        // Get initial data
        $data = $this->topLevelOnly ? $this->container->getTopLevel() : $this->container->get();

        // Unique
        $this->filterUnique($data);

        // Filters
        $this->applyFilters($data);

        // Sorting
        $this->sortData($data);

        // Order reverse
        if ($this->desc) {
            $data = array_reverse($data, true);
        }

        // "Pluck" a specific value from an object
        $this->pluckData($data);

        return $data;
    }

    /**
     *  Filter data to contain only unique values.
     */
    protected function filterUnique(array &$data) : void {
        if ($this->uniqueOnly) {
            if (reset($data) instanceof WithId) {
                $ids = [];
                foreach ($data as $key => $obj) {
                    if (in_array($obj->getId(), $ids, true)) {
                        unset($data[$key]);

                        continue;
                    }
                    $ids[] = $obj->getId();
                }
            } else {
                $data = array_unique($data);
            }
        }
    }

    /**
     * Apply predefined filters on data.
     */
    protected function applyFilters(mixed &$data) : void {
        foreach ($this->filters as $filter) {
            $data = array_filter($data, $filter);
        }
        $data = array_values($data); // Reset array keys
    }

    /**
     * Sort data using a predefined filters.
     */
    protected function sortData(array &$data) : void {
        if (isset($this->sorter)) {
            $data = $this->sorter->sort($data);
        } elseif (isset($this->sortClosure)) {
            uasort($data, $this->sortClosure);
        } elseif (isset($this->sortProperty)) {
            uasort($data, [$this, 'sortByPropertyCallback']);
        }
    }

    /**
     * Pluck a predefined value from data values.
     */
    protected function pluckData(mixed &$data) : void {
        if (null !== $this->pluck && '' !== $this->pluck && '0' !== $this->pluck) {
            $data = array_map(
                function ($item) {
                    if (is_array($item) && isset($item[$this->pluck])) {
                        return $item[$this->pluck];
                    }
                    if (is_object($item)) {
                        if (property_exists($item, $this->pluck)) {
                            return $item->{$this->pluck};
                        }
                        if (method_exists($item, $this->pluck)) {
                            return $item->{$this->pluck}();
                        }
                    }

                    return $item;
                },
                $data
            );
        }
    }

    /**
     * Get query results as an container.
     *
     * @throws Exception
     */
    public function getContainer() : BaseContainer {
        return BaseContainer::fromArray($this->get());
    }

    /**
     * Add a filter callback.
     */
    public function filter(Closure $callback) : ContainerQuery {
        $this->filters[] = $callback;

        return $this;
    }

    /**
     * Filter results to only contain those with a specific ID.
     */
    public function whereId(int|string $id) : ContainerQuery {
        $this->filters[] = (static fn ($object) : bool => $object instanceof WithId && $object->getId() === $id);

        return $this;
    }

    /**
     * Sort in descending order.
     */
    public function desc() : ContainerQuery {
        $this->desc = true;

        return $this;
    }

    /**
     * Sort a result using a callback - maintaining the index association.
     */
    public function sort(?Closure $callback = null) : ContainerQuery {
        $this->sortClosure = is_null($callback) ? static fn ($a, $b) : int => $a <=> $b : $callback;

        return $this;
    }

    /**
     * Sort a result set by a given property.
     *
     * @warning Sort callback has a priority.
     */
    public function sortBy(string $property) : ContainerQuery {
        $this->sortProperty = $property;

        return $this;
    }

    public function addSorter(BaseSorter $baseSorter) : ContainerQuery {
        $this->sorter = $baseSorter;

        return $this;
    }

    /**
     * Get only unique values.
     */
    public function unique() : ContainerQuery {
        $this->uniqueOnly = true;

        return $this;
    }

    /**
     * Get only the object's ids.
     *
     * @throws Exception
     */
    public function ids() : ContainerQuery {
        $this->only('getId');

        return $this;
    }

    /**
     * Pluck a specific key from all values.
     *
     * @param string $property Property, array key or method to extract from values
     *
     * @throws Exception
     */
    public function only(string $property) : ContainerQuery {
        if (null !== $this->pluck && '' !== $this->pluck && '0' !== $this->pluck) {
            throw new Exception('only() can be only called once.');
        }
        $this->pluck = $property;

        return $this;
    }

    /**
     * Sort function for sorting by a defined property.
     */
    protected function sortByPropertyCallback(array|object $value1, array|object $value2) : int {
        // Get values
        $property = $this->sortProperty ?? '';
        $property1 = null;
        $property2 = null;
        if (is_object($value1) && isset($value1->{$property})) {
            $property1 = $value1->{$property};
        } elseif (is_array($value1) && isset($value1[$property])) {
            $property1 = $value1[$property];
        }
        if (is_object($value2) && isset($value2->{$property})) {
            $property2 = $value2->{$property};
        } elseif (is_array($value2) && isset($value2[$property])) {
            $property2 = $value2[$property];
        }

        return $property1 <=> $property2;
    }
}
