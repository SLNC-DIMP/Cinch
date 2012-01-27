<?php
Yii::import('application.models.Utils');

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
			@fclose($fh);
		} else {
			$remote_checkum = NULL;
		}
		
		return $remote_checkum;
	}
	
	/**
	* Move duplicate files from their current directory to their own directory under a users directory
	* If a file has a duplicate checksum and duplicate filename it goes into duplicate checksum folder
	* Uses cp -p for Linux
	* Uses ROBOCOPY /COPYALL for Windows (won't copy files if permissions are different)
	* Creates directory if there isn't one and makes it writable.
	* Error code 3 - Duplicate checksum found
	* Error code 13 - Duplicate filename found
	* Event code 6 - file moved
	* @param $file_path
	* @access public
	*/
	public function moveDupes($file_path, $file_id, $checksum_dup = 0) {
		$split_path = preg_split('/(\/|\\\)/i', $file_path);
		$windows_root = $split_path; // needed by Windows OS only
		$root_pieces_count = count($split_path) - 2;
		
		$base_path = '';
		for($i=0; $i<$root_pieces_count; $i++) {
			$base_path .= $split_path[$i] . '/';
		}
		
		if($checksum_dup > 0) {
			$dup_dir_name = 'dup_checksum';
			$error_id = 3;
		} else {
			$dup_dir_name = 'dup_name';
			$error_id = 15;
		}
		
		$dup_dir_path = $base_path . $dup_dir_name;
		
		if(!file_exists($dup_dir_path)) {
			mkdir($dup_dir_path);
		}
		
		$split_path[$root_pieces_count] = $dup_dir_name;
		$new_path = implode('/', $split_path);
	
		if(strtoupper(substr(php_uname('s'), 0, 3)) !== 'WIN') {
			$command = "cp -p $file_path $new_path"; 
		} else {
			$root_path = '';
			for($i=0; $i<count($windows_root) - 1; $i++) {
				$root_path .= $windows_root[$i] . '/';
			}
			$base_path = substr_replace($root_path, '', -1); // strip trailing slash
			$file_name = end($windows_root);
		
			$command = "ROBOCOPY $base_path $dup_dir_path $file_name /COPYALL"; 
		}
		
		$move_file = system(escapeshellcmd($command));
		
		if($move_file == false) {
			Utils::writeError($file_id, 13);
			
			return false;
		}
	
		if($this->createChecksum($new_path) == $this->checksum->getOneFileChecksum($file_id)) {
			Utils::writeEvent($file_id, 6);
			Utils::writeError($file_id, $error_id);
			@unlink($file_path);
		} else {
			Utils::writeError($file_id, 13);
			@unlink($new_path);
			
			return false;
		}
		
		Utils::writeError($file_list['id'], 3);
					echo "Duplicate checksum found for: " . $file_list['temp_file_path'] . "\r\n";
		
		return $new_path;
	}
	
	/**
	* Calculates a checksum for each file and compares it to the file's initial checksum
	* Write error to DB if mismatch detected.
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
				if($current_checksum != $file['checksum']) {
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
	* 3 is error code for "Duplicate Checksum"
	* 13 is error code for "Unable to move file"
	* Event type 5 is checksum created.
	*/
	public function actionCreate($args) {
		$file_lists = $this->checksum->getFileList();
		
		if(count($file_lists) > 0) {
			foreach($file_lists as $file_list) { 
				$checksum = $this->createChecksum($file_list['temp_file_path']);
				
				if($checksum) {
					Utils::writeEvent($file_list['id'], 5);
					
					$is_dup_checksum = $this->checksum->getDupChecksum($checksum, $file_list['user_id']);
					$is_dup_filename = preg_match('/_dupname_[0-9]{1,10}/', $file_list['temp_file_path']);
					
					$this->checksum->writeSuccess($checksum, $file_list['id']);
					echo "checksum for:" . $file_list['temp_file_path'] . " is " . $checksum . "\r\n";
					
					if($is_dup_checksum != 0 || $is_dup_filename != 0) {
						$dup_move_path = $this->moveDupes($file_list['temp_file_path'], $file_list['id'], $is_dup_checksum);
					
						if($dup_move_path != false) {
							$this->checksum->writeDupMove($dup_move_path, $file_list['id']);
						} else {
							echo "Duplicate file: " . $file_list['temp_file_path'] . " couldn't be moved\r\n";
							Utils::writeError($file_list['id'], 13);
						}
					}
				} else {
					Utils::writeError($file_list['id'], 2);
					echo "Checksum not created. for: " . $file_list['temp_file_path'] . "\r\n";
				}
			}
		}
	}
} // update `files_for_download` set `processed`=0 WHERE `processed`=1