<?php
/**
* Utils model class file
*
* Various database calls to write errors and events.
* @catagory Utils
* @package Utils
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license CC0 1.0 Universal {@link http://creativecommons.org/publicdomain/zero/1.0/}
*/

/**
* Various database calls to write errors and events.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license CC0 1.0 Universal {@link http://creativecommons.org/publicdomain/zero/1.0/}
*/
class Utils {
	/**
	* Set problem file flag to true in file_info table
	* @param $file_id
	* @static
	* @access public 
	* @return object Yii DAO
	*/
	public static function setProblemFile($file_id) {
		$sql = "UPDATE file_info SET problem_file = 1 WHERE id = ?";
		$metadata_processed = Yii::app()->db->createCommand($sql);
		$metadata_processed->execute(array($file_id));	
	}
	
	/**
	* On error write file info to problem_downloads table
	* @param $file_id
	* @param $error_id
	* @static
	* @access public 
	* @return object Yii DAO
	*/
	public static function writeError($file_id, $error_id) {
		$sql = "INSERT INTO problem_files(error_id, file_id) 
			VALUES(:error_id, :file_id)";
		$error = Yii::app()->db->createCommand($sql)
			->bindParam(":error_id", $error_id, PDO::PARAM_INT)
			->bindParam(":file_id", $file_id, PDO::PARAM_INT)
			->execute();
	}
	
	/**
	* On error write file info to problem_downloads table
	* @param $file_id
	* @param $event_id
	* @static
	* @access public 
	* @return object Yii DAO
	*/
	public static function writeEvent($file_id, $event_id)  {
		$sql = "INSERT INTO file_event_history(file_id, event_id) 
			VALUES(:file_id, :event_id)";
		$error = Yii::app()->db->createCommand($sql)
			->bindParam(":file_id", $file_id, PDO::PARAM_INT)
			->bindParam(":event_id", $event_id, PDO::PARAM_INT)
			->execute();
	}
	
	/**
	* Set events_frozen flag to true in file_info table.  
	* Events for this file have ended.
	* @param $file_id
	* @static
	* @access public 
	* @return object Yii DAO
	*/
	public static function freezeEvents($file_id) {
		$sql = "UPDATE file_info SET events_frozen = 1 WHERE id = ?";
		$freeze = Yii::app()->db->createCommand($sql);
		$freeze->execute(array($file_id));	
	}
}