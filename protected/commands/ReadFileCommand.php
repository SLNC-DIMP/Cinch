<?php
class ReadFileCommand extends CConsoleCommand {
	/**
	 * Retrieves a list of uploaded files with url links that need to be downloaded
	 * @access public
	 * @return object Yii DAO
	 */
	public function getLists() {
		$get_file_lists = Yii::app()->db->createCommand()
			->select('*')
			->from('upload')
			->where('processed = :processed', array(':processed' => 0))
			->limit(1)
			->queryAll();
			
		return $get_file_lists;
	}
	
	/**
	* Writes URL listings, user id and list id to files_for_download table
	* @param $url
	* @param $user_uploads_id
	* @param $user_id
	* @access public
	* @return object Yii DAO
	*/
	public function addUrl(array $values) {
		if(trim($url)!='')
		{
			$sql = "INSERT INTO files_for_download(url, user_uploads_id, user_id) VALUES(?, ?, ?)";
			$write_files = Yii::app()->db->createCommand($sql)
				->execute($values);	
		}
	}
	
	public function writeFileCount($num_files, $list_id) {
		$sql = "UPDATE upload SET urls_in_list = ? WHERE id = ?";
		Yii::app()->db->createCommand($sql)
			->execute(array($num_files, $list_id));
	}
	
	
	/**
	* Update file list as processed
	* Doesn't use MySQL specific time function
	* @param $id
	* @access public
	* @return object Yii DAO
	*/
	public function updateFileList($id) {
	//	$process_time = date('Y-m-d H:i:s', time());
		$sql = "UPDATE upload SET processed = 1, process_time = NOW() WHERE id = :id";
		$write_files = Yii::app()->db->createCommand($sql);
	//	$write_files->bindParam(":process_time", $process_time, PDO::PARAM_INT);
		$write_files->bindParam(":id", $id, PDO::PARAM_INT);
		$write_files->execute();		
	}
	
	public function buildValues($url, $file_list, $user_id) {
		$values = array();
		$values[] = "$url, $file_list, $user_id";
		
		return $values;
	}
	
	/**
	* Process all unprocessed lists and add urls to database.  When list completes updates list as processed.
	* If a list fails during processing rereading list will pick up where the list died.
	*/
	public function run() {
    	$file_lists = $file_lists = $this->getLists();
		if(empty($file_lists)) { 
			echo "There are no download lists to process\r\n";
			exit; 
		}
     
		foreach($file_lists as $file_list) {
			$urls = file($file_list['path'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			
			if($file_list['last_url_processed'] > 0) {
				$urls = array_slice($urls, $file_list['last_url_processed']);
				$file_num_in_list = $file_list['last_url_processed'];
			} else {
				$files_in_list = count($urls);
				$this->writeFileCount($files_in_list, $file_list['id']);
				$file_num_in_list = 0;
			}
			
			$url_list = SplFixedArray::fromArray($urls);
			
			foreach($url_list as $url) {
				if(!filter_var($url, FILTER_VALIDATE_URL)) {
					$file_num_in_list++;
					continue;
				}
				
				$file_num_in_list++;  
				
				$values = $this->buildValues(strip_tags(trim($url)), $file_list['id'], $file_list['user_id']);
			}
			
			$this->addUrl($values);
			
			$total_files = (isset($files_in_list)) ? $files_in_list : $file_list['urls_in_list'];
			if($file_num_in_list == $total_files) {
				$this->updateFileList($file_list['id']);
			}
		} 	
    }
}