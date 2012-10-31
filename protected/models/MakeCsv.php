<?php
/**
* MakeCsv model class file
*
* Various database calls to got get and set csv paths.
* @category MakeCsv
* @package MakeCsv
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/

/**
* Various database calls to got get and set csv paths.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/
class MakeCsv {
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
	* Write CSV file path to db and returns last insert id
	* @param $user_id
	* @param $file_path
	* @access public
	*/
	public function addPath($user_id, $file_path) {
		$sql = "INSERT INTO csv_meta_paths(user_id, path) VALUES(?, ?)";
		$fields = Yii::app()->db->createCommand($sql)
			->execute(array($user_id, $file_path));
		
		return Yii::app()->db->lastInsertID;
	}
}