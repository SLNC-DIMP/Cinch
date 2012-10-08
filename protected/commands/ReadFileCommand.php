<?php
/**
* ReadFileCommand class file
*
* This is the command for reading the links in a user's uploaded file into the database to be downloaded.
* @catagory Read File
* @package Read File
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/

/**
* This is the command for reading the links in a user's uploaded file into the database to be downloaded.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/
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
			->limit(3)
			->queryAll();
			
		return $get_file_lists;
	}
	
	/**
	* Writes URL listings, user id and list id to files_for_download table
	* @param array $values
	* @access public
	* @return object Yii DAO
	*/
	public function addUrls(array $values) {
		$num_inserts = count($values) / 8; // 8 is number of fields to insert
		$sql = "INSERT INTO files_for_download(url, jp2, pdfa, pdfa_convert, checksum_type, download_type, user_uploads_id, user_id) VALUES ";
		for($i=0; $i<$num_inserts; $i++) {
			$sql .= "(?, ?, ?, ?, ?, ?, ?, ?),";
		}
		$sql = preg_replace('/,$/', '', $sql);
		
		Yii::app()->db->createCommand($sql)
			->execute($values);	
	}
	
	/**
	* Writes URL file count to urls_in_list table
	* @param $num_files
	* @param $list_id
	* @access public
	* @return object Yii DAO
	*/
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
			$files_in_list = count($urls);
			$this->writeFileCount($files_in_list, $file_list['id']);
			$file_num_in_list = 0;
			$values = array();
			
			foreach($urls as $url) {
				if(!filter_var(trim($url), FILTER_VALIDATE_URL)) {
					$file_num_in_list++;
					continue;
				}

				$file_num_in_list++;  

                $values[] = trim($url);
                $values[] = $file_list['jp2'];
                $values[] = $file_list['pdfa'];
                $values[] = $file_list['pdfa_convert'];
                $values[] = $file_list['checksum_type'];
                $values[] = $file_list['download_type'];
				$values[] = $file_list['id'];
				$values[] = $file_list['user_id'];
			}

			$this->addUrls($values);
			
			if($file_num_in_list == $files_in_list) {
				$this->updateFileList($file_list['id']);
			}
		} 	
    }
}