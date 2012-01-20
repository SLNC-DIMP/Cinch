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
	* Move duplicate files from their current directory to their own directory under a users directory
	* Uses cp -p for Linux
	* Uses ROBOCOPY /COPYALL for Windows (won't copy files if permissions are different)
	* Creates directory if there isn't one and makes it writable.
	* @param $file_path
	* @access public
	*/
	public function moveDupes($file_path) {
		$split_path = preg_split('/(\/|\\\)/i', $file_path);
		$root_pieces_count = count($split_path) - 2;
		
		$base_path = '';
		for($i=0; $i<$root_pieces_count; $i++) {
			$base_path .= $split_path[$i] . '/';
		}
		$dup_dir = $base_path . 'duplicates';
		
		if(!file_exists($dup_dir)) {
			mkdir($dup_dir);
		//	chmod($dup_dir, 0777);
		}
		
		$split_path[$root_pieces_count] = 'duplicates';
		$new_path = implode('/', $split_path);
	
		if(strtoupper(substr(php_uname('s'), 0, 3)) !== 'WIN') {
			$command = "cp -p $file_path $new_path"; 
		} else {
			$root_path = '';
			for($i=0; $i<count($split_path) - 1; $i++) {
				$root_path .= $split_path[$i] . '/';
			}
			$base_path = substr_replace($root_path, '', -1); // strip trailing slash
			$file_name = end($split_path);
			$command = "ROBOCOPY $base_path $dup_dir $file_name /COPYALL"; 
		}
		
		$move_file = system(escapeshellcmd ($command));
		
		if($move_file == false) {
			return false;
		}
		
		if($checksum) {
			unlink($file_path);
		} else {
			unlink($new_path);
		}
		
		return $new_path;
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
	* 3 is value for "Duplicate Checksum"
	* 13 is value for "Unable to move file"
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
					$dup_move_path = $this->moveDupes($file_list['temp_file_path']);
					
					if($dup_move_path != false) {
						$this->checksum->writeDupMove($dup_move_path, $file_list['id']);
					} else {
						$this->checksum->writeError($file_list['id'], $file_list['user_id'], 13);
					}
					
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