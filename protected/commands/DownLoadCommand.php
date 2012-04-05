<?php
/**
* Blows up checksum command if not explcitly called.
*/
Yii::import('application.commands.ChecksumCommand');
Yii::import('application.models.Utils');

class DownloadCommand extends CConsoleCommand {
	public $download_file_list = 'files_for_download';
	public $file_info_table = 'file_info';
	public $problem_downloads = 'problem_downloads';
	public $remote_checksum;
	public $full_path;
	/**
	* max file size = 429496730 bytes 0.4 GB  Otherwise file might not fit into specified zip file limit
	* @var integer
	*/
	const FILE_SIZE_LIMIT = 429496730;
	
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
			->limit(4500)
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
	* See if a user has already downloaded a file with the same url before
	* They may or may not have the same checksum
	* @param $file_name
	* @param $user_id
	* @access public 
	* @return boolean
	*/
	public function sameName($file_name, $user_id) {
		$check_for_dup = Yii::app()->db->createCommand()
			->select("COUNT(*)")
			->from($this->file_info_table)
			->where("org_file_path = :file_name and user_id = :user_id", 
				array(":file_name" => $file_name, ":user_id" => $user_id))
			->queryColumn();
		
		return $check_for_dup[0];
	}
	
	/**
	* Inserts basic file information for downloaded file
	* @param $url
	* @param $curr_path
	* @param $last_mod
	* @param $user_id
	* @param $upload_file_id
	* @access public 
	* @return string last insert id
	*/
	public function setFileInfo($url, $remote_checksum, $user_id, $upload_file_id) {
		$dynamic_file = (is_numeric($this->initFileType($url))) ? 0 : 1;
		$sql = "INSERT INTO file_info(org_file_path, 
				remote_checksum,
				dynamic_file, 
				user_id, 
				upload_file_id) 
			VALUES(:url, :remote_checksum, :dynamic_file, :user_id, :upload_file_id)";
			
			$write_files = Yii::app()->db->createCommand($sql);
			$write_files->bindParam(":url", $url, PDO::PARAM_STR);
			$write_files->bindParam(":remote_checksum", $remote_checksum, PDO::PARAM_STR);
			$write_files->bindParam(":dynamic_file", $dynamic_file, PDO::PARAM_INT);
			$write_files->bindParam(":user_id", $user_id, PDO::PARAM_INT);
			$write_files->bindParam(":upload_file_id", $upload_file_id, PDO::PARAM_INT);
			$write_files->execute();
			
			return Yii::app()->db->lastInsertID;
	} 
	
	/**
	* Updates arbitrary number of basic file information fields for downloaded file
	* Pass in an associative array of field names and values
	* @TODO refactor this out into its own class or Utils so it can be used everywhere
	* @param $args array of fields/values to update
	* @access public 
	* @return string last insert id
	*/
	public function updateFileInfo(array $args, $file_id) {
		$fields = array_keys($args);
		$values = array_values($args);
		$values[] = $file_id;
		
		$sql = "UPDATE file_info ";
		foreach($fields as $key => $field) {
			if($key == 0) {
				$sql .= "SET $field = ?,";
			} else {
				$sql .= "$field = ?,";
			}
		}
		$sql = substr_replace($sql, ' ', -1); // remove trailing comma
		$sql .= "WHERE id = ?";
		
		Yii::app()->db->createCommand($sql)
			->execute($values);
		
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
	
/***************** End of Queries - Maybe move into a model ****************************************************/	
	/**
	* Current file should always return count of 1 if there are no duplicates.  
	* Current file should return count of 2+ if there are duplicates
	* Removes illegal filename characters
	* Event 2 is renamed file
	* @param $file
	* @param $file_id
	* @param $duplicate
	* @access public
	* @return string
	*/
	public function cleanName($file, $file_id, $duplicate =  1) {
		$patterns = array('/^(http|https):\/\//i', '/\s/');
		$replacements = array('', '_');
		$file_name = preg_replace($patterns, $replacements, $file);
		
		$other_replaces = array('?', '&', '=', '\\', '/', '(', ')', '{', '}', ';', ':',"'", '"', '%');
		$file_name = str_replace($other_replaces, '_', $file_name);
		
		$file_extension = $this->initFileType($file);
		
		if($file_extension == 1 && $duplicate > 1) {
			$file_type = strrchr($file_name, '.');
			$path_base = substr_replace($file_name, '', -strlen($file_type));
			$file_name = $path_base . '_dupname_' . mt_rand(1, 99999999) . $file_type;
		} elseif(is_string($file_extension) && $duplicate == 1) {
			$file_name = $file_name . $file_extension;
		} elseif(is_string($file_extension) && $duplicate > 0) {
			$file_name = $file_name . '_dupname_' . mt_rand(1, 99999999) . $file_extension;
		} elseif($file_extension == 2) {
			$file_name = $file_extension;
		}
		
		Utils::writeEvent($file_id, 2);
		
		return $file_name; 
	}
	
	/**
	* Returns self reporting file extension.  Defaults to PDF if no extension given.
	* Checks for allowed file types extensions
	* Certain extensions default to .pdf, so they may misreport real file type
	* @param $file
	* @access public
	* @return string
	*/
	public function initFileType($file) {
		$supported_extensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'png', 'gif', 'jpg', 'jpeg', 'txt', 'csv');
		$pdf_extensions = array('asp', 'aspx', 'php', 'jsp');
		$file_info = @pathinfo($file, PATHINFO_EXTENSION);
		
		if(in_array($file_info, $supported_extensions)) {
			$file_type = 1;
		} elseif($file_info == "" || in_array($file_info, $pdf_extensions) || is_null($file_info)) {
			$file_type = '.pdf';
		} else {
			$file_type = 2;
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
			natsort($dirs); // otherwise list gets sorted 1, 10, 2 instead of 1, 2, 10
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
		
		if($file_count < 500) {
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
	* Checks a file's size before trying to download it.
	* See http://stackoverflow.com/questions/2602612/php-remote-file-size-without-downloading-file
	* @param $url
	* @access protected
	* @return string
	*/
	protected function fileSize($url) {
		$file_size = 0;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$data = curl_exec($ch);
		curl_close($ch);
		
		if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
		  $file_size = (int)$matches[1];
		}

		$error = ($file_size < self::FILE_SIZE_LIMIT) ? false : true;
		
		return $error;
	}
	
	/**
	* Writes Curl/download errors to the db.
	* Error code 1 is unable to download
	* @param $url
	* @param $current_user_id
	* @param $file_id
	* @access private
	* @return string
	*/
	private function writeCurlError($file_id) {
		$error_id = 1;
		Utils::writeError($file_id, $error_id);
		$this->updateFileInfo(array('problem_file' => $error_id, 'events_frozen' => 1), $file_id);
		
		return $error_id;
	}
	
	/**
	* Gets current downloading directory of the current file owner
	* @param $current_user_id
	* @return string
	*/
	private function findCurUserDir($current_user_id) {
		$current_username = $this->getUrlOwner($current_user_id);
		$start_dir = $this->getStartDir($current_username);
		$current_dir = $this->currentDir($start_dir);
		
		return $current_dir;
	}
	
	/**
	* opens a CURL connection and writes CURL contents to a file
	* Tries to get last modified of remote file
	* Rewrites file name from original url
	* If file has a duplicate name of another file the user had previously downloaded a random number
	* is added to end of the file name so it won't overwrite previous file.
	* Event code 1 is file downloaded
	* Event code 13 Download failed
	* @param $user
	* @param $current_user_id
	* @param $file_list_id
	* @access public
	* @return array file info including: file type id, and boolean on whether it's a dynamic file. 
	* As well as File name and last modified time
	*/
	public function CurlProcessing($url, $current_user_id, $file_list_id) {
		$remote_checksum = $this->remote_checksum->createRemoteChecksum($url);
		$db_file_id = $this->setFileInfo($url, $remote_checksum, $current_user_id, $file_list_id);
		
		if($remote_checksum) {
			$dup_file = $this->sameName($url, $current_user_id);
			$file_name = $this->cleanName($url, $db_file_id, $dup_file);
			$file_size_limit = $this->fileSize($url);
		}	
		
		if($remote_checksum && $file_name != 2 && $file_size_limit == false) {
			$file_path = $this->findCurUserDir($current_user_id) . '/' . $file_name;
			
			$ch = curl_init($url);
			
			$fp = @fopen($file_path, "wb");
					
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_FILETIME, 1);
						
			curl_exec($ch);
			
			if(!curl_errno($ch)) {
				$last_modified_time = curl_getinfo($ch, CURLINFO_FILETIME);
				$set_modified_time = $this->updateLastModified($file_path, $db_file_id, $last_modified_time);
			
				$this->updateFileInfo(
					array('temp_file_path' => $file_path, 
					      'last_modified' => $set_modified_time), 
					      $db_file_id 
				);
				$return_vars = array('full_path' => $file_path, 'last_mod_time' => $last_modified_time);
				
			} else {
				$this->writeCurlError($db_file_id);
				@unlink($file_path);
				$return_vars = false;
			}
				
			curl_close($ch);
			fclose($fp);
			
			Utils::writeEvent($db_file_id, 1);
			
		
			return $return_vars; 
		
		} else {
			Utils::writeEvent($db_file_id, 13);
			$this->writeCurlError($db_file_id);
		}
	}
	
	/**
	* Finds true last modified date and set file back to that if changed during download/move process
	* Event code 3 is reset last modified time to correct value if possible
	* @param $file path to file
	* @param $last_modified_time Comes from CURL curl_getinfo($ch, CURLINFO_FILETIME). -1 is false.
	* @access public
	* @return string
	*/
	public function updateLastModified($file, $file_id, $last_modified_time) {
		if($last_modified_time != -1) {
			$file_mtime = $last_modified_time;
		} else {
			$file_mtime = filemtime($file);
		}
		
		$file_atime = fileatime($file);
		touch($file, $file_mtime, $file_atime);
		Utils::writeEvent($file_id, 3);
		
		return $file_mtime;
	}
	
	public function run() {
        $urls = $this->getUrls();
		if(empty($urls)) { exit; }
		
		foreach($urls as $key => $url) {
			
			$download = $this->CurlProcessing($url['url'],  $url['user_id'], $url['user_uploads_id']);
			
			if(is_array($download)) {
				echo $url['url'] . " downloaded\r\n";			
			} else {
				echo "Problem downloading: " . $url['url'] . "\r\n"; // text just a visual cue.
			}
			$this->updateFileList($url['id']); 
		}
    }
}