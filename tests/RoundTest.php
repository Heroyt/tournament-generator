<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class RoundTest extends TestCase
{

	/** @test */
	public function check_name_setup() {
		$round = new \TournamentGenerator\Round('Round name 1');

		$this->assertEquals('Round name 1', $round->getName());
		$this->assertEquals('Round name 1', (string) $round);

		$round->setName('Round name 2');

		$this->assertEquals('Round name 2', $round->getName());
	}

}
