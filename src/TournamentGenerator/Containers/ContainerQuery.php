<?php


namespace TournamentGenerator\Containers;

use Closure;
use TournamentGenerator\Base;
use TournamentGenerator\Helpers\Sorter\BaseSorter;

/**
 * Class ContainerQuery
 *
 * Container query is a helper class to filter, sort, etc. the values of the container hierarchy.
 *
 * @package TournamentGenerator\Containers
 * @author  Tomáš Vojík <vojik@wboy.cz>
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

		// Filters
		foreach ($this->filters as $filter) {
			$data = array_filter($data, $filter);
		}

		// Sorting
		if (isset($this->sorter)) {
			$data = $this->sorter->sort($data);
		}
		elseif (isset($this->sortClosure)) {
			uasort($data, $this->sortClosure);
		}
		elseif (isset($this->sortProperty)) {
			uasort($data, [$this, 'sortByPropertyCallback']);
		}

		// Order reverse
		if ($this->desc) {
			$data = array_reverse($data, true);
		}

		return $data;
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