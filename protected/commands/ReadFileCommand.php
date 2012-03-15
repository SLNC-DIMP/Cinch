<?php
class ReadFileCommand extends CConsoleCommand {
	
	//public function __construct() {
//		ini_set('max_execution_time',0);
//		ini_set('memory_limit',-1);
//	}
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
			->limit(2)
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
	public function addUrl($url, $user_uploads_id, $user_id) {
		if(trim($url)!='')
		{
			$sql = "INSERT INTO files_for_download(url, user_uploads_id, user_id) VALUES(:url, :user_uploads_id, :user_id)";
			$write_files = Yii::app()->db->createCommand($sql);
			$write_files->bindParam(":url", $url, PDO::PARAM_STR);
			$write_files->bindParam(":user_uploads_id", $user_uploads_id, PDO::PARAM_INT);
			$write_files->bindParam(":user_id", $user_id, PDO::PARAM_INT);
			$write_files->execute();	
			
			return Yii::app()->db->lastInsertID;	
		}
	}
	
	public function writeFileCount($num_files) {
		$sql = "UPDATE upload SET urls_in_list = ?";
		$add_file_count = Yii::app()->db->createCommand($sql)
			->execute(array($num_files));
	}
	
	public function incrementFileNum($last_url_num) {
		$sql = "UPDATE upload SET last_url_processed = ?";
		$add_file_count = Yii::app()->db->createCommand($sql)
			->execute(array($last_url_num));
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
	
	/**
	* Process all unprocessed lists and add urls to database.  When list completes updates list as processed.
	*/
	public function run() {
    	$file_lists = $file_lists = $this->getLists();
		if(empty($file_lists)) { 
			echo "There are no download lists to process\r\n";
			exit; 
		}
     
		foreach($file_lists as $file_list) {
			$urls = file($file_list['path'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$url_list = SplFixedArray::fromArray($urls);
			$files_in_list = count($urls);
			$this->writeFileCount($files_in_list);
			
			$file_num_in_list = 1;
			foreach($url_list as $url) {
				if(!filter_var($url, FILTER_VALIDATE_URL)) {
					continue;
					$file_num_in_list++;
				}
				$this->incrementFileNum($i);
				$file_num_in_list++;
				$this->addUrl(strip_tags(trim($url)), $file_list['id'], $file_list['user_id']);
			}
			$this->updateFileList($file_list['id']);
		} 	
    }
}