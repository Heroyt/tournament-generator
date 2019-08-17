<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class FunctionsTest extends TestCase
{

	/** @test */
	public function check_power_of_2() {
		$this->assertTrue(\TournamentGenerator\isPowerOf2(1));
		$this->assertTrue(\TournamentGenerator\isPowerOf2(2));
		$this->assertTrue(\TournamentGenerator\isPowerOf2(8192));
		$this->assertFalse(\TournamentGenerator\isPowerOf2(3));
	}
}
