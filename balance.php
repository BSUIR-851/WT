<?php
	require_once "includes.php";

	$db_volleybet = new DBWorker(NULL);
	$code = 101;
	if ((isset($_SESSION['id']))) {
		$code = 102;
		$sid = $_SESSION['id'];
		$data = $_POST;

		$isCorrect = isCorrectFieldPost($data, $cardNumber, 'card-number') && isCorrectFieldPost($data, $cvv, 'cvv') &&
					 isCorrectFieldPost($data, $monthCard, 'month-card') && isCorrectFieldPost($data, $yearCard, 'year-card') &&
					 isCorrectFieldPost($data, $amount, 'amount');

		if ($isCorrect) {
			$result['user'] = $db_volleybet->getUserInfoById($sid);
			$oldBalance = $result['user']['balance'];
			$newBalance = $oldBalance + floatval($amount);
			$db_volleybet->updateBalanceById($sid, $newBalance);
		}

		$result['user'] = $db_volleybet->getUserInfoById($sid);
		$result['result'] = getSettingsByCode($code);
		echo $twig->render('balance.html', $result);
	}

	if ($code == 101) {
		header("Location: login.php");
	}

?>