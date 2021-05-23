<?php
/** @noinspection PhpDocFieldTypeMismatchInspection */


namespace TournamentGenerator\Export;

use InvalidArgumentException;
use TournamentGenerator\Game;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithGames;

/**
 * Class GameExporter
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class GameExporter extends ExportBase
{

	/** @var WithGames */
	protected HierarchyBase $object;

	public function __construct(HierarchyBase $object) {
		if (!$object instanceof WithGames) {
			throw new InvalidArgumentException('Object must be instance of WithGames.');
		}
		parent::__construct($object);
	}

	/**
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

	public function getBasic() : array {
		return array_map(static function(Game $game) {
			return (object) [
				'object' => $game, // Passed for reference in the modifier methods
				'id'     => $game->getId(),
				'teams'  => $game->getTeamsIds(),
				'scores' => $game->getResults(),
			];
		}, $this->object->getGames());
	}
}