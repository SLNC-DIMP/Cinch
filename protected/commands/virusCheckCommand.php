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
	* Write file virus checked and if error detected to the db
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
	* Update virus definitions for ClamAV
	* @TODO may need sudo privileges
	* @access private
	*/
	private function updateDefs() {
		system(escapeshellcmd('freshclam'));
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
		exec('clamscan ' . $file_path, $output);
	 
		return $output;	
	}
	
	/**
	* Pull needed fields from virus scan output
	* $output returned as an array with fields in a predictable order
	* @param $output
	* @access private
	* @return array
	*/
	private function scanOutput(array $output) {
		$file_path = preg_replace('/:\s{1,}ok$/i', '', $output[0]);
		$num_infected = substr_replace(strrchr($output[7], ':'), '', 0, 2);
		
		return array('path' => $file_path, 'infected' => $num_infected);
	}
	
	/**
	* Write scan results
	* 11 is error code for Virus detected
	* 14 error for unable to delete file
	* @param $scan_results
	* @param $file_id
	* @param $user_id
	* @access private
	*/
	private function writeScan(array $scan_results, $file_id, $user_id) {
		$scan = $this->scanOutput($scan_results);
		if($scan['infected'] > 0) {
			ErrorFiles::writeError(11, $file_list['id'], $file_list['user_id']);
			$this->fileUpdate($file_id, 1);
			
			$delete = @unlink($scan['path']);
			if(!$delete) {
				ErrorFiles::writeError(14, $file_list['id'], $file_list['user_id']);
				echo "Virus detected, but not deleted! \r\n";
			} else {
				echo "Virus detected! \r\n";
			}
		} else {
			$this->fileUpdate($file_id);
			echo "No virus detected \r\n";
		}
	}
	
	public function run() {
		$files = $this->getFiles();
		if(empty($files)) { exit; }
		
		$this->updateDefs();
		foreach($files as $file) {
			$scan = $this->virusScan($file['temp_file_path']);
			if(!empty($scan)) {
				$this->writeScan($scan, $file['id'], $file['user_id']);
			} else {
				echo "scan failed\r\n";
			}
		}
	}
}