<?php


namespace Export;


use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Export\GameExporter;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Tournament;

class GameExporterTest extends TestCase
{

	public function testInvalidConstruct() : void {
		$this->expectException(InvalidArgumentException::class);
		GameExporter::export(new HelperGameExporterClass());
	}

	public function testBasicExport() : void {
		$tournament = new Tournament('Tournament');
		$round = $tournament->round('Round 1');
		$group = $round->group('Group 1');

		$teams = [];
		for ($i = 0; $i < 4; $i++) {
			$teams[] = $group->team('Team '.$i, $i);
		}

		$group->game([$teams[0], $teams[1]]);
		$group->game([$teams[2], $teams[3]]);
		$group->game([$teams[0], $teams[2]]);
		$group->game([$teams[1], $teams[3]]);
		$group->game([$teams[0], $teams[3]]);
		$group->game([$teams[1], $teams[2]]);

		$export1 = GameExporter::export($tournament);
		$export2 = GameExporter::start($tournament)->get();
		$expectedExport = [
			(object) [
				'id'     => 1,
				'teams'  => [0, 1],
				'scores' => [],
			],
			(object) [
				'id'     => 2,
				'teams'  => [2, 3],
				'scores' => [],
			],
			(object) [
				'id'     => 3,
				'teams'  => [0, 2],
				'scores' => [],
			],
			(object) [
				'id'     => 4,
				'teams'  => [1, 3],
				'scores' => [],
			],
			(object) [
				'id'     => 5,
				'teams'  => [0, 3],
				'scores' => [],
			],
			(object) [
				'id'     => 6,
				'teams'  => [1, 2],
				'scores' => [],
			],
		];
		self::assertCount(6, $export1);
		self::assertEquals($expectedExport, $export1);
		self::assertCount(6, $export2);
		self::assertEquals($expectedExport, $export2);
	}

	public function testNestedExport() : void {
		$tournament = new Tournament('Tournament');
		$round = $tournament->round('Round 1');
		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');

		$teams = [];
		for ($i = 0; $i < 4; $i++) {
			$teams[] = $group1->team('Team '.$i, $i);
		}
		for ($i = 0; $i < 4; $i++) {
			$teams[] = $group2->team('Team '.($i + 4), $i + 4);
		}

		$group1->game([$teams[0], $teams[1]]);
		$group1->game([$teams[2], $teams[3]]);
		$group1->game([$teams[0], $teams[2]]);
		$group1->game([$teams[1], $teams[3]]);
		$group1->game([$teams[0], $teams[3]]);
		$group1->game([$teams[1], $teams[2]]);

		$group2->game([$teams[0 + 4], $teams[1 + 4]]);
		$group2->game([$teams[2 + 4], $teams[3 + 4]]);
		$group2->game([$teams[0 + 4], $teams[2 + 4]]);
		$group2->game([$teams[1 + 4], $teams[3 + 4]]);
		$group2->game([$teams[0 + 4], $teams[3 + 4]]);
		$group2->game([$teams[1 + 4], $teams[2 + 4]]);

		$export1 = GameExporter::export($tournament);
		$export2 = GameExporter::start($tournament)->get();
		$expectedExport = [
			(object) [
				'id'     => 1,
				'teams'  => [0, 1],
				'scores' => [],
			],
			(object) [
				'id'     => 2,
				'teams'  => [2, 3],
				'scores' => [],
			],
			(object) [
				'id'     => 3,
				'teams'  => [0, 2],
				'scores' => [],
			],
			(object) [
				'id'     => 4,
				'teams'  => [1, 3],
				'scores' => [],
			],
			(object) [
				'id'     => 5,
				'teams'  => [0, 3],
				'scores' => [],
			],
			(object) [
				'id'     => 6,
				'teams'  => [1, 2],
				'scores' => [],
			],
			(object) [
				'id'     => 7,
				'teams'  => [4, 5],
				'scores' => [],
			],
			(object) [
				'id'     => 8,
				'teams'  => [6, 7],
				'scores' => [],
			],
			(object) [
				'id'     => 9,
				'teams'  => [4, 6],
				'scores' => [],
			],
			(object) [
				'id'     => 10,
				'teams'  => [5, 7],
				'scores' => [],
			],
			(object) [
				'id'     => 11,
				'teams'  => [4, 7],
				'scores' => [],
			],
			(object) [
				'id'     => 12,
				'teams'  => [5, 6],
				'scores' => [],
			],
		];
		self::assertCount(12, $export1);
		self::assertEquals($expectedExport, $export1);
		self::assertCount(12, $export2);
		self::assertEquals($expectedExport, $export2);
	}

}

class HelperGameExporterClass extends HierarchyBase
{

}