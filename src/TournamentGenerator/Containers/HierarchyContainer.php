<?php


namespace TournamentGenerator\Containers;

use Exception;
use InvalidArgumentException;
use TournamentGenerator\Base;

/**
 * Class HierarchyContainer
 *
 * HierarchyContainer is a special type of container specifically for creating hierarchies on Tournament->Category->Round->Group.
 *
 * @package TournamentGenerator\Containers
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
class HierarchyContainer extends BaseContainer
{

	/** @var HierarchyContainer[] Direct child containers */
	protected array $children = [];
	/** @var Base[] Any value that the container holds */
	protected array   $values = [];
	protected ?string $type   = null;

	public function insert(...$values) : BaseContainer {
		if (is_null($this->type)) {
			$this->type = get_class($values[0]);
		}
		foreach ($values as $obj) {
			if (!$obj instanceof $this->type) {
				throw new InvalidArgumentException('HierarchyContainer allows only one class type per level.');
			}
		}
		parent::insert(...$values);
		return $this;
	}

	/**
	 * Returns a container query for a set hierarchy level
	 *
	 * @param $class
	 *
	 * @return ContainerQuery
	 * @throws Exception
	 */
	public function getHierarchyLevelQuery($class) : ContainerQuery {
		$objects = $this->getHierarchyLevel($class);
		$container = BaseContainer::fromArray($objects);
		return $container->getQuery();
	}

	/**
	 * Returns a hierarchy level of objects that contains the given classes
	 *
	 * @param $class
	 *
	 * @return Base[]
	 */
	public function getHierarchyLevel($class) : array {
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
	 * Get current level's type
	 *
	 * @return string|null
	 */
	public function getLevelType() : ?string {
		return $this->type;
	}

}