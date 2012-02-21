<?php
/**
* Blows up if EventCsv command if not explcitly called.
*/
Yii::import('application.commands.EventCsvCommand');
Yii::import('application.models.Utils');

class ZipCreationCommand extends CConsoleCommand {
	public $event_csv;
	public $mail_user;
	private $file_info = 'file_info';
	
	public function __construct() {
		$this->event_csv = new EventCsvCommand;
		$this->mail_user = new MailUser;
	} 
	
	/**
	* Get list of users who have files to download grouped by user and number of files to zip.
	* @access public
	* @return object Yii DAO object
	*/
	public function getUserFileCount() {
		$users_with_files = Yii::app()->db->createCommand()
			->select('user_id, COUNT("user_id") AS file_count')
			->from('file_info')
			->group('user_id')
			->order('file_count desc')
			->queryAll();
	
		return $users_with_files;
	}
	
	/**
	* Gets all a user's files for which zip files haven't been created.
	* Problem file error 2 is checksum not created
	* Problem file error 4 is couldn't get metadata
	* @param $user_id
	* @access public
	* @return object Yii DAO object
	*/
	public function getUserFiles($user_id) {
		$user_files = Yii::app()->db->createCommand()
			->select('id, temp_file_path, user_id')
			->from($this->file_info)
			->where(array('and', ':user_id = user_id', 'checksum_run = 1', 'metadata = 1')) // add in virus_check = 1
			->bindParam(":user_id", $user_id, PDO::PARAM_INT)
			->queryAll();
	
		return $user_files;
	}
	
	/**
	* Gets all a user's csv metadata files which haven't been added to a zip archive.
	* @param $user_id
	* @access public
	* @return object Yii DAO object
	*/
	public function getCsvFiles($user_id) {
		$csv_files = Yii::app()->db->createCommand()
			->select('id, path')
			->from('csv_meta_paths')
			->where(':user_id = user_id and :zipped = zipped', 
				array(':user_id' => $user_id, ':zipped' => 0))
			->queryAll();
		
		return $csv_files;
	}
	
	/**
	* Get user's base download path for Zip file creation
	* @param $user_id
	* @param $type
	* @access public
	* @return string
	*/
	private function getUserPath($user_id, $type = 'curl') {
		$user_name = Yii::app()->db->createCommand()
			->select('username')
			->from('user')
			->where(':id = id', array(':id' => $user_id))
			->queryColumn();
		
		$type = ($type == 'curl') ? 'curl' : 'ftp';

		return Yii::getPathOfAlias('application.' . $type . '_downloads') . DIRECTORY_SEPARATOR . $user_name[0];
	}
	
	/**
	* Write the Zip archive path to the db
	* @param $user_id
	* @param $path
	* @access public
	* @return object Yii DAO object
	*/
	public function writePath($user_id, $path) {
		$sql = "INSERT INTO zip_gz_downloads(user_id, archive_path) VALUES(?, ?)";
		$write_zip = Yii::app()->db->createCommand($sql)
			->execute(array($user_id, $path));
	}
	
	/**
	* Write the Zip archive path to the db
	* @param $file_id
	* @param $tablename
	* @access public
	* @return object Yii DAO object
	*/
	public function updateFileInfo($file_id, $tablename = 'file_info') {
		$sql = "UPDATE " . $tablename . " SET zipped = 1 WHERE id = ?";
		$write_zip = Yii::app()->db->createCommand($sql)
			->execute(array($file_id));
	}
	
	/**
	* Creates a list of the files in a Zip archive
	* See Yeslifer comment at http://us3.php.net/manual/en/function.ziparchive-getnameindex.php
	* as numFiles appears to be an undocumented method
	* Using preg_split as explode might not work with Windows in this case
	* @param $zip
	* @param $zip_path
	* @access public
	*/
	public function createManifest(ZipArchive $zip, $zip_path) {
		$manifest_pieces = preg_split('/(\/|\\\)/', $zip_path);
		array_pop($manifest_pieces);
		$manifest_path = implode('/', $manifest_pieces);
		echo $manifest_path; exit;
		$full_path = $manifest_path . '/' . 'file_manifest.csv';
		$file_count = $zip->numFiles;
		
		$fh = fopen($full_path, 'wb');
		fputcsv($fh, array('This Zip file contains: ' . $file_count . " files, listed below."));
		fputcsv($fh, array('id', 'Filename'));
		for($i = 0; $i < $file_count; $i++) {  
			fputcsv($fh, array($i + 1, $zip->getNameIndex($i)));
		} 
		fclose($fh);
		
		return $full_path;
	}
	
	/**
	* Creates a new zip archive
	* @param $zip_path
	* @access public
	* @return object Zip archive object
	*/
	public function zipOpen($zip_path) {
		$zip = new ZipArchive();
		if ($zip->open($zip_path . '/downloads_' . date('Y_m_d'). '.zip', ZIPARCHIVE::CREATE) !== true) {
			echo "cannot open <$path>\r\n";
			$zip = false;
		}

		return $zip;
	}
	
	/**
	* Write a file to a zip archive
	* @param $zip ZipArchive object
	* @param $file
	* @access public
	* @return object Zip archive object
	*/
	public function zipWrite(ZipArchive $zip, $file) {
		if(file_exists($file)) {
			$short_path = str_replace('/', '', strrchr($file, '/'));
			echo $short_path . "\r\n";
			$zip->addFile($file, $short_path);
		}
	}
	
	/**
	* Close a zip archive
	* @param $zip ZipArchive object
	* @param $path
	* @access public
	* @return object Zip archive object
	*/
	public function zipClose(ZipArchive $zip, $path) {
		return $zip->close($path);
	}
	
	/**
	* Add files to zip archive, including writing event to file_event_history
	* Create file_event_history csv here.  Otherwise it will be missing Zip event.
	* Add generated CSV files last.  
	* Adds file event list as well as metadata files and error file if it exists.
	* Add files to zip archive 10 to zip archive at a time.
	* This won't hold true for 1st 10 file loop.  Since CSV files won't be counted. 
	* Event code 9 is Zipped for download 
	*/
	public function run($args) {
		$users = $this->getUserFileCount();
		if(empty($users)) { exit; }
		
		foreach($users as $user) {
			$user_id = $user['user_id'];
			
			$user_path = $this->getUserPath($user_id);
			$user_files = $this->getUserFiles($user_id);
			$user_csv_files = $this->getCsvFiles($user_id);
			
			$zip_file = $this->zipOpen($user_path);
			
			$file_count = 0;
			foreach($user_files as $file) {
				if($file_count < 10) { 
					$file_count++;
				} else {
					$this->zipClose($zip_file, $user_path);
					$zip_file = $this->zipOpen($user_path);
					$file_count = 0;
				}
				$this->zipWrite($zip_file, $file['temp_file_path']);
				Utils::writeEvent($file['id'], 9);
				$this->updateFileInfo($file['id']); 
			} 
			
			// create file event CSV now that file events should be over
			$this->event_csv->actionIndex();
			
			foreach($user_csv_files as $user_csv_file) {
				$this->zipWrite($zip_file, $user_csv_file['path']);
				$this->updateFileInfo($file['id'], 'csv_meta_paths');
			}
			
			$manifest = $this->createManifest($zip_file, $user_path);
			$this->zipWrite($zip_file, $manifest);
			$this->zipClose($zip_file, $user_path);
			
			$this->writePath($user_id, $user_path); 
			$this->mail_user->UserMail($user_id); 
		} 
	}
}
