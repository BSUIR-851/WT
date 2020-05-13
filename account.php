<?php
	require_once "includes.php";

	$db_volleybet = new DBWorker(NULL);
	$userCode = 102;

	if (isset($_SESSION['id'])) {
		$sid = $_SESSION['id'];
		$code = 102;

		if (isset($_GET['id'])) {
			$id = $_GET['id'];
		} else {
			$id = -1;
		}

		$result['admin'] = false;
		$result['user'] = $db_volleybet->getUserInfoById($sid);
		$result['edit_user'] = 404;

		if ($id != -1) {
			if ($sid != $id) {
				if ($result['user']['admin']) {
					$result['admin'] = true;
					$result['edit_user'] = $db_volleybet->getUserInfoById($id);
				}
			} else {
				$result['edit_user'] = $db_volleybet->getUserInfoById($sid);
			}

			$data = $_POST;
			if ($result['admin']) {
				if (isset($data['admin-delete-admin-submit'])) {
					$db_volleybet->deleteAdmin($id);
				} else if (isset($data['admin-add-admin-submit'])) {
					$db_volleybet->insertAdmin($id);
				}
			}
			if (($sid == $id) || ($result['admin'])) {
				if (isset($data['delete-account-submit'])) {
					if (($result['edit_user'] != 404) && ($result['edit_user'])) {
						$db_volleybet->deleteUser($result['edit_user']['id']);
						if ($result['edit_user']['id'] == $sid) {
							header("Location: logout.php");
						} else {
							header("Location: index.php");
						}
					}
				}

				if (isset($data['edit-account-submit'])) {
					$oldUserData = $db_volleybet->getFullUserInfoById($id);

					if ($oldUserData) {
						if (isCorrectFieldPost($data, $password, 'password')) {
							if (isCorrectPass($password)) {
								if (isCorrectFieldPost($data, $pass_check, 'pass-check')) {
									if ($password == $pass_check) {
										$password = password_hash($password, PASSWORD_DEFAULT);
									} else {
										$password = $oldUserData['password'];
										$userCode = 406;
									}
								} else {
									$password = $oldUserData['password'];
									$userCode = 406;
								}
							} else {
								$password = $oldUserData['password'];
								$userCode = 407;
							}

						} else {
							$password = $oldUserData['password'];
						}
						if (!isCorrectFieldPost($data, $first_name, 'first_name')) {
							$first_name = $oldUserData['first_name'];
						}

						if (!isCorrectFieldPost($data, $last_name, 'last_name')) {
							$last_name = $oldUserData['last_name'];
						}

						$userData = [
							'id' => $id,
							'password' => $password,
							'first_name' => $first_name,
							'last_name' => $last_name,
						];

						$db_volleybet->updateUser($userData);
					}
				}

				$result['edit_user'] = $db_volleybet->getUserInfoById($id);
			} else {
				print_r("HGERNJER\n");
				$result['edit_user'] = 404;
			}
		} else {
			$result['edit_user'] = false;
		}

		$result['user'] = $db_volleybet->getUserInfoById($sid);
		$result['user']['result'] = getSettingsByCode($userCode);
		$result['result'] = getSettingsByCode($code);
		echo $twig->render('account.html', $result);

	} else {
		$code = 101;
		$result['result'] = getSettingsByCode($code);
		echo $twig->render('login.html', $result);
	}
?>