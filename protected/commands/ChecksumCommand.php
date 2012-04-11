<?php
Yii::import('application.models.Utils');

class ChecksumCommand extends CConsoleCommand {
	public $checksum;
	
	public function __construct() {
		$this->checksum = new Checksum;
	}
	
	/**
	* Create an MD5 or SHA1 checksum.  Default is SHA1
	* Escapes weird filename characters
	* @param $file
	* @param $type
	* @param $remote
	* @access protected
	* @return string
	*/
	public function createChecksum($file, $type = 'sha1', $remote = false) {
		if($remote == true || file_exists("$file")) {
			$checksum = ($type == 'sha1') ? sha1_file("$file") : md5_file("$file");	
		} else {
			return false;	
		}
		
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
			$remote_checkum = $this->createChecksum($file, 'sha1', true);
			@fclose($fh);
		} else {
			$remote_checkum = false;
		}
	
		return $remote_checkum;
	}
	
	/**
	* Writes appropriate error to db
	* Error code 3 - Duplicate checksum found
	* Error code 17 - Duplicate filename found
	* @param $checksum_dup
	* @param $filename_dup
	* @access protected
	*/
	protected function errorWrite($checksum_dup, $filename_dup, $file_id) {
		if($checksum_dup > 0 && $filename_dup == 0) {
			$error_id = array(3);
		} elseif($checksum_dup > 0 && $filename_dup > 0) {
			$error_id = array(3, 17);
		} else {
			$error_id = array(17);
		}
		
		Utils::setProblemFile($file_id);
		
		foreach($error_id as $error) {
			Utils::writeError($file_id, $error);
		}
	}
	
	/**
	* Calculates a checksum for each file and compares it to the file's initial checksum
	* Write error to DB if mismatch detected.
	* 2 is error code for "Could not create checksum"
	* 5 is error code for file checksum mismatch
	* 11 is event code for checksum file integrity check
	* @access public 
	*/
	public function actionCheck() {
		$file_count = $this->checksum->getCheckedFileCount();
	
		if($file_count > 0) {
			$file_list = $this->checksum->getFileChecksums($file_count);
			
			foreach($file_list as $file) {
				$current_checksum = $this->checksum->createChecksum($file['temp_file_path']);
				
				if($current_checksum == false) {
					Utils::writeError($file['id'], 2);
					echo 'comparison checksum could not created for: ' . $file['temp_file_path'] . "\r\n";
				}elseif($current_checksum != $file['checksum']) {
					Utils::writeError($file['id'], 5);
					echo 'checksum not ok for: ' . $file['temp_file_path'] . "\r\n";
				} else {
					echo 'checksum ok for: ' . $file['temp_file_path'] . "\r\n";
				}
				Utils::writeEvent($file['id'], 11);
			}
		}
	}
	
	/**
	* Run checksum command 
	* Default is to create new checksum for downloaded files
	* Writes checksum error on failure. 
	* 2 is error code for "Could not create checksum"
	* Event type 5 is checksum created.
	*/
	public function actionCreate() {  // bug with errors not being reported to db.
		$file_lists = $this->checksum->getFileList();
		
		if(count($file_lists) > 0) {
			foreach($file_lists as $file_list) { 
				$checksum = $this->createChecksum($file_list['temp_file_path']);
				Utils::writeEvent($file_list['id'], 5);
				
				if($checksum != false) {
					$is_dup_checksum = $this->checksum->getDupChecksum($checksum, $file_list['user_id']);
					$is_dup_filename = preg_match('/_dupname_[0-9]{1,10}/', $file_list['temp_file_path']);
					
					if($is_dup_checksum != 0 || $is_dup_filename != 0) {
						$this->errorWrite($is_dup_checksum, $is_dup_filename, $file_list['id']);
					}
					
					echo "checksum for:" . $file_list['temp_file_path'] . " is " . $checksum . "\r\n";
				} else {
					$checksum = NULL;
					Utils::writeError($file_list['id'], 2);
					echo "Checksum not created. for: " . $file_list['temp_file_path'] . "\r\n";
				}	
				
				$this->checksum->writeSuccess($checksum, $file_list['id']);
			}
		}
	}
} 