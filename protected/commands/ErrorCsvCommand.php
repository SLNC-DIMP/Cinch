<?php
Yii::import('application.models.MakeCsv');

/**
 * This is the command for creation of error csv manifest for a user's downloaded files.
 * @author Dean Farrell
 * @license CC0 1.0 Universal {@link http://creativecommons.org/publicdomain/zero/1.0/}
 */
class ErrorCsvCommand extends CConsoleCommand {
	/**
	* @var $makecsv
	*/
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
		$sql = "SELECT problem_files.id AS error_key, org_file_path, temp_file_path, error_message, file_info.user_id
				FROM file_info, problem_files, (
					SELECT *
					FROM error_type
				) AS error_list
				WHERE file_info.id = problem_files.file_id
				AND error_list.id = problem_files.error_id
				AND problem_file = 1
				AND csv_added = 0"; 
		
		$error_list = Yii::app()->db->createCommand($sql)
			->queryAll();
		
		return $error_list;
	}
	
	/**
	* Updates the problem file table to show that the entry has been added to error csv file.
	* @param $file_id
	* @access private
	*/
	private function csvAdded($file_id) {
		$sql = "UPDATE problem_files SET csv_added = 1 WHERE id = ?";
		Yii::app()->db->createCommand($sql)
			->execute(array($file_id));
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
				$this->csvAdded($error['error_key']);
				echo "File " . $error['error_message'] . '-' . $error['org_file_path'] . " added to \r\n";
				echo "$csv_path\r\n";
			}
		}
	}
}