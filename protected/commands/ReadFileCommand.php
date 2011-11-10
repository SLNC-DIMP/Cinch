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
		}
	}
	
	/**
	* Update file list as processed
	* @param $id
	* @access public
	* @return object Yii DAO
	*/
	public function updateFileList($id) {
		$sql = "UPDATE upload SET processed = 1 WHERE id = :id";
		$write_files = Yii::app()->db->createCommand($sql);
		$write_files->bindParam(":id", $id, PDO::PARAM_INT);
		$write_files->execute();		
	}
	
	/**
	* Process all unprocessed lists and add urls to database.  When list completes updates list as processed.
	*/
	public function run() {
     $file_lists = $file_lists = $this->getLists();
     
		foreach($file_lists as $file_list) {
			$url_list = SplFixedArray::fromArray(file($file_list['upload_path'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

			foreach($url_list as $url) {
				if(trim($url)=='')
				{
					continue;
				}
				
				$this->addUrl(strip_tags(trim($url)), $file_list['id'], $file_list['user_id']);
			
			}
			$this->updateFileList($file_list['id']);
		} 	
    }
}