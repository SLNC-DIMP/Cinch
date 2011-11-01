<?php
class ChecksumCommand extends CConsoleCommand {
	public $table = 'file_info';
	
	/**
	* Get count of files in file info table
	* @return integer
	*/
	function getFileCount() {
		$get_file_count = Yii::app()->db->createCommand()
				->select("COUNT(*)")
				->from($this->table)
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
		$write_error = Yii::app()->db->createCommand()
				->update($this->table)
				->set(array('problem_file' => 5))
				->where('id = :id', array(':id' => $id));
	}
	
	/**
	* Create an MD5 or SHA1 checksum.  Default is MD5
	* @return string
	*/
	public function createChecksum($file, $type = 'md5') {
		$checksum = ($type = 'md5') ? md5_file($file) : sha1_file($file);
		
		return $checksum;
	}
	
	/**
	* Run checksum command 
	*/
	public function run() {
		$file_count = $this->getFileCount();
		
		if($file_count != 0) {
			$file_list = $this->getFileChecksums($file_count);
			
			foreach($file_list as $file) {
				$current_checksum = md5_file($file['temp_file_path']);
				if($current_checksum != $file['checksum']) {
					$this->writeError($file['id']);
				}
			}
		}
	}
}