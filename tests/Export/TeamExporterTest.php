<?php


namespace Export;


use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Export\TeamExporter;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Tournament;

class TeamExporterTest extends TestCase
{

	public function testInvalidConstruct() : void {
		$this->expectException(InvalidArgumentException::class);
		TeamExporter::export(new HelperTeamExporterClass());
	}

	public function testBasicExport() : void {
		$tournament = new Tournament('Tournament');

		$expectedExport = [];
		for ($i = 0; $i < 10; $i++) {
			$tournament->team('Team '.$i, $i);
			$expectedExport[] = (object) [
				'id'   => $i,
				'name' => 'Team '.$i,
			];
		}

		$export = TeamExporter::export($tournament);
		$exportQuery = TeamExporter::start($tournament)->get();
		self::assertCount(10, $export);
		self::assertCount(10, $exportQuery);
		self::assertEquals($expectedExport, $export);
		self::assertEquals($expectedExport, $exportQuery);
	}

	public function testNestedExport() : void {
		// Setup
		$tournament = new Tournament('Tournament');
		$category1 = $tournament->category('Category 1', 1);
		$category2 = $tournament->category('Category 2', 2);
		$round1 = $category1->round('Round 1', 1);
		$round2 = $category1->round('Round 2', 2);
		$round3 = $category2->round('Round 3', 3);
		$round4 = $category2->round('Round 4', 4);

		$expectedExport = [];
		for ($i = 0; $i < 5; $i++) {
			$round1->team('Team '.$i, $i);
			$expectedExport[] = (object) [
				'id'   => $i,
				'name' => 'Team '.$i,
			];
		}
		for ($i = 0; $i < 5; $i++) {
			$round2->team('Team '.($i + 5), $i + 5);
			$expectedExport[] = (object) [
				'id'   => $i + 5,
				'name' => 'Team '.($i + 5),
			];
		}
		for ($i = 0; $i < 5; $i++) {
			$round3->team('Team '.($i + 10), $i + 10);
			$expectedExport[] = (object) [
				'id'   => $i + 10,
				'name' => 'Team '.($i + 10),
			];
		}
		for ($i = 0; $i < 5; $i++) {
			$round4->team('Team '.($i + 15), $i + 15);
			$expectedExport[] = (object) [
				'id'   => $i + 15,
				'name' => 'Team '.($i + 15),
			];
		}

		$export = TeamExporter::export($tournament);
		$exportQuery = TeamExporter::start($tournament)->get();
		self::assertCount(20, $export);
		self::assertCount(20, $exportQuery);
		self::assertEquals($expectedExport, $exportQuery);
	}

	public function testWithScoresExport() : void {
		$tournament = new Tournament('Tournament');

		$expectedExport = [];
		for ($i = 0; $i < 10; $i++) {
			$tournament->team('Team '.$i, $i);
			$expectedExport[] = (object) [
				'id'     => $i,
				'name'   => 'Team '.$i,
				'scores' => [],
			];
		}

		$export = TeamExporter::start($tournament)->withScores()->get();
		self::assertCount(10, $export);
		self::assertEquals($expectedExport, $export);
	}

	public function testWithScoresInGroupExport() : void {
		$tournament = new Tournament('Tournament');
		$round = $tournament->round('Round');
		$group = $round->group('Group', 1);

		$expectedExport = [];
		for ($i = 0; $i < 10; $i++) {
			$group->team('Team '.$i, $i);
			$expectedExport[$i] = (object) [
				'id'     => $i,
				'name'   => 'Team '.$i,
				'scores' => [
					1 => [
						'points' => 0,
						'score'  => 0,
						'wins'   => 0,
						'draws'  => 0,
						'losses' => 0,
						'second' => 0,
						'third'  => 0,
					],
				],
			];
		}

		$export = TeamExporter::start($tournament)->withScores()->get();
		self::assertCount(10, $export);
		self::assertEquals($expectedExport, $export);

		// Add results
		foreach ($tournament->getTeams() as $team) {
			$max = random_int(1, 50);
			for ($i = 0; $i < $max; $i++) {
				$chance = random_int(0, 4);
				$points = 0;
				switch ($chance) {
					case 0:
						$team->addWin(1);
						$points += $group->getWinPoints();
						$expectedExport[$team->getId()]->scores[1]['wins']++;
						break;
					case 1:
						$team->addLoss(1);
						$points += $group->getLostPoints();
						$expectedExport[$team->getId()]->scores[1]['losses']++;
						break;
					case 2:
						$team->addDraw(1);
						$points += $group->getDrawPoints();
						$expectedExport[$team->getId()]->scores[1]['draws']++;
						break;
					case 3:
						$team->addSecond(1);
						$points += $group->getSecondPoints();
						$expectedExport[$team->getId()]->scores[1]['second']++;
						break;
					case 4:
						$team->addThird(1);
						$points += $group->getThirdPoints();
						$expectedExport[$team->getId()]->scores[1]['third']++;
						break;
				}
				$expectedExport[$team->getId()]->scores[1]['points'] += $points;
			}
		}

		$export = TeamExporter::start($tournament)->withScores()->get();
		self::assertCount(10, $export);
		self::assertEquals($expectedExport, $export);
	}

	public function testWithScoresInMoreGroupsExport() : void {
		$tournament = new Tournament('Tournament');
		$round = $tournament->round('Round');
		$group1 = $round->group('Group', 1);
		$group2 = $round->group('Group', 2);

		$expectedExport = [];
		for ($i = 0; $i < 10; $i++) {
			$team = $group1->team('Team '.$i, $i);
			$group2->addTeam($team);
			$expectedExport[$i] = (object) [
				'id'     => $i,
				'name'   => 'Team '.$i,
				'scores' => [
					1 => [
						'points' => 0,
						'score'  => 0,
						'wins'   => 0,
						'draws'  => 0,
						'losses' => 0,
						'second' => 0,
						'third'  => 0,
					],
					2 => [
						'points' => 0,
						'score'  => 0,
						'wins'   => 0,
						'draws'  => 0,
						'losses' => 0,
						'second' => 0,
						'third'  => 0,
					],
				],
			];
		}

		$export = TeamExporter::start($tournament)->withScores()->get();
		self::assertCount(10, $export);
		self::assertEquals($expectedExport, $export);

		// Add results
		foreach ($tournament->getTeams() as $team) {
			for ($groupId = 1; $groupId <= 2; $groupId++) {
				$max = random_int(1, 50);
				for ($i = 0; $i < $max; $i++) {
					$chance = random_int(0, 4);
					$points = 0;
					switch ($chance) {
						case 0:
							$team->addWin($groupId);
							$points += $group1->getWinPoints();
							$expectedExport[$team->getId()]->scores[$groupId]['wins']++;
							break;
						case 1:
							$team->addLoss($groupId);
							$points += $group1->getLostPoints();
							$expectedExport[$team->getId()]->scores[$groupId]['losses']++;
							break;
						case 2:
							$team->addDraw($groupId);
							$points += $group1->getDrawPoints();
							$expectedExport[$team->getId()]->scores[$groupId]['draws']++;
							break;
						case 3:
							$team->addSecond($groupId);
							$points += $group1->getSecondPoints();
							$expectedExport[$team->getId()]->scores[$groupId]['second']++;
							break;
						case 4:
							$team->addThird($groupId);
							$points += $group1->getThirdPoints();
							$expectedExport[$team->getId()]->scores[$groupId]['third']++;
							break;
					}
					$expectedExport[$team->getId()]->scores[$groupId]['points'] += $points;
				}
			}
		}

		$export = TeamExporter::start($tournament)->withScores()->get();
		self::assertCount(10, $export);
		self::assertEquals($expectedExport, $export);
	}

}

class HelperTeamExporterClass extends HierarchyBase
{

}