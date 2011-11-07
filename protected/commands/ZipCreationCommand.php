<?php
/**
* @TODO create metadata txt files (add to each folder)
* @TODO create error listing txt files (add to zip directory)
* @TODO create manifest of all files included (add to zip directory)
* @TODO only dump the specified users files into a zip file
*/
class ZipCreationCommand extends CConsoleCommand {
	public $file_info = 'file_info';
	
	/**
	* Get list of users who have files to download grouped by user and number of files to zip.
	* @return Yii DAO object
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
	* @return Yii DAO object
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
	* Creates a new zip archive
	* @return Zip archive object
	*/
	public function createArchive($path) {
		$zip_path = $path . DIRECTORY_SEPARATOR . 'files_' . date('Y_m_d_H_i_sa') . '.zip';
		
		$zip = new ZipArchive();
		if ($zip->open($zip_path, ZIPARCHIVE::CREATE) !== true) {
			echo "cannot open <$path>\r\n";
			$zip = false;
		}

		return $zip;
	}
	
	/**
	* Write a file to a zip archive
	* @return Zip archive object
	*/
	public function ZipWrite(ZipArchive $zip, $file) {
		if(file_exists($file)) {
		//	$dir_sep = (PHP_OS != 'WINNT') ? '/' : '\\';
			$short_path = str_replace('/', '', strrchr($file['temp_file_path'], '/'));
		
			$zip->addFile($file, $short_path);
		}
		
	}
	
	/**
	* Close a zip archive
	* @return Zip archive object
	*/
	public function ZipClose(ZipArchive $zip, $path) {
		return $zip->close($path);
	}
	
	/**
	* Get user's base download path
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
	
	
	public function run() {
		$users = $this->getUserFileCount();
		
		foreach($users as $user) {
			$user_files = $this->getUserFiles($user['user_id']);
			$user_path = $this->getUserPath($user['user_id']);
			$zip_file = $this->createArchive($user_path);
			
			foreach($user_files as $file) {

				$this->ZipWrite($zip_file, $file['temp_file_path']);
			}
			
			$this->ZipClose($zip_file, $user_path);
		}
	}
}
