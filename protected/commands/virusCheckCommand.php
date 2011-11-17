<?php
class virusCheckCommand extends CConsoleCommand {
	/**
	* Get file list for virus checks
	* @access public
	* @return object Yii DAO
	*/
	public function getFiles() {
		$files = Yii::app()->db->createCommand()
			->select('id, temp_file_path')
			->from('file_info')
			->where(':virus_check = virus_check or :problem_file = problem_file', 
			  array(':virus_check' => 0, ':problem_file' => 1))
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
	* @param $file_path
	* @param $file_id
	* @access public
	* @return mixed
	*/
	public function virusScan($file_path, $file_id) {
		exec('' . $file_path, $output);
		if($output == false) {
			$this->fileUpdate($file_id, 11);
			unlink($file_path);
		} else {
			$this->fileUpdate($file_id);
		}
	}
	
	public function run() {
		$files = $this->getFiles();
		if(empty($files)) { exit; }
		
		foreach($files as $file) {
			$this->virusScan($file_path, $file_id);
		}
	}
}