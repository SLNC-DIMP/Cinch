<?php
Yii::import('application.models.Utils');

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
		
		if($problem > 0) {
			$sql = "UPDATE file_info SET temp_file_path = '', virus_check = 1, problem_file = ? WHERE id = ?";
		}
		
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
	* Wrap path in double quotes or it will probably fail if weird characters in file name.
	* 4 event code for virus scan run
	* @param $file_path
	* @param $file_id
	* @access public
	* @return mixed
	*/
	public function virusScan($file_path, $file_id) {
		$output = array();
		exec('clamdscan ' . "$file_path", $output);
	 	Utils::writeEvent($file_id, 4);
		
		$output['path'] = $file_path;
		$output['file_id'] = $file_id;
		
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
		if(isset($output[3])) {
			$output['infected'] = substr_replace(strrchr($output[3], ':'), '', 0, 2);
		} 
		
		return $output;
	}
	
	/**
	* Write scan results
	* 11 is error code for Virus detected
	* 14 error for unable to delete file
	* 16 Virus check couldn't scan file
	* @param $scan_results
	* @param $file_id
	* @param $user_id
	* @access private
	*/
	private function writeScan(array $scan_results) {
		$scan = $this->scanOutput($scan_results);
	
		if(!isset($scan['infected']) || $scan['infected'] > 0) {
			if(!isset($scan['infected'])) {
				$error_id = 16;
				$message_text = "Scan failed";
			} else {
				$error_id = 11;
				$message_text = "Virus detected";
			}
			
			Utils::writeError($scan['file_id'], $error_id);
			
			$delete = @unlink("{$scan['path']}");
			
			if(!$delete) {
				Utils::writeError($scan['file_id'], 14);
				echo $message_text . ", but file could not be deleted! -" .  $scan['file_id'] . "\r\n";
				exit;
			} else {
				echo $message_text . "! File deleted -" . $scan['file_id'] . "\r\n";
			}
			$this->fileUpdate($scan['file_id'], 1);
		} else {
			$this->fileUpdate($scan['file_id']);
			echo "No virus detected -" . $scan['file_id'] . "\r\n";
		}
	}
	
	public function run() {
		$files = $this->getFiles();
		if(empty($files)) { exit; }
		
	//	$this->updateDefs();
		foreach($files as $file) {
			$scan = $this->virusScan($file['temp_file_path'], $file['id']);
			$this->writeScan($scan);
		}
	}
}