<?php
/**
* @todo this should probably be done last
*/
class purgeSystemCommand extends CConsoleCommand {
	public $file_info = 'file_info';
	
	/**
	* Gets files that are more than 30 days old for deletion
	* Trying to keep it DB agnostic, would use DATE_SUB with MySQL
	* @access public
	* @return object Yii DAO object
	*/
	public function filesToDelete() {
		$thirty_days = time() - (30 * 24 * 60 * 60);
		$files = Yii::app()->db->createCommand()
			->select('id, temp_file_path, problem_file')
			->from($this->file_info)
			->where(':download_time <= download_time', array(':download_time' => $thirty_days))
			->queryAll();
		
		return $files;
	}
	
	/**
	* Delete a downloaded (via Curl or FTP) file's information from the database.
	* These records are linked to a file on the server
	* @access protected
	* @return object Yii DAO object
	*/
	protected function clearDb($file_id) {
		$sql = "DELETE FROM " . $this->file_info . " WHERE id = ?";
		$write_zip = Yii::app()->db->createCommand($sql)
			->execute(array($file_id));
	}
	
	/**
	* Delete processed download lists, url lists, and ftp lists from the database.
	* These records aren't linked to a file on the server
	* @access protected
	* @return object Yii DAO object
	*/
	protected function clearLists($table) {
		$sql = "DELETE FROM $table WHERE processed = ?";
		$write_zip = Yii::app()->db->createCommand($sql)
			->execute(array(1));
	}
	
	/**
	* Remove file from the file system
	* @param $file_path
	* @access public
	* @return boolean
	*/
	public function removeFile($file_path, $file_id) {
		if(file_exists($file_path)) {
			$delete_file = @unlink($file_path);
			
			if($delete_file == false) {
				
			} 
		}
		$this->clearDb($file_id);
	}
	
	/**
	* Remove directory from the file system if empty accounting for . and .. files.
	* @param $dir_path
	* @access public
	* @return boolean
	*/
	public function removeDir($dir_path) {
		if(file_exists($dir_path) && count(scandir($dir_path)) == 2) {
			@rmdir($dir_path);
		}
	}
	
	public function run() {
		$this->clearLists('upload');
		$this->clearLists('files_for_download');
		
		$files = $this->filesToDelete();
		if(empty($files)) { exit; }
		
		foreach($files as $file) {
			$this->removeFile($file['temp_file_path'], $file['id']);
		}
	}
}