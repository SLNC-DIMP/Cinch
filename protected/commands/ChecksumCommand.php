<?php
class ChecksumCommand extends CConsoleCommand {
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
			->where("checksum IS NULL")
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
	
	/**
	* Create an MD5 or SHA1 checksum.  Default is MD5
	* @param $file
	* @param $type
	* @access public
	* @return string
	*/
	public function createChecksum($file, $type = 'md5') {
		$checksum = ($type == 'md5') ? md5_file($file) : sha1_file($file);
		
		return $checksum;
	}
	
	/**
	* Calculates a checksum for each file and compares it to the file's initial checksum
	* Write error to DB if mismatch detected.
	* @access public 
	*/
	public function actionCheck() {
		$file_count = $this->getCheckedFileCount();
	
		if($file_count > 0) {
			$file_list = $this->getFileChecksums($file_count);
			
			foreach($file_list as $file) {
				$current_checksum = $this->createChecksum($file['temp_file_path']);
				if($current_checksum != $file['checksum']) {
					$this->writeError($file['id']);
					echo 'checksum not ok for: ' . $file['temp_file_path'] . "\r\n";
				} else {
					echo 'checksum ok for: ' . $file['temp_file_path'] . "\r\n";
				}
			}
		}
	}
	
	/**
	* Run checksum command 
	* Default is to create new checksum for downloaded files
	* Writes checksum error on failure. 
	* 2 is value for "Could not create checksum"
	* 3 is value for "Duplicate Checksum. File deleted or not downloaded"
	*/
	public function actionCreate($args) {
		$file_lists = $this->getFileList();
		
		if(count($file_lists) > 0) {
			foreach($file_lists as $file_list) { 
				$checksum = $this->createChecksum($file_list['temp_file_path']);
				
				if($checksum) {
					$this->writeSuccess($checksum, $file_list['id']);
					echo "checksum for:" . $file_list['temp_file_path'] . " is " . $checksum . "\r\n";
				} else {
					$this->writeError($file_list['id'], 2);
					echo "Checksum not created. for: " . $file_list['temp_file_path'] . "\r\n";
				}
			}
		}
	}
}
// UPDATE `file_info` SET `checksum` = NULL WHERE `checksum`IS NOT NULL