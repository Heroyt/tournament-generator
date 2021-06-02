<?php
/** @noinspection PhpDocFieldTypeMismatchInspection */


namespace TournamentGenerator\Export\Hierarchy;

use InvalidArgumentException;
use TournamentGenerator\Export\Export;
use TournamentGenerator\Export\ExportBase;
use TournamentGenerator\Export\Modifiers\WithScoresModifier;
use TournamentGenerator\Export\Single\SingleTeamExporter;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithId;
use TournamentGenerator\Interfaces\WithTeams;
use TournamentGenerator\Team;

/**
 * Exporter for teams
 *
 * A specific exporter for teams and their related data. Exports all teams from a hierarchy object.
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class TeamsExporter extends ExportBase
{

	/** @var WithTeams */
	protected WithId $object;

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
	public static function export(WithId $object) : array {
		return self::start($object)->get();
	}

	/**
	 * Start an export query
	 *
	 * @param HierarchyBase $object
	 *
	 * @return Export
	 */
	public static function start(WithId $object) : Export {
		return new self($object);
	}

	/**
	 * Gets the basic unmodified data
	 *
	 * @return array
	 */
	public function getBasic() : array {
		return array_map(static function(Team $team) {
			return (object) SingleTeamExporter::exportBasic($team);
		}, $this->object->getTeams());
	}

	/**
	 * @defgroup TeamExporterQueryModifiers Query modifiers
	 * @brief    Modifier methods for the query
	 */

	/**
	 * Include team scores in the result set
	 *
	 * @return TeamsExporter
	 * @ingroup TeamExporterQueryModifiers
	 */
	public function withScores() : TeamsExporter {
		$this->modifiers[] = WithScoresModifier::class;
		return $this;
	}
}