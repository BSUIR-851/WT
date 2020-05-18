<?php
	require_once "includes.php";

	$db_volleybet = new DBWorker(NULL);

	$matches = $db_volleybet->getMatches();

	if (isset($_SESSION['id'])) {
		$sid = $_SESSION['id'];
		$result['user'] = $db_volleybet->getUserInfoById($sid);
		$result['user']['bets'] = $db_volleybet->getBetsByUserId($sid);
		$code = 102;

	} else {
		$code = 300;
	}

	$result['result'] = getSettingsByCode($code);

	echo $twig->render('bets.html', $result);

?>