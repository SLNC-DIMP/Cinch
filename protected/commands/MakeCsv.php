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
	* Write CSV file path to $db
	* @param $file_path
	* @param $user_id
	* @access protected
	*/
	protected function addPath($user_id, $file_path) {
		$sql = "INSERT INTO csv_meta_paths(user_id, path) VALUES(?, ?)";
		$fields = Yii::app()->db->createCommand($sql)
			->execute(array($user_id, $file_path));
	}
	
	/**
	* The command "run()" method defined in child classes.
	* @abstract
	*/
	abstract public function actionIndex();
}