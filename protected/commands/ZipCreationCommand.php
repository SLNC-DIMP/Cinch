<?php
/**
* @todo create metadata txt files (add to each folder)
* @todo create error listing txt files (add to zip directory)
* @todo create manifest of all files included (add to zip directory)
* @todo only dump the specified users files into a zip file (Done)
*/
class ZipCreationCommand extends CConsoleCommand {
	public $file_info = 'file_info';
	
	public function __construct() {
	//	$this->manifest = new Manifest();
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
	* Add zip files 10 at a time.
	*/
	public function run() {
		$users = $this->getUserFileCount();
		
		foreach($users as $user) {
			$user_files = $this->getUserFiles($user['user_id']);
			$user_path = $this->getUserPath($user['user_id']);
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
			}
			
			$this->zipClose($zip_file, $user_path);
			$this->createManifest($zip_file, $user['user_id']);
			$this->writePath($user['user_id'], $user_path); 
		} 
	}
}
