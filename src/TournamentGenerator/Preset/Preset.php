<?php

declare(strict_types=1);

namespace TournamentGenerator\Preset;

/**
 * Interface for tournament presets.
 *
 * Presets are used to generate set tournaments more easily without the need of explicit creation of groups, rounds, progressions, etc.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
interface Preset
{
    /**
     * Generates all groups, rounds and games for a preset.
     *
     * Creates a whole tournament structure for easier setup.
     *
     * @pre  The teams have been already added to the tournament
     *
     * @post All rounds are added
     * @post All groups are added
     * @post All progressions are added
     */
    public function generate() : Preset;
}
