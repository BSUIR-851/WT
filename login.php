<?php
	require_once "includes.php";

	if (!(isset($_SESSION['id']))) {
		$code = 101;

		$data_post = $_POST;
		if (isset($data_post['login-submit'])) {
			$isCorrectPost = isCorrectFieldPost($data_post, $login, 'login') && isCorrectFieldPost($data_post, $input_pass, 'pass');
			if ($isCorrectPost) {
				$db_users = new DBWorker(NULL);
				if ($db_users->isUserExists($login)) {
					$userData = $db_users->getUserData($login);
					$pass = $userData['password'];
					if (password_verify($input_pass, $pass)) {
						$code = 102;
						$_SESSION['id'] = $userData['id'];


					} else {
						$code = 501;
					}

				} else {
					$code = 500;
				}	
			}
		}
	} else {
		$code = 102;
	}

	$result['result'] = getSettingsByCode($code);
	echo $twig->render('login.html', $result);

?>
