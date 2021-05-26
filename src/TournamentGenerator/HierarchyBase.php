<?php


namespace TournamentGenerator;


use TournamentGenerator\Containers\HierarchyContainer;
use TournamentGenerator\Interfaces\WithGames as WithGamesInterface;
use TournamentGenerator\Interfaces\WithTeams as WithTeamsInterface;

/**
 * Class HierarchyBase
 *
 * Extended base for hierarchy objects (Tournament, Category, Round, Group).
 *
 * @package TournamentGenerator
 * @author Tomáš Vojík <vojik@wboy.cz>
 */
abstract class HierarchyBase extends Base
{

	protected HierarchyContainer $container;

	/**
	 * Get the hierarchy container
	 *
	 * @return HierarchyContainer
	 */
	public function getContainer() : HierarchyContainer {
		return $this->container;
	}

	/**
	 * Insert into hierarchical container
	 *
	 * @param Base $object
	 *
	 * @post Object is added to hierarchy
	 * @post If the object has teams -> add other team container to hierarchy
	 * @post If the object has games -> add other game container to hierarchy
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function insertIntoContainer(Base $object) : Base {
		$this->container->insert($object);
		if ($this instanceof WithGamesInterface && $object instanceof WithGamesInterface) {
			$this->addGameContainer($object->getGameContainer());
		}
		if ($this instanceof WithTeamsInterface && $object instanceof WithTeamsInterface) {
			$this->addTeamContainer($object->getTeamContainer());
		}
		return $this;
	}

}