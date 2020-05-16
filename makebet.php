<?php
	require_once "includes.php";

	$db_volleybet = new DBWorker(NULL);
	$matchById = false;
	$isExistBet = false;
	$isError = false;
	$isMadeBet = false; 

	if (isset($_SESSION['id'])) {
		$sid = $_SESSION['id'];
		$code = 102;
		$result['user'] = $db_volleybet->getUserInfoById($_SESSION['id']);

		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$matchById = $db_volleybet->getMatchInfoById($id);

			if ($matchById) {
				$isExistBet = $db_volleybet->isExistBet($sid, $id);

				if (!$isExistBet) {
					$data = $_POST;
					if (isset($data['make-bet'])) {
						$isCorrect = isCorrectFieldPost($data, $command_id, 'command-id') && isCorrectFieldPost($data, $price, 'price');

						if ($isCorrect) {
							$betInfo = [
								'user_id' => $sid,
								'match_id' => $id,
								'bid_amount' => $price,
								'command_id' => $command_id,
							];
							if ($result['user']['balance']>=floatval($price)) {
								$db_volleybet->makeBet($betInfo);
								$isMadeBet = true;
							} else {
								$isSuccess = 'Not enough balance';
							}
						}
					}
				}
			}
		}

	} else {
		$code = 300;
	}

	$result['result'] = getSettingsByCode($code);
	$result['match'] = $matchById;

	if ($isExistBet) {
		//echo $twig->render('bet.html', $result);
	} else {
		if (!$isError) {
			if ($isMadeBet) {
				//echo $twig->render('bet.html', $result);
			} else {
				echo $twig->render('makebet.html', $result);
			}
		} else {

		}
	}
?>
