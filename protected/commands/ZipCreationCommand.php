<?php
Yii::import('application.models.MakCsv');
Yii::import('application.commands.EventCsvCommand');
Yii::import('application.models.Utils');

/**
* ZipCreationCommand class code
*
* This is the command for creation of zip files for a user's downloaded files.
* @catagory ZipCreation
* @package ZipCreation
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.4
* @license Unlicense {@link http://unlicense.org/}
*/

/**
* This is the command for creation of zip files for a user's downloaded files.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.4
* @license Unlicense {@link http://unlicense.org/}
*/
class ZipCreationCommand extends CConsoleCommand {
	/**
	* Implements MakeCsv modal class
	* @var $make_csv
	*/
	public $make_csv;
	/**
	* Implements the EventCsv command
	* @var $event_csv
	*/
	public $event_csv;
	/**
	* Implements MailUser class
	* @var $mail_user
	*/
	public $mail_user;
	/**
	* Gets the file_info table
	* @var $file_info
	*/
	private $file_info = 'file_info';
	/**
	* max zip number of file = 65500
	* @var integer
	*/
	const ZIP_FILE_LIMIT = 65500;
	/**
	* max zip size = 536870912 bytes 0.5 GB  
	* Otherwise file requires too much memory to download
	* @var integer
	*/
	const ZIP_SIZE_LIMIT = 536870912;
	
	/**
	* Instantiates MakeCsv, EventCsvCommand and MailUser classes for use in zip creation
	*/
	public function __construct() {
		$this->make_csv = new MakeCsv;
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
			->where(array('and', 'events_frozen = 0', 
								 'virus_check = 1',
								 'checksum_run = 1',
								 'metadata = 1',
                                 'zipped != 1',
								 'temp_file_path IS NOT NULL', 
								 'temp_file_path !=""'))
			->group('user_id')
			->order('file_count desc')
			->queryAll();

		return $users_with_files;
	}
	
	/**
	* Gets all a user's files for which zip files haven't been created.
	* Yii order clause didn't seem to work here.  So rewrote the query
	* @param $user_id
	* @access public
	* @return object Yii DAO object
	*/
	public function getUserFiles($user_id) {
		$sql = "SELECT id, temp_file_path, problem_file, user_id
			FROM file_info
			WHERE :user_id = user_id
			AND zipped != 1
			AND	checksum_run = 1
			AND	metadata = 1 
			AND	virus_check = 1
			AND temp_file_path IS NOT NULL
			AND	temp_file_path != ''
			ORDER BY problem_file DESC";
			
		$user_files = Yii::app()->db->createCommand($sql)
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
	* @access protected
	* @return string
	*/
	protected function getUserPath($user_id, $type = 'curl') {
		$user_name = Yii::app()->db->createCommand()
			->select('username')
			->from('user')
			->where(':id = id', array(':id' => $user_id))
			->queryColumn();
		
		$type = ($type == 'curl') ? 'curl' : 'ftp';

		return Yii::getPathOfAlias('application.' . $type . '_downloads') . '/' . $user_name[0] . '/' . $user_name[0] . '_downloads_' . date('Y_m_d') . '.zip';
	}
	
	/**
	* Creates incremented new file path if a zip file is approaching its size limit
	* @param $zip_path
	* @access protected
	* @return string
	*/
	protected function addNewArchive($zip_path) {
		$path = explode('.', $zip_path);
		$pieces = count($path);
		$file_num_piece = $path[$pieces - 2];
		preg_match('/-[0-9]{1,}$/', $file_num_piece, $matches);
	
		if(!$matches) {
			$path[$pieces - 2] = $file_num_piece .'-1';
		} else {
			$get_cur_num = explode('-', $file_num_piece);
			$sub_piece = count($get_cur_num);
			$get_cur_num[$sub_piece - 1] = end($get_cur_num) + 1;
			$path[$pieces - 2] = implode('-', $get_cur_num);
		}

		return implode('.', $path);
	}
	
	/**
	* Write the Zip archive path to the db
	* @param $user_id
	* @param $path
	* @access public
	* @return object Yii DAO object
	*/
	public function writeZipPath($user_id, $path) {
		$sql = "INSERT INTO zip_gz_downloads(user_id, path) VALUES(?, ?)";
		Yii::app()->db->createCommand($sql)
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
		Yii::app()->db->createCommand($sql)
			->execute(array($file_id));
	}
	
	/**
	* Creates a list of the files in a Zip archive
	* See Yeslifer comment at http://us3.php.net/manual/en/function.ziparchive-getnameindex.php
	* as numFiles appears to be an undocumented method
	* Using preg_split as explode might not work with Windows in this case
	* @param ZipArchive $zip
	* @param $zip_path
	* @access protected
	*/
	protected function createManifest(ZipArchive $zip, $zip_path) {
		$manifest_pieces = preg_split('/(\/|\\\)/', $zip_path);
		$file_num = strrchr(array_pop($manifest_pieces), '-');
		$file_num = strstr($file_num, '.', true);
		
		if(!$file_num and !preg_match('/[0-9]{1,}$/', $file_num)) {
			$file_num = '';
		}
		
		$manifest_path = implode('/', $manifest_pieces);
		$full_path = $manifest_path . '/' . 'file_manifest_' . date('Y-m-d') . $file_num . '.csv';
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
	* Creates zip file manifest
	* Adds it to the zip file and writes the results to the db
	* @param ZipArchive $zip_file
	* @param $user_path
	* @param $user_id
	* @access protected
	*/
	protected function addManifest(ZipArchive $zip_file, $user_path, $user_id) {
		$manifest_path = $this->createManifest($zip_file, $user_path);
		$this->zipWrite($zip_file, $manifest_path);
		$this->zipClose($zip_file, $user_path);
			
		$csv_path_id = $this->make_csv->addPath($user_id, $manifest_path); // add manifest to db
		$this->updateFileInfo($csv_path_id, 'csv_meta_paths'); // mark manifest as zipped 
	}
	
	/**
	* Writes metadata and errors_csv files to every zip archive
	* Events csv file will only show up in the last archive as events haven't ended yet.
	* Then writes the results to the db
	* @param ZipArchive $zip_file
	* @param $user_id
	* @param $mark_zipped
	* @access protected
	*/
	protected function addMetaCsvFiles(ZipArchive $zip_file, $user_id, $mark_zipped = true) {
		$user_csv_files = $this->getCsvFiles($user_id);
		foreach($user_csv_files as $user_csv_file) {
			$this->zipWrite($zip_file, $user_csv_file['path']);
			
			if($mark_zipped == true) {
				$this->updateFileInfo($user_csv_file['id'], 'csv_meta_paths');
			}
		}
	}
	
	/**
	* Creates a new zip archive and add problem file directory
	* fopen a hacky work around to generate a file so zip file size can be checked
	* @param $zip_path
	* @access public
	* @return object Zip archive object
	*/
	public function zipOpen($zip_path) {
		if(!file_exists($zip_path)) {
			$fh = fopen($zip_path, 'wb');
			fclose($fh);
		}
		
		$zip = new ZipArchive();
		
		if ($zip->open($zip_path, ZIPARCHIVE::CREATE) !== true) {
			echo "cannot open <$zip_path>\r\n";
			$zip = false;
		} else {
			$this->addZipDir($zip, $zip_path);
		}

		return $zip;
	}
	
	/**
	* Add a problem file directory to zip archive.
	* @param ZipArchive $zip
	* @param $zip_path
	* @access protected
	*/
	protected function addZipDir(ZipArchive $zip, $zip_path) {
		if ($zip->open($zip_path) === TRUE && $zip->locateName('problem_files') == false) {
			$zip->addEmptyDir('problem_files');
		} else {
			$zip = false;
		}
	}
	
	/**
	* Write a file to a zip archive
	* @param ZipArchive $zip
	* @param $file
	* @param $problem
	* @access public
	*/
	public function zipWrite(ZipArchive $zip, $file, $problem = false) {
		if(file_exists($file)) {
			$short_path = str_replace('/', '', strrchr($file, '/'));
			
			if($problem == true) {
				$short_path = 'problem_files/' . $short_path; 
			}
			
			echo $short_path . "\r\n";
			$zip->addFile($file, $short_path);
		}
	}
	
	/**
	* Close a zip archive
	* @param ZipArchive $zip
	* @param $path
	* @access public
	* @return object Zip archive object
	*/
	public function zipClose(ZipArchive $zip, $path) {
		return $zip->close($path);
	}
	
	/**
	* Check a zip file's size to see if it's nearing the 2GB limit of certain file systems
	* Or conversely if adding the current file would put zip file over the limit.
	* @param $file
	* @access protected
	* @return string
	*/
	protected function sizeCheck($file) {
		return filesize($file);
	}
	
	/**
	* Adds event csv to zip file and updates that it's been zipped.
	* Event code 9 is Zipped for download
	* @param ZipArchive $zip_file 
	* @param $file_path
	* @param $file_id
	* @param $problem
	* @access protected
	*/
	protected function zipWriteEvents(ZipArchive $zip_file, $file_path, $file_id, $problem = false) {
		$this->zipWrite($zip_file, $file_path, $problem);
		Utils::writeEvent($file_id, 9);
		$this->updateFileInfo($file_id);
	}
	
	/**
	* Add files to zip archive, including writing event to file_event_history
	* Create file_event_history csv here.  Otherwise it will be missing Zip event.
	* Add generated CSV files last.  
	* Adds file event list as well as metadata files and error file if it exists.
	* Add files to zip archive 10 to zip archive at a time.
	* This won't always hold true since CSV files won't be counted.
	* Creates a new zip file for user if zip archive will go over 0.5GB with addition of new file or if archive has more than 65500 files
	* Event code 9 is Zipped for download
    * @param $args
	* @access public
	*/
	public function run($args) {
		$users = $this->getUserFileCount();
		if(empty($users)) { echo "Nothing to zip\r\n"; exit; }
		
		foreach($users as $user) {
			$user_id = $user['user_id'];
			
			$user_path = $this->getUserPath($user_id);
			$user_files = $this->getUserFiles($user_id);
			$zip_file = $this->zipOpen($user_path);
			
			$file_count = 0;
			foreach($user_files as $file) {
				$curr_file_size = $this->sizeCheck($file['temp_file_path']);
				$curr_zip_size = $this->sizeCheck($zip_file->filename);
	
				if((($curr_file_size + $curr_zip_size) < self::ZIP_SIZE_LIMIT) && ($zip_file->numFiles < self::ZIP_FILE_LIMIT)) {
					$this->zipWriteEvents($zip_file, $file['temp_file_path'], $file['id'], $file['problem_file']);
				} else {
					$this->addMetaCsvFiles($zip_file, $user_id, false);
					$this->addManifest($zip_file, $user_path, $user_id);
					$this->writeZipPath($user_id, $user_path);
					
					$file_count = 0; 
					$user_path = $this->addNewArchive($user_path); // switch to new zip file for current user
					$zip_file = $this->zipOpen($user_path);
					$this->zipWriteEvents($zip_file, $file['temp_file_path'], $file['id'], $file['problem_file']);
				}
				
				if($file_count < 10) { 
					$file_count++;
				} else {
					$this->zipClose($zip_file, $user_path);
					$zip_file = $this->zipOpen($user_path);
					$file_count = 0;
				}
			} 
			
			// create file event CSV now that file events should be over
			$this->event_csv->actionIndex();
			
			$this->addMetaCsvFiles($zip_file, $user_id);
			
			$this->addManifest($zip_file, $user_path, $user_id);
			$this->writeZipPath($user_id, $user_path); 
			
			// mail user
			$subject = 'You have Cinch files ready';
			$message = 'You have files ready for download from Cinch.  Please login at http://cinch.nclive.org to retrieve your files.';
			$this->mail_user->UserMail($user_id, $subject, $message); 
		} 
	}
}

