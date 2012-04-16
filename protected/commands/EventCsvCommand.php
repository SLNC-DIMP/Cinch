<?php
Yii::import('application.models.MakeCsv');
Yii::import('application.models.Utils');

/**
* EventCsvCommand class file
* This is the command for creation of event csv manifest for a user's downloaded files.
* @catagory Event CSV
* @package Event CSV
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license CC0 1.0 Universal {@link http://creativecommons.org/publicdomain/zero/1.0/}
*/

/**
* This is the command for creation of event csv manifest for a user's downloaded files.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license CC0 1.0 Universal {@link http://creativecommons.org/publicdomain/zero/1.0/}
*/
class EventCsvCommand extends CConsoleCommand {
	/**
	* Implements MakeCsv model class
	* @var $makecsv
	*/
	public $makecsv;
	
	/**
	* Create new MakeCsv model class
	*/
	public function __construct() {
		$this->makecsv = new MakeCsv;
	}
	
	/**
	* Gets event listings for a user's files 
	* @access public
	* @return object Yii DAO
	*/
	public function getEvents() {
		$sql = "SELECT file_info.id AS file_key, org_file_path, temp_file_path, event_name, event_time, file_info.user_id
				FROM file_info, file_event_history, (
					SELECT *
					FROM event_list
				) AS event_listings
				WHERE file_info.id = file_event_history.file_id
				AND event_listings.id = file_event_history.event_id
				AND virus_check = 1
				AND checksum_run = 1
				AND metadata = 1
				AND zipped = 1
				AND events_frozen = 0
				ORDER BY file_info.id ASC, file_event_history.event_time ASC"; 
		
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
		} else {
			echo "There are no event reports to generate'\n";
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
				Utils::freezeEvents($event['file_key']);
			}
		} else {
			echo "There are no new events to add to a csv file'\n";
		}
	}
}