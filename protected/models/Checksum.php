<?php
class Checksum {
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
			->where(array('and', "checksum IS NULL", 'problem_file != 1'))
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
			->select('id, temp_file_path, checksum')
			->from($this->table)
			->limit($limit, $offset)
			->queryAll();
			
		return $get_file_checksums;
	}
	
	/**
	* Retrieves a checksum for an individual file. 
	* @param $file_id
	* @access public 
	* @return string
	*/
	public function getOneFileChecksum($file_id) {
		$checksum = Yii::app()->db->createCommand()
		->select('checksum')
		->from($this->table)
		->where("id = :file_id", array(":file_id" => $file_id))
		->queryColumn();
		
		return $checksum[0];
	}
	
	/**
	* Write checksum mismatch error. 
	* 5 is id of checksum mismatch in error_type table
	* @param $id
	* @param $error
	* @access public 
	* @return object Yii DAO
	*/
	public function writeError($id, $error = 5) {
		$sql = "UPDATE " . $this->table . " SET problem_file = ? WHERE id = ?";
		$write_error = Yii::app()->db->createCommand()
			->execute(array($error, $id));
	}
	
	/**
	* Write checksum errors to the database
	* @param $checksum
	* @param $id
	* @access public 
	* @return object Yii DAO
	*/
	public function writeSuccess($checksum, $id) {
		$sql = "UPDATE " . $this->table . " SET checksum = ? WHERE id = ?";
		$write = Yii::app()->db->createCommand($sql)
			->execute(array($checksum, $id));
	}
}