<?php
/**
* Checksum model class file
*
* Various database calls to select and write checksums.
* @catagory Checksum
* @package Checksum
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/

/**
* Various database calls to select and write checksums.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/
class Checksum {
	/**
	* Variable for file_info table
	* @var $table
	*/
	public $table = 'file_info';
	
	/**
	* Get list of downloaded files without checksums
	* @access public
	* @return object Yii DAO
	*/
	public function getFileList() {
		$get_files = Yii::app()->db->createCommand()
			->select("id, temp_file_path, user_id")
			->from($this->table)
			->where(array('and', 'checksum_run = 0', "checksum IS NULL", 'problem_file != 1'))
			->queryAll();
			
		return $get_files;
	}
	
	/**
	* Retrieves a list of checksums for files.
	* @access public 
	* @return object Yii DAO
	*/
	public function getFileChecksums() {
		$get_file_checksums = Yii::app()->db->createCommand()
			->select('id, temp_file_path, checksum, user_id, expired_deleted')
			->from($this->table)
			->where(array('and', 'expired_deleted=0', 'zipped != 1', 'checksum_run = 1',
			        array('or', 'temp_file_path IS NOT NULL', 'temp_file_path !=""')))
			->queryAll();
			
		return $get_file_checksums;
	}
	
	/**
	* Retrieves a checksum for an individual file. 
	* @param $file_id
	* @param $remote
	* @access public 
	* @return string
	*/
	public function getOneFileChecksum($file_id, $remote = false) {
		$which_check = ($remote == false) ? 'checksum' : 'remote_checksum';
		
		$checksum = Yii::app()->db->createCommand()
		->select($which_check)
		->from($this->table)
		->where("id = :file_id", array(":file_id" => $file_id))
		->queryColumn();
		
		return $checksum[0];
	}
	
	/**
	* Determines if a file has been previously downloaded by a user
	* @param $file_id
	* @access public 
	* @return integer
	*/
	public function getDupChecksum($checksum, $user_id) {
		$dup_checksum_count = Yii::app()->db->createCommand()
			->select("COUNT(*)")
			->from($this->table)
			->where("checksum = :checksum and :user_id = user_id", array(":checksum" => $checksum, ":user_id" => $user_id))
			->queryColumn();
		
		return $dup_checksum_count[0];
	}
	
	/**
	* Write checksum errors to the database
	* @param $checksum
	* @param $id
	* @access public 
	* @return object Yii DAO
	*/
	public function writeSuccess($checksum, $id) {
		$sql = "UPDATE " . $this->table . " SET checksum_run = 1, checksum = ? WHERE id = ?";
		$write = Yii::app()->db->createCommand($sql)
			->execute(array($checksum, $id));
	}	
}