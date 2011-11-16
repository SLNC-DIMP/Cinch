<?php
/**
* @todo create metadata txt files (add to each folder)
* @todo create error listing txt files (add to zip directory)
* @todo create manifest of all files included (add to zip directory)
* @todo only dump the specified users files into a zip file (Done)
*/
class ZipCreationCommand extends CConsoleCommand {
	public $mail_user;
	public $file_info = 'file_info';
	
	public function __construct() {
	//	$this->manifest = new Manifest();
		$this->mail_user = new MailUser();
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
	* @param $user_id
	* @access public
	* @return object Yii DAO object
	*/
	public function getUserFiles($user_id) {
		$user_files = Yii::app()->db->createCommand()
			->select('id, temp_file_path, user_id')
			->from($this->file_info)
			->where(':user_id = user_id', array(':user_id' => $user_id))
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
			->where(':user_id = user_id and :added_to_archives = added_to_archives', 
				array(':user_id' => $user_id, ':added_to_archives' => 0))
			->queryAll();
		
		return $csv_files;
	}
	
	/**
	* Updates csv_meta_paths table that a CSV file has been added to a Zip archive.
	* @param $file_id
	* @access public
	* @return object Yii DAO object
	*/
	public function updateCsv($file_id) {
		$sql = "UPDATE csv_meta_paths SET added_to_archives = 1 WHERE id = ?";
		$csv_updated = Yii::app()->db->createCommand($sql);
		$csv_updated->execute(array($file_id));
		
		return $csv_updated;
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
		
		return Yii::getPathOfAlias('application.' . $type . '_downloads') . DIRECTORY_SEPARATOR . $user_name[0] . DIRECTORY_SEPARATOR . $type . '_files.zip';
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
	* @access public
	* @return object Yii DAO object
	*/
	public function updateFileInfo($file_id) {
		$sql = "UPDATE " . $this->file_info . " SET zipped = 1 WHERE id = ?";
		$write_zip = Yii::app()->db->createCommand($sql)
			->execute(array($file_id));
	}
	
	/**
	* Creates a list of the files in a Zip archive
	* See Yeslifer commnet at http://us3.php.net/manual/en/function.ziparchive-getnameindex.php
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
		if ($zip->open($zip_path, ZIPARCHIVE::CREATE) !== true) {
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
	* Write File level metadata
	* Add CSV files first.  This should usually be one file.
	* Add files to zip archive 10 to zip archive at a time.
	* This won't hold true for 1st 10 file loop.  Since CSV files won't be counted.  
	*/
	public function run($args) {
		$users = $this->getUserFileCount();
		
		foreach($users as $user) {
			$user_id = $user['user_id'];
			
			$user_path = $this->getUserPath($user_id);
			$user_files = $this->getUserFiles($user_id);
			$user_csv_files = $this->getCsvFiles($user_id);
			
			$zip_file = $this->zipOpen($user_path);
			
			foreach($user_csv_files as $user_csv_file) {
				$this->zipWrite($zip_file, $user_csv_file['path']);
				$this->updateCsv($user_csv_file['id']);
			}
			
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
				$this->updateFileInfo($file['id']);
			} 
			$manifest = $this->createManifest($zip_file, $user_path);
			$this->zipWrite($zip_file, $manifest);
			$this->zipClose($zip_file, $user_path);
			
			$this->writePath($user_id, $user_path); 
		//	$this->mail_user->UserMail($user_id);
		} 
	}
}
