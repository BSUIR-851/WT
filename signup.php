<?php		
	require_once "includes.php";

	$code = 100;

	$data_post = $_POST;
	if (!(isset($_SESSION['id']))) {
		if (isset($data_post['signup-submit'])) {
			$isCorrectPost = isCorrectFieldPost($data_post, $login, 'login') && isCorrectFieldPost($data_post, $email, 'email') && 
						 isCorrectFieldPost($data_post, $firstName, 'first-name') && isCorrectFieldPost($data_post, $lastName, 'last-name') && 
						 isCorrectFieldPost($data_post, $pass, 'pass') && isCorrectFieldPost($data_post, $passCheck, 'pass-check');

			if ($isCorrectPost) {
				$db_users = new DBWorker(NULL);

				do {
					if ($pass != $passCheck) {
						$code = 406;
						break;
					}

					if (!isCorrectPass($pass)) {
						$code = 407;
						break;
					}

					$now = date("Y-m-d H:i:s");
					$userData = array(
									'now' => $now,
									'login' => $login,
									'pass' => password_hash($pass, PASSWORD_DEFAULT),
									'email' => $email,
									'firstName' => $firstName,
									'lastName' => $lastName,
								);

					try {
						$db_users->insertUser($userData);
						$code = 103;
					} catch (PDOException $e) {
						print_r($e);
						print_r("\n");
						$code = 405;
					}

				} while (0);

			} else {
				$code = 404;
			}
		}
	} else {
		$code = 102;
	}
	$result['result'] = getSettingsByCode($code);
	echo $twig->render('signup.html', $result);

?>






