<?php
	function alert($msg) {
		echo "<script type = 'text/javascript'>alert('$msg');</script>";
	}

	function isCorrectFieldPost($data_post, &$data, $field) {
		$isCorrect = false;
		if (isset($data_post[$field])) {
			$data = $data_post[$field];
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			$data = trim($data);
			$isCorrect = true;
			if ($data == '') {
				$isCorrect = false;
			}
		}
		return $isCorrect;
	}

	function isCorrectPass($pass) {
		/*
			12+ символов
			заглавная 1+
			цифры 1+
			crNNN_ - начало пароля
			NNN - любые числа (кол-во - 1+)
			
			example: cr4_sa#aA0000g4@
		*/
		$pattern = "/^(cr[0-9]+_(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*]{12,})$/";
		$res = preg_match($pattern, $pass);
		if ($res == 1) {
			return true;
		} else {
			return false;
		}
	}

	function getSettingsByCode($code) {
		$settings['code'] = $code;
		$settings['success'] = false;

		switch ($settings['code']) {
			case 100:
				$settings['redir'] = 'signup.php';
				$settings['header'] = '';
				$settings['body'] = [
					'',
				];
				break;

			case 102:
				$settings['success'] = true;
				$settings['redir'] = 'index.php';
				$settings['header'] = 'Success!';
				$settings['body'] = [
					'You logged!',
				];
				break;
				
			case 103:
				$settings['success'] = true;
				$settings['redir'] = 'index.php';
				$settings['header'] = 'Success!';
				$settings['body'] = [
					'You have been registered!',
				];
				break;

			case 404:
				$settings['redir'] = 'signup.php';
				$settings['header'] = 'ERROR! You are not registered!';
				$settings['body'] = [
					'Please, check your input data!',
				];
				break;

			case 405:
				$settings['redir'] = 'signup.php';
				$settings['header'] = 'ERROR! User with this login or email exists!';
				$settings['body'] = [
					'Please, check your input data!',
				];
				break;

			case 406:
				$settings['redir'] = 'signup.php';
				$settings['header'] = 'ERROR! Confirm your password correctly!';
				$settings['body'] = [
					'Please, check your input data!',
				];
				break;

			case 407:
				$settings['redir'] = 'signup.php';
				$settings['header'] = 'ERROR! Password is incorrect!';
				$settings['body'] = [
					"Password must start with: \"crNNN_\"",
					"(NNN - any digits, amount: 1+)",
					"Length: 12+",
					"In password must be:",
					"1+ digit",
					"1+ capital letter",
					"1+ special character (!@#$%^&*)",
					"Please, check your input data!",
				];
				break;
			
			case 500:
				$settings['redir'] = 'login.php';
				$settings['header'] = 'ERROR! User doesn\'t exist!';
				$settings['body'] = [
					'Please, check your input data!',
				];
				break;

			case 501:
				$settings['redir'] = 'login.php';
				$settings['header'] = 'ERROR! Password is incorrect!';
				$settings['body'] = [
					'Please, check your input data!',
				];
				break;


		}

		return $settings;
	}