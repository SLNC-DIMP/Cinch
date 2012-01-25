<?php
Yii::import('application.models.ErrorFiles');

class virusCheckCommand extends CConsoleCommand {
	/**
	* Get file list for virus checks
	* @access public
	* @return object Yii DAO
	*/
	public function getFiles() {
		$files = Yii::app()->db->createCommand()
			->select('id, temp_file_path, user_id')
			->from('file_info')
			->where(':virus_check = virus_check AND :temp_file_path != temp_file_path', 
			  array(':virus_check' => 0, ':temp_file_path' => ''))
			->queryAll();
			
		return $files;
	}
	
	/**
	* Write file viruse checked and if error detected to the db
	* @param $file_id
	* @param $problem
	* @access protected
	* @return object Yii DAO
	*/
	protected function fileUpdate($file_id, $problem = 0) {
		$sql = "UPDATE file_info SET virus_check = 1, problem_file = ? WHERE id = ?";
		
		$virus = Yii::app()->db->createCommand($sql);
		$virus->execute(array($problem, $file_id));	
	}
	
	/**
	* Scan a file for viruses
	* 11 is error code for Virus detected
	* 14 error for unable to delete file
	* @param $file_path
	* @param $file_id
	* @access public
	* @return mixed
	*/
	public function virusScan($file_path) {
		exec('' . $file_path, $output);
		
		return $output;	
	}
	
	public function writeScan($file_path, $file_id, $user_id) {
		if($output == false) {
			ErrorFiles::writeError(11, $file_list['id'], $file_list['user_id']);
			$this->fileUpdate($file_id, 1);
			
			$delete = @unlink($file_path);
			if(!$delete) {
				ErrorFiles::writeError(14, $file_list['id'], $file_list['user_id']);
			}
		} else {
			$this->fileUpdate($file_id);
		}
	}
	
	public function run() {
		$files = $this->getFiles();
		if(empty($files)) { exit; }
		
		foreach($files as $file) {
			$this->virusScan($file['temp_file_path']);
			$this->writeScan($file['temp_file_path'], $file['id'], $file['user_id']);
		}
	}
}