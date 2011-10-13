<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			// uncomment the following to provide test database connection
			'db'=>array(
				'connectionString' => 'mysql:host=localhost;dbname=testdrive_test',
				'emulatePrepare' => true,
				'username' => '',
				'password' => '',
				'charset' => 'utf8',
			),
		),
	)
);
