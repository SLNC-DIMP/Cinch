<?php
class DownloadCommand extends CConsoleCommand {
	public $download_file_list = 'files_for_download';
	public $file_info_table = 'file_info';
	public $problem_downloads = 'problem_downloads';
	public $full_path;
	
	public function __construct() {
		$this->full_path = Yii::getPathOfAlias('application.curl_downloads');
	}
	
	/**
	* Retrieves a list of uploaded files with url links that need to be downloaded
	* @return object Data Access Object
	*/
	public function getUrls() {
		$get_file_list = Yii::app()->db->createCommand()
			->select('*')
			->from($this->download_file_list)
			->where('processed = :processed', array(':processed' => 0))
			->limit(10)
			->queryAll();
			
		return $get_file_list;
	}
	
	/**
	* Retrieve the username associated with a particular file
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
	*/
	public function setFileInfo($url, $curr_path, $last_mod, $user_id, $upload_file_id) {
		$dynamic_file = ($this->initFileType($url) == 1) ? 0 : 1;
		$sql = "INSERT INTO file_info(org_file_path, temp_file_path, dynamic_file, last_modified, user_id, upload_file_id) 
			VALUES(:url, :curr_path, :dynamic_file, :last_mod, :user_id, :upload_file_id)";
			
			$write_files = Yii::app()->db->createCommand($sql);
			$write_files->bindParam(":url", $url, PDO::PARAM_STR);
			$write_files->bindParam(":curr_path", $curr_path, PDO::PARAM_STR);
			$write_files->bindParam(":dynamic_file", $dynamic_file, PDO::PARAM_INT);
			$write_files->bindParam(":last_mod", $last_mod, PDO::PARAM_INT);
			$write_files->bindParam(":user_id", $user_id, PDO::PARAM_INT);
			$write_files->bindParam(":upload_file_id", $upload_file_id, PDO::PARAM_INT);
			$write_files->execute();
	}
	
	/**
	* Update file as processed
	*/
	public function updateFileList($id) {
		$sql = "UPDATE files_for_download SET processed = 1 WHERE id = :id";
		$write_files = Yii::app()->db->createCommand($sql);
		$write_files->bindParam(":id", $id, PDO::PARAM_INT);
		$write_files->execute();		
	}
	
/***************** End of Queries - Maybe move into a model ****************************************************/	
	/**
	* Removes illegal filename characters
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
	* @return string
	*/
	public function initFileType($file) {
		$file_info = pathinfo($file, PATHINFO_EXTENSION);
		if($file_info) {
			$file_type = 1;
		} else {
			$file_type = '.pdf';
		}
		
		return $file_type;
	}
	
	/**
	* Finds first directory files should be added to under a given user's main directory
	* @return string directory name
	*/
	public function getStartDir($file_list_owner) {
		$user_dir = $this->full_path . '/' . $file_list_owner;
		
		if(!file_exists($user_dir)) { mkdir($user_dir); }
		$dirs = scandir($user_dir);
		
		$dir_list = array();
		foreach($dirs as $dir) {
			// check for dirs starting with . character.
			if(is_dir($dir) && substr($dir, 0) == false) {
				$dir_list[] = $dir;
			}
		}
		
		$working_dir = (!empty($dir_list)) ?  end($dir_list) : $user_dir . '/' . $file_list_owner . '_0';
		if(!file_exists($user_dir . '/' . $file_list_owner . '_0')) { mkdir($user_dir . '/' . $file_list_owner . '_0'); }
		 
		return $working_dir;
	}
	
	/**
	* See if currently downloading directory needs to be updated if 500 file limit has been reached.
	* Takes the 2 default sub-directories into account.
	* @return string
	*/
	public function currentDir($current_dir) {
		$file_count = count(scandir($current_dir)) - 2;
		
		if($file_count < 500) {
			$working_dir = $current_dir;
		} else {
			$dir_suffix = strrchr($current_dir, '_');
			$dir_body = substr_replace($current_dir, '', - (int)strlen($dir_suffix));
			$next_dir_num = str_replace('_', '', $dir_suffix) + 1;
			$working_dir = $dir_body . '_' . $next_dir_num;
			
			if(!is_dir($working_dir)) { mkdir($working_dir); }
		}
		
		return $working_dir;
	}
	
	
	

	
	/**
	* opens a CURL connection and writes CURL contents to a file
	* Tries to get last modified of remote file
	* Rewrites file name from original url
	* @access public
	* @return array file info including: file type id, and boolean on whether it's a dynamic file. 
	* As well as File name and last modified time
	*/
	public function CurlProcessing($url, $current_user_id, $file_id) {
		$current_username = $this->getUrlOwner($current_user_id);
		$start_dir = $this->getStartDir($current_username);
		$current_dir = $this->currentDir($start_dir);
		$file_name = $this->cleanName($url);
		
		$file_path = $current_dir . '/' . $file_name;
		$ch = curl_init($url);
		$fp = @fopen($file_path, "wb");
				
	//	if($fp != false) {
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_FILETIME, 1);
					
			curl_exec($ch);
			
			if(!curl_errno($ch)) {
				$last_modified_time = curl_getinfo($ch, CURLINFO_FILETIME);
				$set_modified_time = $this->updateLastModified($file_path, $last_modified_time);
				$this->setFileInfo($url, $file_path, $set_modified_time, $current_user_id, $file_id);
			} else {
				if(curl_errno($ch) == 7 || curl_errno($ch) == 9) {
					$error = 1;
				} else {
					$error = 9;
				}
				$this->writeError($url, $error, $file_id, $current_user_id);
			}
			

			curl_close($ch);
			fclose($fp);
	//	}
		
		return array('full_path' => $file_path, 'last_mod_time' => $last_modified_time);
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
		
		foreach($urls as $url) {
			$download = $this->CurlProcessing($url['url'], $url['user_id'], $url['user_uploads_id']);
			
			if(is_array($download)) {
				$this->updateLastModified($download['full_path'], $download['last_mod_time']);
			} else {
				echo "Problem downloading: " . $url['url'] . "\r\n";
				continue;
			}
			$this->updateFileList($url['id']);
			echo $url['url'] . " downloaded\r\n";
		}
    }
}