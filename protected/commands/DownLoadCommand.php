<?php
/**
* Blows up command if not explcitly called.
*/
Yii::import('application.commands.ChecksumCommand');

class DownloadCommand extends CConsoleCommand {
	public $download_file_list = 'files_for_download';
	public $file_info_table = 'file_info';
	public $problem_downloads = 'problem_downloads';
	public $remote_checksum;
	public $full_path;
	
	public function __construct() {
		$this->remote_checksum = new ChecksumCommand;
		$this->full_path = Yii::getPathOfAlias('application.curl_downloads');
	}
	
	/**
	* Retrieves a list of uploaded files with url links that need to be downloaded
	* @access public 
	* @return object Yii DAO
	*/
	public function getUrls() {
		$get_file_list = Yii::app()->db->createCommand()
			->select('*')
			->from($this->download_file_list)
			->where('processed = :processed', array(':processed' => 0))
			//->limit(3)
			->queryAll();
			
		return $get_file_list;
	}
	
	/**
	* Retrieve the username associated with a particular file
	* @param $user_id
	* @access public 
	* @return string
	*/
	public function getUrlOwner($user_id) {
		$get_user = Yii::app()->db->createCommand()
			->select('username')
			->from('user')
			->where('id = :id', array(':id' => $user_id))
			->queryColumn();
			
		return $get_user[0];
	}
	
	/**
	* Inserts basic file information for downloaded file
	* @param $url
	* @param $curr_path
	* @param $last_mod
	* @param $user_id
	* @param $upload_file_id
	* @access public 
	* @return object Yii DAO
	*/
	public function setFileInfo($url, $curr_path, $remote_checksum, $last_mod, $user_id, $upload_file_id, $problem_file = 0) {
		$dynamic_file = ($this->initFileType($url) == 1) ? 0 : 1;
		$sql = "INSERT INTO file_info(org_file_path, 
				temp_file_path, 
				remote_checksum,
				dynamic_file, 
				last_modified, 
				problem_file, 
				user_id, 
				upload_file_id) 
			VALUES(:url, :curr_path, :remote_checksum, :dynamic_file, :last_mod, :problem_file, :user_id, :upload_file_id)";
			
			$write_files = Yii::app()->db->createCommand($sql);
			$write_files->bindParam(":url", $url, PDO::PARAM_STR);
			$write_files->bindParam(":curr_path", $curr_path, PDO::PARAM_STR);
			$write_files->bindParam(":remote_checksum", $remote_checksum, PDO::PARAM_STR);
			$write_files->bindParam(":dynamic_file", $dynamic_file, PDO::PARAM_INT);
			$write_files->bindParam(":last_mod", $last_mod, PDO::PARAM_INT);
			$write_files->bindParam(":problem_file", $problem_file, PDO::PARAM_INT);
			$write_files->bindParam(":user_id", $user_id, PDO::PARAM_INT);
			$write_files->bindParam(":upload_file_id", $upload_file_id, PDO::PARAM_INT);
			$write_files->execute();
			
			return Yii::app()->db->lastInsertID;
	}
	
	/**
	* Update file as processed
	* @param $id
	* @access public 
	* @return object Yii DAO
	*/
	public function updateFileList($id) {
		$sql = "UPDATE files_for_download SET processed = 1 WHERE id = :id";
		$write_files = Yii::app()->db->createCommand($sql);
		$write_files->bindParam(":id", $id, PDO::PARAM_INT);
		$write_files->execute();		
	}
	
	/**
	* On error write file info to problem_downloads table
	* @param $url
	* @param $error
	* @param $list_id
	* @param $current_user_id
	* @access public 
	* @return object Yii DAO
	*/
	public function writeError($error_id, $file_id, $current_user_id) {
		$sql = "INSERT INTO problem_files(error_id, file_id, user_id) 
			VALUES(:error_id, :file_id, :user_id)";
		$error = Yii::app()->db->createCommand($sql)
			->bindParam(":error_id", $error_id, PDO::PARAM_INT)
			->bindParam(":file_id", $file_id, PDO::PARAM_INT)
			->bindParam(":user_id", $current_user_id, PDO::PARAM_INT)
			->execute();
	}
	
/***************** End of Queries - Maybe move into a model ****************************************************/	
	/**
	* Removes illegal filename characters
	* @param $file
	* @access public
	* @return string
	*/
	public function cleanName($file) {
		$patterns = array('/^(http|https):\/\//i', '/(\/|\s|\?|&|=|\\\)/');
		$replacments = array('', '_');
		$file_name = preg_replace($patterns, $replacments, $file);
		$file_extension = $this->initFileType($file);
		
		if($file_extension != 1) { $file_name = $file_name . $file_extension; }

		return $file_name; 
	}
	
	/**
	* Returns self reporting file extension.  Defaults to PDF if no extension given.
	* Checks for some base url extensions
	* $file_info length should sort out country codes
	* @param $file
	* @access public
	* @return string
	*/
	public function initFileType($file) {
		$bad_extensions = array('gov', 'com', 'edu', 'info', 'org');
		$file_info = pathinfo($file, PATHINFO_EXTENSION);
		
		if($file_info && !in_array($file_info, $bad_extensions) && strlen($file_info) > 2) {
			$file_type = 1;
		} else {
			$file_type = '.pdf';
		}
		
		return $file_type;
	}
	
	/**
	* Finds first directory files should be added to under a given user's main directory
	* Removes non-directory files from the list
	* Regex checks that download directory ends with an underscore and number
	* As it's possible to have other directories under user's root directory
	* @param $file_list_owner
	* @access public
	* @return string directory name
	*/
	public function getStartDir($file_list_owner) {
		$user_base_dir = $this->full_path . '/' . $file_list_owner;
		if(!file_exists($user_base_dir)) { 
			mkdir($user_base_dir); 
		}
		

		$first_download_dir = $user_base_dir . '/' . $file_list_owner . '_0';
		if(!file_exists($first_download_dir)) { 
			mkdir($first_download_dir); 
		} 
		
		$dir_listing = scandir($user_base_dir);
		foreach($dir_listing as $dir) {
			if(is_dir($dir)) {
				$dirs[] = $dir;
			}
		}
		
		$root_dir = new DirectoryIterator($user_base_dir);
		$dirs = array();
		foreach ($root_dir as $dir) {
			if ($dir->isDir() && !$dir->isDot() && preg_match('/_[0-9]{1,}$/i', $dir->getFilename())) {
				$dirs[] = $dir->getFilename();
			}
		}
		if(!empty($dirs)) {
			natsort($dirs); // otherwise list gets sorted 1, 10, 2 instead of 1,2, 10
			$working_dir = $user_base_dir . '/' . end($dirs);
		} else {
			$working_dir = $first_download_dir;
		}
		
		return $working_dir;
	}
	
	/**
	* See if currently downloading directory needs to be updated if 500 file limit has been reached.
	* Takes the 2 default sub-directories into account.
	* @param $current_dir
	* @access public
	* @return string
	*/
	public function currentDir($current_dir) {
		$file_count = count(scandir($current_dir)) - 2;
		echo $file_count;
		if($file_count < 5) {
			$working_dir = $current_dir;
		} else {
			$dir_suffix = strrchr($current_dir, '_');
			
			$dir_body = substr_replace($current_dir, '', - (int)strlen($dir_suffix));
			$next_dir_num = str_replace('_', '', $dir_suffix) + 1;
			$working_dir = $dir_body . '_' . $next_dir_num;
			
			if(!file_exists($working_dir)) { mkdir($working_dir); }
		}
		
		return $working_dir;
	}
	
	/**
	* Checks to see if a file exists before trying to download it.
	* @param $url
	* @access protected
	* @return string
	*/
	protected function fileExists($url) {
		$fn = @fopen($url, 'r');
		$error_id = ($fn != false) ? 0 : 1;
		if($fn) { fclose($fn); }
		
		return $error_id;
	}
	
	/**
	* Writes Curl/download errors to the db.
	* @param $url
	* @param $current_user_id
	* @param $file_id
	* @access private
	* @return string
	*/
	private function writeCurlError($url, $current_user_id, $file_id) {
		$error_id = 1;
		$current_insert = $this->setFileInfo($url, '', NULL, 0, $current_user_id, $file_id, 1);
		$this->writeError($error_id, $current_insert, $current_user_id);
		
		return $error_id;
	}
	
	/**
	* opens a CURL connection and writes CURL contents to a file
	* Tries to get last modified of remote file
	* Rewrites file name from original url
	* Error 1 - unable to download
	* @param $user
	* @param $current_user_id
	* @param $file_id
	* @access public
	* @return array file info including: file type id, and boolean on whether it's a dynamic file. 
	* As well as File name and last modified time
	*/
	public function CurlProcessing($url, $current_user_id, $file_id, $file_list_id) {
		$remote_checksum = $this->remote_checksum->createRemoteChecksum($url);
		
		if($remote_checksum != false) {
			$current_username = $this->getUrlOwner($current_user_id);
			$start_dir = $this->getStartDir($current_username);
			$current_dir = $this->currentDir($start_dir);
			echo $current_dir . "\r\n";
			$file_name = $this->cleanName($url);
			$file_path = $current_dir . '/' . $file_name;
			
			$ch = curl_init($url);
			
			$fp = @fopen($file_path, "wb");
					
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_FILETIME, 1);
						
			curl_exec($ch);
			
			if(!curl_errno($ch)) {
				$last_modified_time = curl_getinfo($ch, CURLINFO_FILETIME);
				$set_modified_time = $this->updateLastModified($file_path, $last_modified_time);
				$this->setFileInfo($url, $file_path, $remote_checksum, $set_modified_time, $current_user_id, $file_list_id);
			} else {
				$curl_error = curl_errno($ch);
				$this->writeCurlError($url, $current_user_id, $file_id);
			}
				
			curl_close($ch);
			fclose($fp);
			
			if(isset($curl_error)) { 
				@unlink($file_path); 
				return;
			}
		
			return array('full_path' => $file_path, 'last_mod_time' => $last_modified_time); 
		
		} else {
			$this->writeCurlError($url, $current_user_id, $file_id);
		}
	}
	
	/**
	* Finds true last modified date and set file back to that if changed during download/move process
	* @param $file path to file
	* @param $last_modified_time Comes from CURL curl_getinfo($ch, CURLINFO_FILETIME). -1 is false.
	* @access public
	* @return string
	*/
	public function updateLastModified($file, $last_modified_time) {
		if($last_modified_time != -1) {
			$file_mtime = $last_modified_time;
		} else {
			$file_mtime = filemtime($file);
		}
		
		$file_atime = fileatime($file);
		touch($file, $file_mtime, $file_atime);
		
		return $file_mtime;
	}
	
	public function run() {
        $urls = $this->getUrls();
		if(empty($urls)) { exit; }
		
		foreach($urls as $url) {
			$download = $this->CurlProcessing($url['url'],  $url['user_id'], $url['id'], $url['user_uploads_id']);
			
			if(is_array($download)) {
		//		echo $url['url'] . " downloaded\r\n";			
			} else {
		//		echo "Problem downloading: " . $url['url'] . "\r\n"; // text just a visual cue.  Can remove else statement
			}
			$this->updateFileList($url['id']); 
		}
    }
}