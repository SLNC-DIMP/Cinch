<?php
class MakeCsvCommand extends CConsoleCommand {
	public $meta_text;
	// public $error_list;
	
	public function __construct() {
		$this->meta_text = new MetaText();	
	//	$this->error_list = new ErrorList();
	}
	
	/**
	* Gets all downloaded files that have been fully processed.
	* @todo get this working "AND virus_check != 0"
	* @access public
	* @return object Yii DAO object
	*/
	public function getFiles() {
		$sql = "SELECT id, file_type_id, user_id 
		FROM file_info 
		WHERE file_type_id != 0
		AND	checksum IS NOT NULL
		AND metadata != 0";
		
		$user_files = Yii::app()->db->createCommand($sql)
			->queryAll();
	
		return $user_files;
	}
	
	/**
	* Get user's base download path for csv file creation
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
	
	public function run($args) {
		$files = $this->getFiles();
		if(empty($files)) { exit; }
		
		foreach($files as $file) {
			$csv_path = $this->getUserPath($file['user_id']);
			$metadata_table = $this->meta_text->findMetaTable($file['file_type_id']);
			$metadata = $this->meta_text->findMetadata(
				$metadata_table, 
				$file['id'], 
				$file['user_id']
			);
			
			$this->meta_text->write($csv_path, $metadata_table, $metadata, $file['user_id']);
		}
	}
}