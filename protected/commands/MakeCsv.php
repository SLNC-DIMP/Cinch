<?php
abstract class MakeCsv extends CConsoleCommand {
	/**
	* Get user's base download path for csv file creation
	* @param $user_id
	* @param $type
	* @access public
	* @return string
	*/
	public function getUserPath($user_id, $type = 'curl') {
		$user_name = Yii::app()->db->createCommand()
			->select('username')
			->from('user')
			->where(':id = id', array(':id' => $user_id))
			->queryColumn();
		
		$type = ($type == 'curl') ? 'curl' : 'ftp';
		
		return Yii::getPathOfAlias('application.' . $type . '_downloads') . DIRECTORY_SEPARATOR . $user_name[0];
	}
	
	/**
	* The command "run()" method defined in child classes.
	* @abstract
	*/
	abstract public function actionIndex();
}