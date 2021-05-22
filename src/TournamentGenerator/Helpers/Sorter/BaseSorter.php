<?php


namespace TournamentGenerator\Helpers\Sorter;

/**
 * Class BaseSorter
 *
 * @package TournamentGenerator\Helpers\Sorter
 * @author Tomáš Vojík <vojik@wboy.cz>
 */
interface BaseSorter
{

	/**
	 * Sort function to call
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function sort(array $data) : array;

}