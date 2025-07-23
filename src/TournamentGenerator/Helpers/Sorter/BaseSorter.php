<?php

declare(strict_types=1);

namespace TournamentGenerator\Helpers\Sorter;

/**
 * Class BaseSorter.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 */
interface BaseSorter
{
    /**
     * Sort function to call.
     */
    public function sort(array $data) : array;
}
