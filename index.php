<!DOCTYPE html>
<html lang="cs">
	<head>
		<meta charset="utf-8">
		<title>Test rozlosování</title>
		<style media="screen">
			* {
				text-align: center;
				margin: 5px 0;
				padding: 0;
			}
			h2 {
				margin: 15px 0;
			}
			h3 {
				margin: 8px 0;
			}
			ul li {
				list-style: none;
			}
			table {
				border-collapse: collapse;
				position: relative;
				margin: 10px auto;
			}
			td, th {
				padding: 5px;
				border: 1px #000 solid;
			}
			th {
				background-color: #555;
				color: #fff;
				font-weight: bold;
			}
			pre {
				display: inline-block;
				position: relative;
				padding: 10px;
				text-align: left;
			}
		</style>
	</head>
	<body>
		<?php
			require 'vendor/autoload.php';

			// use TournamentGenerator;
			// require 'functions.php';
			// require 'classes/class_round.php';
			// require 'classes/class_group.php';
			// require 'classes/class_game.php';
			// require 'classes/class_team.php';
			// require 'classes/class_blankTeam.php';
			// require 'classes/class_progression.php';
			// require 'classes/class_teamFilter.php';
			// require 'classes/class_tournament.php';
			// require 'classes/tournament_presets/class_2R2G.php';
			// require 'classes/tournament_presets/class_singleElim.php';
			// require 'classes/tournament_presets/class_doubleElim.php';

			$t = new TournamentGenerator\Tournament('test');

			$tournament = new TournamentGenerator\Tournament_DoubleElimination('Testovací turnaj');
			$tournament->setPlay(7)->setGameWait(2)->setRoundWait(0);
			//
			// $round1 = $tournament->round('Round 1');
			// $round2 = $tournament->round('Round 2');
			// $round3 = $tournament->round('Round 3');
			//
			// $group_0_0 = $round1->group([
			// 	'name' => '0/0',
			// 	'inGame' => 2,
			// 	'type' => TWO_TWO,
			// ]);
			// $group_0_1 = $round2->group([
			// 	'name' => '0/1',
			// 	'type' => TWO_TWO
			// ]);
			// $group_1_0 = $round2->group([
			// 	'name' => '1/0',
			// 	'type' => TWO_TWO
			// ]);
			// $group_1_1 = $round3->group([
			// 	'name' => '1/1',
			// 	'type' => COND_SPLIT,
			// 	'maxSize' => 3
			// ]);
			// $group_0_2 = $round3->group([
			// 	'name' => '0/2',
			// 	'type' => R_R
			// ]);
			// $group_2_0 = $round3->group([
			// 	'name' => '2/0',
			// 	'type' => R_R
			// ]);
			//
			// $filter_win_1 = new TeamFilter('wins', '=', 1);
			// $filter_loss_1 = new TeamFilter('losses', '=', 1);
			// $filter_notProgressed = new TeamFilter('notprogressed');
			//
			$teamsA = [];
			for ($i=1; $i <= 6; $i++) {
				$t = $tournament->team('Team '.$i);
				$teamsA[$t->id] = $t;
			}
			//
			// $tournament->splitTeams($round1);
			//
			// if (count($teamsA) % 4 == 2) {
			// 	$group_top = $round2->group([
			// 		'name' => 'TOP',
			// 		'type' => TWO_TWO
			// 	]);
			//
			// 	$filter_win_2 = new TeamFilter('wins', '=', 2, [$group_0_0, $group_top]);
			// 	$filter_loss_2 = new TeamFilter('losses', '=', 2, [$group_0_0, $group_top]);
			// 	$filter_win_1_both = new TeamFilter('wins', '=', 1, [$group_0_0, $group_top]);
			// 	$filter_loss_1_both = new TeamFilter('losses', '=', 1, [$group_0_0, $group_top]);
			// 	$group_0_0->progression($group_top, 0, 1)->addFilter($filter_win_1); // PROGRESS THE BEST WINNING TEAM
			// 	$group_0_0->progression($group_top, 0, 1)->addFilter($filter_loss_1); // PROGRESS THE BEST LOSING TEAM
			// 	$group_top->progression($group_2_0)->addFilter($filter_win_2);
			// 	$group_top->progression($group_0_2)->addFilter($filter_loss_2);
			// 	$group_top->progression($group_1_1)->addFilter($filter_win_1_both, $filter_loss_1_both);
			// }
			//
			// $group_0_0->progression($group_0_1)->addFilter($filter_loss_1)->addFilter($filter_notProgressed);
			// $group_0_0->progression($group_1_0)->addFilter($filter_win_1)->addFilter($filter_notProgressed);
			// $group_0_1->progression($group_0_2)->addFilter($filter_loss_1);
			// $group_0_1->progression($group_1_1)->addFilter($filter_win_1);
			// $group_1_0->progression($group_2_0)->addFilter($filter_win_1);
			// $group_1_0->progression($group_1_1)->addFilter($filter_loss_1);

			$time = $tournament->generate()->genGamesSimulateReal(true);

			// try {
			// 	$games = $round1->genGames();
			// 	$round1->simulate();
			// } catch (Exception $e) {
			// 	echo '<pre style="color:red">'.$e.'</pre>';
			// }

			echo '<h2>Tournament: '.$tournament.' (expected: '.minutesToTime($time).')</h2>';

			echo '<h2>Teams</h2>';
			foreach ($teamsA as $id => $team) {
				echo '<h3>'.$team->name.' ('.$id.')</h3>';
			}

			foreach ($tournament->getRounds() as $round) {
				echo '<h2>'.$round.'</h2>';
				foreach ($round->getGroups() as $group) {
					echo '<h3>'.$group.'</h3>';
					writeGames($group->getGames(), $group->getTeams(), true);
				}
			}

			// echo '<h2>Round 1</h2>';
			// writeGames($round1->getGames(), $round1->getTeams());
			// echo '<h3>Playing Round 1</h3>';
			// foreach ($round1->getGames() as $game) {
			// 	$teams = $game->getTeams();
			// 	$results = [];
			// 	echo '<h4>'.implode(' VS ', $teams).'</h4>';
			// 	foreach ($teams as $team) {
			// 		$results[$team->id] = floor(rand(-1000, 5000));
			// 	}
			// 	$game->setResults($results);
			// 	echo '<p><strong>'.implode('</strong> : <strong>', $results).'</strong></p>';
			// }
			// $teams = $group_0_0->sortTeams();
			// echo '<h3>0/0 results</h3>';
			// echo '<ol>';
			// foreach ($teams as $team) {
			// 	echo '<li>'.$team.' - '.$team->groupResults[$group_0_0->id]['wins'].'/'.$team->groupResults[$group_0_0->id]['losses'].'</li>';
			// }
			// echo '</ol>';
			// $round1->progress();
			// $round1->progressBlank();
			// $round1->resetGames();

			// try {
			// 	$games = $round2->genGames();
			// } catch (Exception $e) {
			// 	echo '<pre style="color:red">'.$e.'</pre>';
			// }
			// echo '<h2>Round 2</h2>';
			// writeGames($round2->getGames(), $round2->getTeams());
			// echo '<h3>'.$group_0_1.'</h3>';
			// echo '<p>'.implode(', ', $group_0_1->getTeams()).'</p>';
			// writeGames($group_0_1->getGames(), $group_0_1->getTeams());
			// echo '<h3>'.$group_1_0.'</h3>';
			// echo '<p>'.implode(', ', $group_1_0->getTeams()).'</p>';
			// writeGames($group_1_0->getGames(), $group_1_0->getTeams());
			// if (isset($group_top)) {
			// 	echo '<h3>'.$group_top.'</h3>';
			// 	echo '<p>'.implode(', ', $group_top->getTeams()).'</p>';
			// 	writeGames($group_top->getGames(), $group_top->getTeams());
			// }
			// echo '<h3>Playing Round 2</h3>';
			// foreach ($round2->getGames() as $game) {
			// 	$teams = $game->getTeams();
			// 	$results = [];
			// 	echo '<h4>'.implode(' VS ', $teams).'</h4>';
			// 	foreach ($teams as $team) {
			// 		$results[$team->id] = floor(rand(-1000, 5000));
			// 	}
			// 	$game->setResults($results);
			// 	echo '<p><strong>'.implode('</strong> : <strong>', $results).'</strong></p>';
			// }
			// $teams = $group_0_1->sortTeams();
			// echo '<h3>0/1 results</h3>';
			// echo '<ol>';
			// foreach ($teams as $team) {
			// 	echo '<li>'.$team.' - '.$team->groupResults[$group_0_1->id]['wins'].'/'.$team->groupResults[$group_0_1->id]['losses'].'</li>';
			// }
			// echo '</ol>';
			// $teams = $group_1_0->sortTeams();
			// echo '<h3>1/0 results</h3>';
			// echo '<ol>';
			// foreach ($teams as $team) {
			// 	echo '<li>'.$team.' - '.$team->groupResults[$group_1_0->id]['wins'].'/'.$team->groupResults[$group_1_0->id]['losses'].'</li>';
			// }
			// echo '</ol>';
			// $round2->progress();
			// $round2->progressBlank();

			// try {
			// 	$games = $round3->genGames();
			// } catch (Exception $e) {
			// 	echo '<pre style="color:red">'.$e.'</pre>';
			// }
			// echo '<h2>Round 3</h2>';
			// writeGames($round3->getGames(), $round3->getTeams());
			// echo '<h3>'.$group_0_2.'</h3>';
			// writeGames($group_0_2->getGames(), $group_0_2->getTeams());
			// echo '<h3>'.$group_1_1.'</h3>';
			// writeGames($group_1_1->getGames(), $group_1_1->getTeams());
			// echo '<h3>'.$group_2_0.'</h3>';
			// writeGames($group_2_0->getGames(), $group_2_0->getTeams());
			// echo '<h3>Playing Round 3</h3>';
			// foreach ($round3->getGames() as $game) {
			// 	$teams = $game->getTeams();
			// 	$results = [];
			// 	echo '<h4>'.implode(' VS ', $teams).'</h4>';
			// 	foreach ($teams as $team) {
			// 		$results[$team->id] = floor(rand(-1000, 5000));
			// 	}
			// 	$game->setResults($results);
			// 	echo '<p><strong>'.implode('</strong> : <strong>', $results).'</strong></p>';
			// }
			// $teams = $group_2_0->sortTeams();
			// echo '<h3>2/0 results</h3>';
			// echo '<ol>';
			// foreach ($teams as $team) {
			// 	echo '<li>'.$team.' - '.$team->groupResults[$group_2_0->id]['wins'].'/'.$team->groupResults[$group_2_0->id]['losses'].'</li>';
			// }
			// echo '</ol>';
			// $teams = $group_1_1->sortTeams();
			// echo '<h3>1/1 results</h3>';
			// echo '<ol>';
			// foreach ($teams as $team) {
			// 	echo '<li>'.$team.' - '.$team->groupResults[$group_1_1->id]['wins'].'/'.$team->groupResults[$group_1_1->id]['losses'].'</li>';
			// }
			// echo '</ol>';
			// $teams = $group_0_2->sortTeams();
			// echo '<h3>0/2 results</h3>';
			// echo '<ol>';
			// foreach ($teams as $team) {
			// 	echo '<li>'.$team.' - '.$team->groupResults[$group_0_2->id]['wins'].'/'.$team->groupResults[$group_0_2->id]['losses'].'</li>';
			// }
			// echo '</ol>';
			//
			// $results = array_merge(
			// 	$group_2_0->sortTeams(),
			// 	$group_1_1->sortTeams(),
			// 	$group_0_2->sortTeams()
			// );
			// echo '<h3>Final results</h3>';
			// echo '<ol>';
			// foreach ($results as $team) {
			// 	echo '<li>'.$team.'</li>';
			// }
			// echo '</ol>';
			// echo '<table><thead><tr><th></th>';
			// foreach ($teams as $id => $team) {
			// 	echo '<th>'.$team->name.'</th>';
			// }
			// echo '</tr></thead><tbody>';
			// foreach ($teams as $id => $team) {
			// 	echo '<tr><th>'.$team->name.'</th>';
			// 		foreach ($teams as $id2 => $team2) {
			// 			if (!isset($team->gamesWith[$group->id][$id2])) $team->gamesWith[$group->id][$id2] = 0;
			// 			if ($id === $id2) echo '<th>X</th>';
			// 			else echo '<td>'.$team->gamesWith[$group->id][$id2].'</td>';
			// 		}
			// 	echo '</tr>';
			// }
			// echo '</tbody></table>';

			function writeGames($games, $teams, $highlightWin = false) {
				echo '<table><thead><tr><th></th>';
				for ($i=1; $i <= count($games); $i++) {
					echo '<th>'.$i.'</th>';
				}
				echo '</tr></thead><tbody>';
				foreach ($teams as $id => $team) {
					echo '<tr><th>'.$team->name.'</th>';
					foreach ($games as $game) {
						if (!isset($game) || in_array($team->id, $game->getTeamsIds())) echo '<th'.(isset($game) && $game->getWin() === $team->id ? ' style="background-color: #00ad31;"' : (isset($game) && in_array($team->id, $game->getDraw()) ? ' style="background-color: #c8c22a;"' : '')).'>X</th>';
						else echo '<td></td>';
					}
					echo '</tr>';
				}
				echo '</tbody></table>';
			}
			function minutesToTime($minutes) {
				return sprintf('%02d', floor($minutes/60)).':'.sprintf('%02d', $minutes%60);
			}
		?>
	</body>
</html>
