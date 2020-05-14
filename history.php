<?php
	require_once "includes.php";

	$db_volleybet = new DBWorker(NULL);

	$end_matches = $db_volleybet->getMatchesByStatus("end_matches");

	if (!(isset($_SESSION['id']))) {
		$code = 300;
	} else {
		$result['user'] = $db_volleybet->getUserInfoById($_SESSION['id']);
		$code = 102;
	}

	$result['result'] = getSettingsByCode($code);
	$result['end_matches'] = $end_matches;

	echo $twig->render('history.html', $result);

?>