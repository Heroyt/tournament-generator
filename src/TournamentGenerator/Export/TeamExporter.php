<?php
/** @noinspection PhpDocFieldTypeMismatchInspection */


namespace TournamentGenerator\Export;

use Exception;
use InvalidArgumentException;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithTeams;
use TournamentGenerator\Team;

/**
 * Exporter for teams
 *
 * A specific exporter for teams and their related data.
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class TeamExporter extends ExportBase
{

	/** @var WithTeams */
	protected HierarchyBase $object;

	/**
	 * TeamExporter constructor.
	 *
	 * @param HierarchyBase $object
	 */
	public function __construct(HierarchyBase $object) {
		if (!$object instanceof WithTeams) {
			throw new InvalidArgumentException('Object must be instance of WithTeams.');
		}
		parent::__construct($object);
	}

	/**
	 * Simple export query without any modifiers
	 *
	 * @param HierarchyBase $object
	 *
	 * @return array
	 */
	public static function export(HierarchyBase $object) : array {
		return self::start($object)->get();
	}

	/**
	 * Start an export query
	 *
	 * @param HierarchyBase $object
	 *
	 * @return Export
	 */
	public static function start(HierarchyBase $object) : Export {
		return new self($object);
	}

	/**
	 * Gets the basic unmodified data
	 *
	 * @return array
	 */
	public function getBasic() : array {
		return array_map(static function(Team $team) {
			return (object) [
				'object' => $team, // Passed for reference in the modifier methods
				'id'     => $team->getId(),
				'name'   => $team->getName(),
			];
		}, $this->object->getTeams());
	}

	/**
	 * @defgroup TeamExporterQueryModifiers Query modifiers
	 * @brief    Modifier methods for the query
	 */

	/**
	 * Include team scores in the result set
	 *
	 * @return TeamExporter
	 * @ingroup TeamExporterQueryModifiers
	 */
	public function withScores() : TeamExporter {
		$this->modifiers[] = 'withScoresModifier';
		return $this;
	}

	/**
	 * @defgroup TeamExporterModifiers Modifier callbacks
	 * @brief    Modifier callbacks
	 * @details  Modifier callbacks alter the input in some way and return the modified result.
	 */

	/**
	 * Includes team scores in the result set
	 *
	 * @param array $data
	 *
	 * @return array
	 * @ingroup TeamExporterModifiers
	 * @throws Exception
	 */
	protected function withScoresModifier(array &$data) : array {
		foreach ($data as $object) {
			/** @var Team $team */
			$team = $object->object;
			$object->scores = array_map(static function(array $group) {
				unset($group['group']); // Get rid of the Group object reference
				return $group;
			}, $team->getGroupResults());
		}
		return $data;
	}
}