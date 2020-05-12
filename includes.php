<?php
	require_once "vendor/autoload.php";
	require_once "includes/db.php";
	require_once "includes/utils.php";

	$loader = new Twig_Loader_Filesystem("templates");
	$options = array(
		'cache' => 'compilation_cache',
		'auto_reload' => true 
	);
	$twig = new Twig_Environment($loader, $options);
	