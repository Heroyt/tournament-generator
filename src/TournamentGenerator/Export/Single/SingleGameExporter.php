<?php
/** @noinspection PhpDocFieldTypeMismatchInspection */


namespace TournamentGenerator\Export\Single;

use TournamentGenerator\Export\Export;
use TournamentGenerator\Export\SingleExportBase;
use TournamentGenerator\Game;
use TournamentGenerator\Interfaces\WithId;

/**
 * Exporter for games
 *
 * A specific exporter, taking care of games and their related data. Exports data from a single Game object.
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class SingleGameExporter extends SingleExportBase
{

	/** @var Game */
	protected WithId $object;

	/**
	 * SingleGameExporter constructor.
	 *
	 * @param Game $game
	 *
	 * @noinspection MagicMethodsValidityInspection
	 * @noinspection PhpMissingParentConstructorInspection
	 */
	public function __construct(Game $game) {
		$this->object = $game;
	}

	/**
	 * Simple export query without any modifiers
	 *
	 * @param Game $object
	 *
	 * @return array
	 */
	public static function export(WithId $object) : array {
		return self::start($object)->get();
	}

	/**
	 * Start an export query
	 *
	 * @param Game $object
	 *
	 * @return Expo\textsl{}rt
	 */
	public static function start(WithId $object) : Export {
		return new self($object);
	}

	/**
	 * Simple export query without any modifiers
	 *
	 * @param Game $object
	 *
	 * @return array The query result including the object reference
	 */
	public static function exportBasic(WithId $object) : array {
		return (new self($object))->getWithObject();
	}

	/**
	 * Gets the basic unmodified data
	 *
	 * @return array
	 */
	public function getBasic() : array {
		return [
			'object' => $this->object, // Passed for reference in the modifier methods
			'id'     => $this->object->getId(),
			'teams'  => $this->object->getTeamsIds(),
			'scores' => $this->object->getResults(),
		];
	}
}