<?php
if ($_SERVER['HTTP_HOST'] == "niuedu.uz" || $_SERVER['HTTP_HOST'] == "www.niuedu.uz") {
	return array (
	  'pdo' => 
		array (
			'db_host' => 'localhost',
			'db_name' => 'niuedu_uz',
			'db_user' => 'niuedu_uz',
			'db_pass' => '8oZe29r1BVRywQQS',
		),
	);
} else {
	return array (
		'pdo' => 
		array (
			'db_host' => 'localhost',
			'db_name' => 'niuedu',
			'db_user' => 'root',
			'db_pass' => '',
		),
	);
}