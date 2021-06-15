<?php


namespace TournamentGenerator\Containers;

use Closure;
use Exception;
use TournamentGenerator\Base;
use TournamentGenerator\Helpers\Sorter\BaseSorter;
use TournamentGenerator\Interfaces\WithId;

/**
 * Class ContainerQuery
 *
 * Container query is a helper class to filter, sort, etc. the values of the container hierarchy.
 *
 * @package TournamentGenerator\Containers
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
class ContainerQuery
{

	/** @var BaseContainer Queried container */
	protected BaseContainer $container;
	/** @var Closure[] Filter closures */
	protected array      $filters      = [];
	protected Closure    $sortClosure;
	protected string     $sortProperty;
	protected bool       $desc         = false;
	protected BaseSorter $sorter;
	protected bool       $topLevelOnly = false;
	protected bool       $uniqueOnly   = false;
	protected ?string    $pluck        = null;

	/**
	 * ContainerQuery constructor.
	 *
	 * @param BaseContainer $container Queried container
	 */
	public function __construct(BaseContainer $container, bool $topLevelOnly = false) {
		$this->container = $container;
		$this->topLevelOnly = $topLevelOnly;
	}

	/**
	 * Gets the first result of container query
	 *
	 * @return mixed|null
	 */
	public function getFirst() {
		$data = $this->get();
		if (count($data) === 0) {
			return null;
		}
		return reset($data);
	}

	/**
	 * Get the result
	 *
	 * @return array
	 */
	public function get() : array {
		// Get initial data
		if ($this->topLevelOnly) {
			$data = $this->container->getTopLevel();
		}
		else {
			$data = $this->container->get();
		}

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
	 *  Filter data to contain only unique values
	 *
	 * @param array $data
	 */
	protected function filterUnique(array &$data) : void {
		if ($this->uniqueOnly) {
			if (reset($data) instanceof Base) {
				$ids = [];
				foreach ($data as $key => $obj) {
					if (in_array($obj->getId(), $ids, true)) {
						unset($data[$key]);
						continue;
					}
					$ids[] = $obj->getId();
				}
			}
			else {
				$data = array_unique($data);
			}
		}
	}

	/**
	 * Apply predefined filters on data
	 *
	 * @param $data
	 */
	protected function applyFilters(&$data) : void {
		foreach ($this->filters as $filter) {
			$data = array_filter($data, $filter);
		}
		$data = array_values($data); // Reset array keys
	}

	/**
	 * Sort data using a predefined filters
	 *
	 * @param array $data
	 */
	protected function sortData(array &$data) : void {
		if (isset($this->sorter)) {
			$data = $this->sorter->sort($data);
		}
		elseif (isset($this->sortClosure)) {
			uasort($data, $this->sortClosure);
		}
		elseif (isset($this->sortProperty)) {
			uasort($data, [$this, 'sortByPropertyCallback']);
		}
	}

	/**
	 * Pluck a predefined value from data values
	 *
	 * @param $data
	 */
	protected function pluckData(&$data) : void {
		if (!empty($this->pluck)) {
			$data = array_map(function($item) {
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
			}, $data);
		}
	}

	/**
	 * Add a filter callback
	 *
	 * @param Closure $callback
	 *
	 * @return $this
	 */
	public function filter(Closure $callback) : ContainerQuery {
		$this->filters[] = $callback;
		return $this;
	}

	/**
	 * Filter results to only contain those with a specific ID
	 *
	 * @param string|int $id
	 *
	 * @return ContainerQuery
	 */
	public function whereId($id) : ContainerQuery {
		$this->filters[] = static function($object) use ($id) {
			return $object InstanceOf WithId && $object->getId() === $id;
		};
		return $this;
	}

	/**
	 * Sort in descending order
	 *
	 * @return $this
	 */
	public function desc() : ContainerQuery {
		$this->desc = true;
		return $this;
	}

	/**
	 * Sort a result using a callback - maintaining the index association
	 *
	 * @param Closure $callback
	 *
	 * @return $this
	 */
	public function sort(Closure $callback) : ContainerQuery {
		$this->sortClosure = $callback;
		return $this;
	}

	/**
	 * Sort a result set by a given property
	 *
	 * @warning Sort callback has a priority.
	 *
	 * @param string $property
	 *
	 * @return $this
	 */
	public function sortBy(string $property) : ContainerQuery {
		$this->sortProperty = $property;
		return $this;
	}

	/**
	 * @param BaseSorter $sorter
	 *
	 * @return $this
	 */
	public function addSorter(BaseSorter $sorter) : ContainerQuery {
		$this->sorter = $sorter;
		return $this;
	}

	/**
	 * Get only unique values
	 *
	 * @return $this
	 */
	public function unique() : ContainerQuery {
		$this->uniqueOnly = true;
		return $this;
	}

	/**
	 * Get only the object's ids
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function ids() : ContainerQuery {
		$this->only('getId');
		return $this;
	}

	/**
	 * Pluck a specific key from all values
	 *
	 * @param string $property Property, array key or method to extract from values
	 *
	 * @return ContainerQuery
	 * @throws Exception
	 */
	public function only(string $property) : ContainerQuery {
		if (!empty($this->pluck)) {
			throw new Exception('only() can be only called once.');
		}
		$this->pluck = $property;
		return $this;
	}

	/**
	 * Sort function for sorting by a defined property
	 *
	 * @param array|object $value1
	 * @param array|object $value2
	 *
	 * @return int
	 */
	protected function sortByPropertyCallback($value1, $value2) : int {
		// Get values
		$property = $this->sortProperty ?? '';
		$property1 = null;
		$property2 = null;
		if (is_object($value1) && isset($value1->$property)) {
			$property1 = $value1->$property;
		}
		elseif (is_array($value1) && isset($value1[$property])) {
			$property1 = $value1[$property];
		}
		if (is_object($value2) && isset($value2->$property)) {
			$property2 = $value2->$property;
		}
		elseif (is_array($value2) && isset($value2[$property])) {
			$property2 = $value2[$property];
		}

		// Compare values
		if ($property1 === $property2) {
			return 0;
		}
		return $property1 < $property2 ? -1 : 1;
	}

}