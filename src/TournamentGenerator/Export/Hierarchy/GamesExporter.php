<?php
/** @noinspection PhpDocFieldTypeMismatchInspection */


namespace TournamentGenerator\Export\Hierarchy;

use InvalidArgumentException;
use TournamentGenerator\Export\ExporterInterface;
use TournamentGenerator\Export\ExporterBase;
use TournamentGenerator\Export\Single\GameExporter;
use TournamentGenerator\Game;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithId;

/**
 * Exporter for games
 *
 * A specific exporter, taking care of games and their related data. Exports all games from a hierarchy object.
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class GamesExporter extends ExporterBase
{

	/** @var Game */
	protected WithId $object;

	public function __construct(HierarchyBase $object) {
		if (!$object instanceof WithGames) {
			throw new InvalidArgumentException('Object must be instance of WithGames.');
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
	 * @return ExporterInterface
	 */
	public static function start(WithId $object) : ExporterInterface {
		return new self($object);
	}

	/**
	 * Gets the basic unmodified data
	 *
	 * @return array
	 * @see GameExporter::export()
	 *
	 */
	public function getBasic() : array {
		return array_map(static function(Game $game) {
			return (object) GameExporter::exportBasic($game);
		}, $this->object->getGames());
	}
}