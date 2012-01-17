<?php
class ChecksumCommand extends CConsoleCommand {
	public $checksum;
	
	public function __construct() {
		$this->checksum = new Checksum;
	}
	
	/**
	* Create an MD5 or SHA1 checksum.  Default is SHA1
	* @param $file
	* @param $type
	* @access protected
	* @return string
	*/
	protected function createChecksum($file, $type = 'sha1') {
		$checksum = ($type == 'sha1') ? sha1_file($file) : md5_file($file);
		
		return $checksum;
	}
	
	/**
	* Create remote checksum to compare with downloaded version.  
	* Also acts as check to see if file exists.  
	* Supress file open warning on failure.
	* @param $file
	* @access public
	* @return string
	*/
	public function createRemoteChecksum($file) {
		$fh = @fopen($file, 'r');
		if($fh != false) {
			$remote_checkum = $this->createChecksum($file);
			fclose($fh);
		} else {
			return false;
		}
		
		return $remote_checkum;
	}
	
	/**
	* Calculates a checksum for each file and compares it to the file's initial checksum
	* Write error to DB if mismatch detected.
	* @access public 
	*/
	public function actionCheck() {
		$file_count = $this->checksum->getCheckedFileCount();
	
		if($file_count > 0) {
			$file_list = $this->checksum->getFileChecksums($file_count);
			
			foreach($file_list as $file) {
				$current_checksum = $this->checksum->createChecksum($file['temp_file_path']);
				if($current_checksum != $file['checksum']) {
					$this->checksum->writeError($file['id'], $file['user_id']);
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
		$file_lists = $this->checksum->getFileList();
		
		if(count($file_lists) > 0) {
			foreach($file_lists as $file_list) { 
				$checksum = $this->createChecksum($file_list['temp_file_path']);
				$is_duplicate = $this->checksum->getDupChecksum($checksum, $file_list['user_id']);
				
				if($checksum && !$is_duplicate) {
					$this->checksum->writeSuccess($checksum, $file_list['id']);
					echo "checksum for:" . $file_list['temp_file_path'] . " is " . $checksum . "\r\n";
				} elseif($checksum && $is_duplicate) {
					$this->checksum->writeError($file_list['id'], $file_list['user_id'], 3);
					echo "Duplicate checksum found for: " . $file_list['temp_file_path'] . "\r\n";
				} else {
					$this->checksum->writeError($file_list['id'], $file_list['user_id'], 2);
					echo "Checksum not created. for: " . $file_list['temp_file_path'] . "\r\n";
				}
			}
		}
	}
}