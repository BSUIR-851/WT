<?php
	require_once "includes.php";

	if (isset($_SESSION['id'])) {
		$code = 102;
		unset($_SESSION['id']);
	} else {
		$code = 300;
	}

	header("Location: index.php");
