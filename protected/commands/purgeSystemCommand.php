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
	* Delete a files information from the database.
	* @access protected
	* @return object Yii DAO object
	*/
	protected function clearDb($file_id) {
		$sql = "DELETE FROM " . $this->file_info . " WHERE id = ?";
		$write_zip = Yii::app()->db->createCommand($sql)
			->execute(array($file_id));
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
		$files = $this->filesToDelete();
		
		foreach($files as $file) {
			$this->removeFile($file['temp_file_path'], $file['id']);
		}
	}
}