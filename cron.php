<?php
//defined('YII_DEBUG') or define('YII_DEBUG',true);

// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/console.php';

require_once($yii);
Yii::createConsoleApplication($config)->run();