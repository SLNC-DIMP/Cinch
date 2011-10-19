<?php
class DownloadCommand extends CConsoleCommand {
	public function __construct() {
		parent::__construct();
		$this->error = new Error();
	}
	
	/**
	* Retrieves a list of uploaded files with url links that need to be downloaded
	* @return object Data Access Object
	*/
	public function getUrls() {
		$get_file_list = Yii::app()->db->createCommand()
			->select('*')
			->from('files_for_download')
			->where('processed = :processed', array(':processed' => 0))
			->queryAll();
			
		return $get_file_list;
	}
	
	public function cleanName($file) {
		$patterns = array('/^(http|https):\/\//i', '\s');
		$replacments = array('', '_');
		$file_name = preg_replace($patterns, $replacments, $file);
		
		return $clean_name;
	}
	
	/**
	* Update url as processed
	*/
	public function updateFileList($id) {
		$sql = "UPDATE files_for_download SET processed = 1 WHERE id = :id)";
		$write_files = Yii::app()->db->createCommand($sql);
		$write_files->bindParam(":id", $id, PDO::PARAM_INT);
		$write_files->execute();		
	}
	
	/**
	* Write file error
	*/
	public function writeError($error, $id) {
		$sql = "UPDATE file_info SET problem_file = :error WHERE id = :id)";
		$error = Yii::app()->db->createCommand($sql);
		$error->bindParam(":error", $error, PDO::PARAM_INT);
		$error->bindParam(":id", $id, PDO::PARAM_INT);
		$error->execute();		
	}
	
	/**
	* opens a CURL connection and writes CURL contents to a file
	* Tries to get last modified of remote file
	* Rewrites file name from original url
	* @access public
	* @return array file info including: file type id, and boolean on whether it's a dynamic file. 
	* As well as File name and last modified time
	*/
	public function cURLProcessing($file) {
		$file_path = Yii::getPathOfAlias('application.curl_downloads');
		$ch = curl_init($file);
				
		$file_info = $this->cleanFilename($file, $total_files_processed);
		$file_name = $this->download_path . $folder_info . "/" . $file_info['filename'];
					
		$fp = @fopen($file_name, "wb");
				
		if($fp != false) {
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_FILETIME, 1);
					
			curl_exec($ch);
			$last_modified_time = curl_getinfo($ch, CURLINFO_FILETIME);
			curl_close($ch);
					
			fclose($fp);
		} else {
			
		}
		
		return array('file_info' => $file_info, 'file_name' => $file_name, 'last_mod_time' => $last_modified_time);
	}
	
	public function run($args) {
        
    }
}