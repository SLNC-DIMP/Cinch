<?php
/**
* Blows up command if not explcitly called.  I believe because MakeCsv isn't named MakeCsvCommand
* Don't want it named that as would show up as a command instead of merely being clase for others to descend from.
*/
Yii::import('application.models.MakeCsv');
 
class EventCsvCommand extends CConsoleCommand {
	public $makecsv;
	
	public function __construct() {
		$this->makecsv = new makeCsv;
	}
	/**
	* Gets event listings for a user's files 
	* @access public
	* @return object Yii DAO
	*/
	public function getEvents() {
		$sql = "SELECT org_file_path, temp_file_path, event_name, event_time, file_info.user_id
				FROM file_info, file_event_history, (
					SELECT *
					FROM event_list
				) AS event_listings
				WHERE file_info.id = file_event_history.file_id
				AND event_listings.id = file_event_history.event_id
				AND zipped != 1
				ORDER BY file_info.id ASC, event_listings.id ASC"; 
		
		$event_list = Yii::app()->db->createCommand($sql)
			->queryAll();
		
		return $event_list;
	}
	
	/**
	* Writes user event file list to a csv file and add csv file path to db.
	* Writes column headers only on first iteration
	* @param $file
	* @param $user_path
	* @access public
	*/
	public function makeReport($file, $user_path) {
		if(!empty($file)) {
			$event_list = $user_path . '/event_list_' . date('Y_m_d') . '.csv';
			
			if(!file_exists($event_list)) {
				$headers = array('Url', 'Filename', 'Event Name', 'Event Time');
			} else {
				$headers = 'Column headers already set';
			}
			$fh = fopen($event_list, 'ab');
			
			if(is_array($headers)) {
				fputcsv($fh, $headers);
				$this->makecsv->addPath($file['user_id'], $event_list);
			}
				
			if($file['temp_file_path'] != '') {
				$file_path = str_replace('/', '', strrchr($file['temp_file_path'], '/'));
			} else {
				$file_path = '';
			}
			fputcsv($fh, array($file['org_file_path'], $file_path, $file['event_name'], $file['event_time']));
			fclose($fh);
		}
	}
	
	/**
	* Writes each user event to correct csv file
	* @access public
	*/
	public function actionIndex() {
		$events = $this->getEvents();
		
		if(!empty($events)) {
			foreach($events as $event) {
				$csv_path = $this->makecsv->getUserPath($event['user_id']);
				$this->makeReport($event, $csv_path);
			}
		}
	}
}