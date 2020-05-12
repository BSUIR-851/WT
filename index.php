<?php
	require_once "includes.php";

	$db_volleybet = new DBWorker(NULL);

	$matches = $db_volleybet->getMatches();
	$live_matches = $matches['live'];
	$future_matches = $matches['future'];

	if (!(isset($_SESSION['id']))) {
		$code = 300;
	} else {
		$result['user'] = $db_volleybet->getUserInfoById($_SESSION['id']);
		$code = 102;
	}

	$result['result'] = getSettingsByCode($code);
	$result['live_matches'] = $live_matches;
	$result['future_matches'] = $future_matches;

	echo $twig->render('index.html', $result);

?>