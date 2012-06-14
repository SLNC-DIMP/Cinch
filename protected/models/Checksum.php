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
	* Get count of files in file info table
	* @access public
	* @return integer
	*/
	public function getCheckedFileCount() {
		$get_file_count = Yii::app()->db->createCommand()
			->select("COUNT(*)")
			->from($this->table)
			->where("checksum IS NOT NULL")
			->queryColumn();
		
		return $get_file_count[0];
		
	}
	
	/**
	* Retrieves a list of checksums for files.  If more than 5000 files to check 
	* it starts from a random point of less than or equal to 5000 files from the total # of files.
	* @param $count
	* @access public 
	* @return object Yii DAO
	*/
	public function getFileChecksums($count) {
		if($count <= 5000) {
			$offset = 0;
			$limit = $count;
		} else {
			$offset = mt_rand(0, ($count - 5000));
			$limit = 5000;
		}
		
		$get_file_checksums = Yii::app()->db->createCommand()
			->select('id, temp_file_path, checksum, user_id')
			->from($this->table)
			->limit($limit, $offset)
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