<?php

declare(strict_types=1);

namespace TournamentGenerator;

use TournamentGenerator\Interfaces\ProgressionInterface;

/**
 * Blank / dummy team used for simulating the games.
 *
 * It's not a "real" team, but it holds a reference to the original team that it was created from.
 * The dummy teams are created in progressions where we do not really care for the real results, but only for the games that will be generated.
 * It keeps the original team's id, but everything else can be different.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.1
 */
class BlankTeam extends Team
{
    /**
     * BlankTeam constructor.
     *
     * @param string      $name        New team name
     * @param Team        $team        Original team that this team is derived from
     * @param Group       $from        A group that the original team was playing in
     * @param Progression $progression A progression object that created this team
     */
    public function __construct(
        string $name,
        Team $team,
        protected Group $from,
        protected ProgressionInterface $progression
    ) {
        $this->groupResults = $team->groupResults;
        parent::__construct($name, $team->getId());
    }
}
