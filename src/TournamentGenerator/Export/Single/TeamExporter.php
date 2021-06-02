<?php
/** @noinspection PhpDocFieldTypeMismatchInspection */


namespace TournamentGenerator\Export\Single;

use TournamentGenerator\Export\Export;
use TournamentGenerator\Export\Modifiers\WithScoresModifier;
use TournamentGenerator\Export\SingleExportBase;
use TournamentGenerator\Interfaces\WithId;
use TournamentGenerator\Team;

/**
 * Exporter for teams
 *
 * A specific exporter, taking care of teams and their related data. Exports a single team object.
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class TeamExporter extends SingleExportBase
{

	/** @var Team */
	protected WithId $object;

	/**
	 * SingleTeamExporter constructor.
	 *
	 * @param Team $game
	 *
	 * @noinspection MagicMethodsValidityInspection
	 * @noinspection PhpMissingParentConstructorInspection
	 */
	public function __construct(Team $game) {
		$this->object = $game;
	}

	/**
	 * Simple export query without any modifiers
	 *
	 * @param Team $object
	 *
	 * @return array
	 */
	public static function export(WithId $object) : array {
		return self::start($object)->get();
	}

	/**
	 * Start an export query
	 *
	 * @param Team $object
	 *
	 * @return Export
	 */
	public static function start(WithId $object) : Export {
		return new self($object);
	}

	/**
	 * Simple export query without any modifiers
	 *
	 * @param Team $object
	 *
	 * @return array The query result including the object reference
	 */
	public static function exportBasic(WithId $object) : array {
		return (new self($object))->getWithObject();
	}

	/**
	 * @defgroup TeamExporterQueryModifiers Query modifiers
	 * @brief    Modifier methods for the query
	 */

	/**
	 * Gets the basic unmodified data
	 *
	 * @return array
	 */
	public function getBasic() : array {
		return [
			'object' => $this->object, // Passed for reference in the modifier methods
			'id'     => $this->object->getId(),
			'name'   => $this->object->getName(),
		];
	}

	/**
	 * Include team scores in the result set
	 *
	 * @return TeamExporter
	 * @ingroup TeamExporterQueryModifiers
	 */
	public function withScores() : TeamExporter {
		$this->modifiers[] = WithScoresModifier::class;
		return $this;
	}
}