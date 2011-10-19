<?php
class ChecksumCommand extends CConsoleCommand {
	/**
	* Get count of files in file info table
	* @return integer
	*/
	function getFileCount($table) {
		$get_file_count = Yii::app()->db->createCommand()
				->select("COUNT(*)")
				->from($table)
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
			->from('file_info')
			->limit($start, $total)
			->queryAll();
			
		return $get_file_lists;
	}
	
	public function run() {
		$file_count = $this->getFileCount('file_info');
		
		if($file_count != 0) {
			$file_list = $this->getFileChecksums($file_count);
			
			foreach($file_list as $file) {
				$current_checksum = md5_file($file['temp_file_path']);
			}
		}
	}
}