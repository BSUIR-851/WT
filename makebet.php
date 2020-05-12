<?php
	require_once "includes.php";

	$db_volleybet = new DBWorker(NULL);
	$matchById = 404;
	if (isset($_SESSION['id'])) {
		$code = 102;
		$result['user'] = $db_volleybet->getUserInfoById($_SESSION['id']);

		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$matchById = $db_volleybet->getMatchInfoById($id);
		}

	} else {
		$code = 300;
	}


	$result['result'] = getSettingsByCode($code);
	$result['match'] = $matchById;
	echo $twig->render('makebet.html', $result);
?>

