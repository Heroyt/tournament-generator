<?php


namespace TournamentGenerator\Containers;

use Closure;
use Countable;
use Exception;
use Iterator;
use TournamentGenerator\Helpers\Sorter\BaseSorter;

/**
 * Class BaseContainer
 *
 * Container is a helper class for a tree-like structure. It can be used to create a hierarchy and store objects.
 *
 * @package TournamentGenerator\Containers
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
class BaseContainer implements Countable, Iterator
{

	/** @var string|int Identifier */
	public $id;
	/** @var BaseContainer[] Direct child containers */
	protected array $children = [];
	/** @var BaseContainer|null Parent container reference */
	protected ?BaseContainer $parent;
	/** @var array Any value that the container holds */
	protected array $values = [];

	/** @var int Current iterator index */
	protected int $currentIndex = 0;

	/**
	 * BaseContainer constructor.
	 *
	 * @param string|int $id
	 */
	public function __construct($id, BaseContainer $parent = null) {
		$this->id = $id;
		$this->parent = $parent;
	}

	/**
	 * Create a new container from array
	 *
	 * @param array $data
	 *
	 * @return BaseContainer
	 * @throws Exception
	 */
	public static function fromArray(array $data) : BaseContainer {
		$container = new self(0);
		$container->insertFlat(...$data);
		return $container;
	}

	/**
	 * Insert a value into container
	 *
	 * @param array $values Any value to insert into container
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function insertFlat(...$values) : BaseContainer {
		foreach ($values as $value) {
			$this->values[] = $value;
		}
		return $this;
	}

	/**
	 * Returns the value count
	 *
	 * @return int
	 */
	public function count() : int {
		return count($this->get());
	}

	/**
	 * Get all values from the container - including child nodes
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
	 * Get all top-level values from the container
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
	 * Get the current value
	 *
	 * @return mixed
	 */
	public function current() {
		return $this->get()[$this->currentIndex];
	}

	/**
	 * Move pointer to next
	 */
	public function next() : void {
		++$this->currentIndex;
	}

	/**
	 * Return the current key
	 *
	 * @return int
	 */
	public function key() : int {
		return $this->currentIndex;
	}

	/**
	 * Check if the current value exists
	 *
	 * @return bool
	 */
	public function valid() : bool {
		return isset($this->get()[$this->currentIndex]);
	}

	/**
	 * Rewind the iterator
	 */
	public function rewind() : void {
		$this->currentIndex = 0;
	}

	/**
	 * Insert a value into container
	 *
	 * @param array $values Any value to insert into container
	 *
	 * @post If the value has a container -> add it to the hierarchy
	 *
	 * @return $this
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
	 * Adds a child container
	 *
	 * @param BaseContainer[] $containers
	 *
	 * @return $this
	 * @post Parent container is set for the added children
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
	 * Gets all ids of the leaf containers
	 *
	 * @return string[]|int[]
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
	 * Add a filter callback
	 *
	 * @param Closure $callback
	 *
	 * @return ContainerQuery
	 */
	public function filter(Closure $callback) : ContainerQuery {
		$query = new ContainerQuery($this);
		$query->filter($callback);
		return $query;
	}

	/**
	 * Filter results to only contain those with a specific ID
	 *
	 * @param string|int $id
	 *
	 * @return ContainerQuery
	 */
	public function whereId($id) : ContainerQuery {
		$query = new ContainerQuery($this);
		$query->whereId($id);
		return $query;
	}

	/**
	 * Sort a result using a callback - maintaining the index association
	 *
	 * @param Closure $callback
	 *
	 * @return ContainerQuery
	 */
	public function sort(Closure $callback) : ContainerQuery {
		$query = new ContainerQuery($this);
		$query->sort($callback);
		return $query;
	}

	/**
	 * Sort a result set by a given property
	 *
	 * @warning Sort callback has a priority.
	 *
	 * @param string $property
	 *
	 * @return ContainerQuery
	 */
	public function sortBy(string $property) : ContainerQuery {
		$query = new ContainerQuery($this);
		$query->sortBy($property);
		return $query;
	}

	/**
	 * @param BaseSorter $sorter
	 *
	 * @return ContainerQuery
	 */
	public function addSorter(BaseSorter $sorter) : ContainerQuery {
		$query = new ContainerQuery($this);
		$query->addSorter($sorter);
		return $query;
	}

	/**
	 * Get only unique values
	 *
	 * @return ContainerQuery
	 */
	public function unique() : ContainerQuery {
		$query = new ContainerQuery($this);
		$query->unique();
		return $query;
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
		$query = new ContainerQuery($this);
		$query->only($property);
		return $query;
	}

	/**
	 * Get only the object's ids
	 *
	 * @return ContainerQuery
	 * @throws Exception
	 */
	public function ids() : ContainerQuery {
		$query = new ContainerQuery($this);
		$query->ids();
		return $query;
	}

	/**
	 * Get a parent container
	 *
	 * @return BaseContainer|null
	 * @since 0.5
	 */
	public function getParent() : ?BaseContainer {
		return $this->parent;
	}

	/**
	 * Set a container's parent
	 *
	 * @param BaseContainer|null $parent
	 *
	 * @return BaseContainer
	 * @throws Exception
	 * @since 0.5
	 */
	public function setParent(BaseContainer $parent) : BaseContainer {
		if ($parent !== $this->parent && !is_null($this->parent)) {
			throw new Exception('Parent container can only be set once!');
		}
		$this->parent = $parent;
		return $this;
	}
}