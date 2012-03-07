<?php
/**
* Blows up command if not explcitly called.  I believe because MakeCsv isn't named MakeCsvCommand
* Don't want it named that as would show up as a command instead of merely being clase for others to descend from.
*/
Yii::import('application.models.MakeCsv');

class ErrorCsvCommand extends CConsoleCommand {
	public $makecsv;
	
	public function __construct() {
		$this->makecsv = new MakeCsv;
	}
	
	/**
	* Gets user files that have errors
	* @access public
	* @return object Yii DAO
	*/
	public function getErrorFiles() {
		$sql = "SELECT org_file_path, temp_file_path, error_message, file_info.user_id
				FROM file_info, problem_files, (
					SELECT *
					FROM error_type
				) AS error_list
				WHERE file_info.id = problem_files.file_id
				AND error_list.id = problem_files.error_id
				AND problem_file = 1
				AND zipped != 1"; 
		
		$error_list = Yii::app()->db->createCommand($sql)
			->queryAll();
		
		return $error_list;
	}
	
	/**
	* Writes user error file list to a csv file and add csv file path to db.
	* Writes column headers only on first iteration
	* @param $file
	* @param $user_path
	* @access public
	*/
	public function makeReport($file, $user_path) {
		if(!empty($file)) {
			$error_file = $user_path . '/error_files_' . date('Y_m_d') . '.csv';
			
			if(!file_exists($error_file)) {
				$headers = array('Url', 'Filename', 'Error Message');
			} else {
				$headers = 'Column headers already set';
			}
			$fh = fopen($error_file, 'ab');
			
			if(is_array($headers)) {
				fputcsv($fh, $headers);
				$this->makecsv->addPath($file['user_id'], $error_file);
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
				$csv_path = $this->makecsv->getUserPath($error['user_id']);
				$this->makeReport($error, $csv_path);
			}
		}
	}
}