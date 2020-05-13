<?php
	require_once "includes.php";

	$db_volleybet = new DBWorker(NULL);
	$code = 300;
	if ((isset($_SESSION['id']))) {
		$result['user'] = $db_volleybet->getUserInfoById($_SESSION['id']);
		$code = 102;
		if ($result['user']['admin']) {
			$result['result'] = getSettingsByCode($code);

			if (isset($_GET['id'])) {
				$id = $_GET['id'];
				$matchById = $db_volleybet->getMatchInfoById($id);
			} else {
				$matchById = 404;
			}
			$result['match'] = $matchById;

			$commands = $db_volleybet->getCommands();
			$result['commands'] = $commands;
			$result['now'] = date("Y-m-d H:i:s");

			$data = $_POST;
			// add command
			if (isset($data['admin-add-command'])) {
				echo $twig->render('addcommand.html', $result);

			// submit add command
			} else if (isset($data['admin-add-command-submit'])) {

				if (!isCorrectFieldPost($data, $full_name, 'full_name')) {
					$full_name = 'full name';
				}

				if (!isCorrectFieldPost($data, $short_name, 'short_name')) {
					$short_name = 'short name';
				}

				if (!isCorrectFieldPost($data, $wins, 'wins')) {
					$wins = 0;
				}

				if (!isCorrectFieldPost($data, $loses, 'loses')) {
					$loses = 0;
				}

				$commandData = [
					'full_name' => $full_name,
					'short_name' => $short_name,
					'wins' => $wins,
					'loses' => $loses,
				];

				$command_id = $db_volleybet->insertCommand($commandData);
				$commands = $db_volleybet->getCommands();
				$result['commands'] = $commands;
				$result['curr_command'] = $db_volleybet->getCommandInfoById($command_id);
				echo $twig->render('editcommand.html', $result);

			// show all commands
			} else if (isset($data['admin-show-commands'])) {
				echo $twig->render('commands.html', $result);

			// edit command
			} else if (isset($_GET['command_id'])) {
				$command_id = $_GET['command_id'];
				$result['curr_command'] = $db_volleybet->getCommandInfoById($command_id);
				echo $twig->render('editcommand.html', $result);

			// submit edit command
			} else if (isset($data['admin-edit-command-submit'])) {
				if (isset($_GET['edited_command_id'])){ 
					$edited_command_id = $_GET['edited_command_id'];

					$result['curr_command'] = $db_volleybet->getCommandInfoById($edited_command_id);

					if (!isCorrectFieldPost($data, $full_name, 'full_name')) {
						$full_name = $result['curr_command']['full_name'];
					}

					if (!isCorrectFieldPost($data, $short_name, 'short_name')) {
						$short_name = $result['curr_command']['short_name'];
					}
					
					if (!isCorrectFieldPost($data, $wins, 'wins')) {
						$wins = $result['curr_command']['wins'];
					}

					if (!isCorrectFieldPost($data, $loses, 'loses')) {
						$loses = $result['curr_command']['loses'];
					}


					$commandData = [
						'id' => $edited_command_id,
						'full_name' => $full_name,
						'short_name' => $short_name,
						'wins' => $wins,
						'loses' => $loses,
					];

					$db_volleybet->updateCommand($commandData);

				} else {
					$edited_command_id = 0;
				}

				$result['curr_command'] = $db_volleybet->getCommandInfoById($edited_command_id);
				echo $twig->render('editcommand.html', $result);

			// delete command 
			} else if (isset($data['admin-delete-command'])) {
				$edited_command_id = $_GET['edited_command_id'];
				$db_volleybet->deleteCommand($edited_command_id);
				$result['commands'] = $db_volleybet->getCommands();
				echo $twig->render('commands.html', $result);

			// add match
			} else if (isset($data['admin-add'])) {
				echo $twig->render('add.html', $result);

			// submit add match
			} else if (isset($data['admin-add-submit'])) {


				if (!isCorrectFieldPost($data, $id_command_1, 'command_1')) {
					$id_command_1 = 1;
				}

				if (!isCorrectFieldPost($data, $id_command_2, 'command_2')) {
					$id_command_2 = 1;
				}
				
				if (!isCorrectFieldPost($data, $date, 'datetime')) {
					$date = $result['now'];
				}

				if (!isCorrectFieldPost($data, $coeff_1, 'coeff_1')) {
					$coeff_1 = 1.5;
				}

				if (!isCorrectFieldPost($data, $coeff_2, 'coeff_2')) {
					$coeff_2 = 1.5;
				}

				$matchData = [
					'id_command_1' => $id_command_1,
					'id_command_2' => $id_command_2,
					'date' => $date,
					'coeff_1' => $coeff_1,
					'coeff_2' => $coeff_2,
				];

				$id = $db_volleybet->insertMatch($matchData);
				$commands = $db_volleybet->getCommands();
				$result['commands'] = $commands;
				$result['match'] = $db_volleybet->getMatchInfoById($id);

				echo $twig->render('makebet.html', $result);

			// edit match
			} else if (isset($data['admin-edit'])) {
				if ($result['match'] != 404) {
					echo $twig->render('edit.html', $result);
				} else {
					echo $twig->render('makebet.html', $result);
				}

			// submit edit match
			} else if (isset($data['admin-edit-submit'])) {

				if (!isCorrectFieldPost($data, $id_command_1, 'command_1')) {
					$id_command_1 = $result['match']['id_command_1'];
				}

				if (!isCorrectFieldPost($data, $id_command_2, 'command_2')) {
					$id_command_2 = $result['match']['id_command_2'];
				}

				if (!isCorrectFieldPost($data, $date, 'datetime')) {
					$date = $result['match']['date'];
				}

				if (!isCorrectFieldPost($data, $coeff_1, 'coeff_1')) {
					$coeff_1 = $result['match']['coeff_1'];
				}

				if (!isCorrectFieldPost($data, $coeff_2, 'coeff_2')) {
					$coeff_2 = $result['match']['coeff_2'];
				}

				if (!isCorrectFieldPost($data, $isLive, 'isLive')) {
					$isLive = $result['match']['isLive'];
				}

				$editData = [
					'id' => $result['match']['id'],
					'id_command_1' => $id_command_1,
					'id_command_2' => $id_command_2,
					'date' => $date,
					'coeff_1' => $coeff_1,
					'coeff_2' => $coeff_2,
					'isLive' => $isLive,
				];

				$db_volleybet->updateMatch($editData);
				$commands = $db_volleybet->getCommands();
				$result['commands'] = $commands;
				$result['match'] = $db_volleybet->getMatchInfoById($id);
				echo $twig->render('edit.html', $result);

			// delete match
			} else if (isset($data['admin-delete'])) {
				$db_volleybet->deleteMatch($id);
				header("Location: index.php");
			
			// watch basic match info
			} else {
				echo $twig->render('makebet.html', $result);
			}


		} else {
			$code = 300;
		}
	} else {
		$code = 300;
	}
	if ($code == 300) {
		header("HTTP/1.0 403 Forbidden");
		include("templates/error/403.html");
	}
?>











































