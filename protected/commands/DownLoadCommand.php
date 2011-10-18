<?php
class DownloadCommand extends CConsoleCommand {
    public function run($args) {
        
    }
	
	/**
	 * Retrieves a list of uploaded files with url links that need to be downloaded
	 * @return object Data Access Object
	 */
	public function getList() {
		$get_file_list = Yii::app()->db->createCommand()
			->select('*')
			->from('files_for_download')
			->where('processed = :processed', array(':processed' => 0))
			->queryAll();
			
		return $get_file_list;
	}
	
	/**
	 * Pulls the urls from a file lists into arrays
	 * @return array
	 */
	public function readList() {
		$file_lists = $this->getList();
		
		foreach($file_lists as $file_list) {
			$url_list = file($file_list, FILE_SKIP_EMPTY_LINES);
		}
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
}