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
			$data = $_POST;
			if ($matchById) {
				$isExistBet = $db_volleybet->isExistBet($sid, $id);
				if (!$isExistBet) {
					if (isset($data['make-bet'])) {
						$isCorrect = isCorrectFieldPost($data, $command_id, 'command-id') && isCorrectFieldPost($data, $price, 'price');
						$price = floatval($price);
						if ($isCorrect) {
							$betInfo = [
								'user_id' => $sid,
								'match_id' => $id,
								'bid_amount' => $price,
								'command_id' => $command_id,
							];
							if ($result['user']['balance']>=$price) {
								$db_volleybet->updateBalanceById($sid, $result['user']['balance'] - $price);
								$bet_id = $db_volleybet->makeBet($betInfo);
								$isMadeBet = true;
							} else {
								$isError = 'Not enough balance';
								alert($isError);
							}
						}
					}
				} else {
					if (isset($data['delete-bet'])) {
						$betInfo = [
							'user_id' => $sid,
							'match_id' => $id,
						];
						$bet = $db_volleybet->deleteBet($betInfo);
						$db_volleybet->updateBalanceById($sid, $result['user']['balance'] + $bet['bid_amount']);
						$isExistBet = false;
					}
				}
			}
		}
		$result['user'] = $db_volleybet->getUserInfoById($sid);
	} else {
		$code = 300;
	}

	$result['result'] = getSettingsByCode($code);
	$result['match'] = $matchById;

	if ($isExistBet) {
		$betInfo = [
			'match_id' => $result['match']['id'],
			'user_id' => $result['user']['id'],
		];
		$result['user']['bet'] = $db_volleybet->getBetInfoByMatchAndUserId($betInfo);
		echo $twig->render('bet.html', $result);
	} else {
		if (!$isError) {
			if ($isMadeBet) {
				$betInfo = [
					'match_id' => $result['match']['id'],
					'user_id' => $result['user']['id'],
				];
				$result['user']['bet'] = $db_volleybet->getBetInfoByMatchAndUserId($betInfo);
				echo $twig->render('bet.html', $result);

			} else {
				echo $twig->render('makebet.html', $result);
			}
		} else {
			echo $twig->render('makebet.html', $result);
		}
	}
?>




















