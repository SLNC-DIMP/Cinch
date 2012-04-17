<?php
/**
* ErrorFiles model class file
*
* Writes errors to the problem_files table.
* @catagory ErrorFiles
* @package ErrorFiles
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license CC0 1.0 Universal {@link http://creativecommons.org/publicdomain/zero/1.0/}
*/

/**
* Various database calls to select and write checksums.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license CC0 1.0 Universal {@link http://creativecommons.org/publicdomain/zero/1.0/}
*/
class ErrorFiles {
	/**
	* On error write file info to problem_downloads table
	* @param $error_id
	* @param $list_id
	* @param $current_user_id
	* @access public 
	* @return object Yii DAO
	*/
	public static function writeError($error_id, $file_id, $current_user_id) {
		$sql = "INSERT INTO problem_files(error_id, file_id, user_id) 
			VALUES(:error_id, :file_id, :user_id)";
		$error = Yii::app()->db->createCommand($sql)
			->bindParam(":error_id", $error_id, PDO::PARAM_INT)
			->bindParam(":file_id", $file_id, PDO::PARAM_INT)
			->bindParam(":user_id", $current_user_id, PDO::PARAM_INT)
			->execute();
	}
}