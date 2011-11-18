<?php
/**
* Blows up command if not explcitly called.  I believe because MakeCsv isn't named MakeCsvCommand
* Don't want it named that as would show up as a command instead of merely being clase for others to descend from.
*/
Yii::import('application.commands.MakeCsv');

class ErrorCsvCommand extends MakeCsv {
	/**
	* Gets user files that have errors
	* @access public
	* @return object Yii DAO
	*/
	public function getErrorFiles() {
		$sql = "SELECT org_file_path, temp_file_path, error_message, user_id
			FROM file_info, error_type
			WHERE file_info.problem_file = error_type.id"; 
		
		$error_list = Yii::app()->db->createCommand($sql)
			->queryAll();
		
		return $error_list;
	}
	
	/**
	* Writes user error file list to a csv file and add csv file path to db.
	* Writes column headers only on first iteration
	* @access public
	*/
	public function makeReport($file, $user_path) {
		if(!empty($file)) {
			$error_file = $user_path . '/error_files.csv';
			
			if(!file_exists($error_file)) {
				$headers = array('Url', 'Filename', 'Error Message');
			} else {
				$headers = 'Column headers already set';
			}
			$fh = fopen($error_file, 'ab');
			
			if(is_array($headers)) {
				fputcsv($fh, $headers);
				$this->addPath($file['user_id'], $error_file);
			}
				
			if($file['temp_file_path'] != '') {
				$file_path = str_replace('/', '', strrchr($file['temp_file_path'], '/'));
			} else {
				$file_path = '';
			}
			fputcsv($fh, array($file['org_file_path'], $file_path, $file['error_message']));
			fclose($fh);
		}
	}
	
	/**
	* Writes each user error file list to correct csv file
	* @access public
	*/
	public function actionIndex() {
		$errors = $this->getErrorFiles();
		
		if(!empty($errors)) {
			foreach($errors as $error) {
				$csv_path = $this->getUserPath($error['user_id']);
				$this->makeReport($error, $csv_path);
			}
		}
	}
}