<?php
class ChecksumCommand extends CConsoleCommand {
	public $table = 'file_info';
	
	/**
	* Get list of downloaded files without checksums
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
	* @return object Data Access Object
	*/
	public function getFileChecksums($count) {
		if($count <= 5000) {
			$start = 0;
			$total = $count;
		} else {
			$start = mt_rand(0, ($count - 5000));
			$total = 5000;
		}
		
		$get_file_checksums = Yii::app()->db->createCommand()
			->select('id, temp_file_path, checksum')
			->from($this->table)
			->limit($start, $total)
			->queryAll();
			
		return $get_file_lists;
	}
	
	/**
	* Write checksum mismatch error. 
	* 5 is id of checksum mismatch in error_type table
	* @return object Data Access Object
	*/
	public function writeError($id) {
		$sql = "UPDATE " . $this->table . " SET problem_file = 5 WHERE id = ?";
		$write_error = Yii::app()->db->createCommand()
			->execute(array($id));
	}
	
	/**
	* Create an MD5 or SHA1 checksum.  Default is MD5
	* @return string
	*/
	public function createChecksum($file, $type = 'md5') {
		$checksum = ($type == 'md5') ? md5_file($file) : sha1_file($file);
		
		return $checksum;
	}
	
	public function writeSuccess($checksum, $id) {
		$sql = "UPDATE " . $this->table . " SET checksum = ? WHERE id = ?";
		$write = Yii::app()->db->createCommand($sql)
			->execute(array($checksum, $id));
	}
	
	
	/**
	* Calculates a checksum for each file and compares it to the file's initial checksum
	* Write error to DB if mismatch detected. 
	*/
	public function actionMetaCheck() {
		$file_count = $this->getCheckedFileCount();
		
		if($file_count > 0) {
			$file_list = $this->getFileChecksums($file_count);
			
			foreach($file_list as $file) {
				$current_checksum = $this->createChecksum($file['temp_file_path']);
				if($current_checksum != $file['checksum']) {
					$this->writeError($file['id']);
				}
			}
		}
	}
	
	/**
	* Run checksum command 
	* Default is to create new checksum for downloaded files
	*/
	public function run($args) {
		$file_lists = $this->getFileList();
		
		if(count($file_lists) > 0) {
			foreach($file_lists as $file_list) {
				$checksum = $this->createChecksum($file_list['temp_file_path']);
				if($checksum) {
					$this->writeSuccess($checksum, $file_list['id']);
					echo "checksum for:" . $file_list['temp_file_path'] . " is " . $checksum . "\r\n";
				} else {
					$this->writeError($file_list['id']);
					echo "Checksum not created. for: " . $file_list['temp_file_path'] . "\r\n";
				}
			}
		}
	}
}