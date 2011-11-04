<?php
class MetadataCommand extends CConsoleCommand {
	const PDF = 'application/pdf';
	const WORD2003 = 'application/msword';
	const WORD2007 = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
	const TEXT = 'text/plain';
	
	/**
	* Retrieves a list of uploaded files that need to have their metadata extracted
	* @return object Data Access Object
	*/
	public function getFileList() {
		$get_file_list =  Yii::app()->db->createCommand()
			->select('id, temp_file_path, user_id, upload_file_id')
			->from('file_info')
			->where('metadata = :metadata', array(':metadata' => 0))
			->limit(1)
			->queryAll();
			
		return $get_file_list;
	}
	
	/**
	* Save metadata to correct
	* @return object Data Access Object
	*/
	public function writeMetadata($file_type, $metadata, $file_id, $user_id) {
		switch($file_type) {
			case self::PDF:
				$write = new PDF_Metadata;
				break;
			case self::WORD2003:
				$write = new WORD2003_Metadata;
				break;
			case self::WORD2007:
				$write = new WORD2007_Metadata;
				break;
			case self::TEXT:
				$write = new Text_Metadata;
				break;
		}
		$write->writeMetadata($metadata, $file_id, $user_id);
	}
	
	/**
	* Write file processed for metadata
	* @param $file_id
	* @access public
	*/
	public function updateFileInfo($file_id) {
		$sql = "UPDATE file_info SET metadata = 1, file_type_id = 1 WHERE id = ?";
		$metadata_processed = Yii::app()->db->createCommand($sql);
		$metadata_processed->execute(array($file_id));	
	}
	
	/**
	* Writes error to database if metadata could not be extracted from a file
	* Metadata extraction error code is 4
	* @param $file_id
	* @access public
	* @return boolean
	*/
	public function tikaError($file_id) {
		$sql = "UPDATE file_info SET problem_file = 4 WHERE id = :id";
		$tika_error = Yii::app()->db->createCommand($sql);
		$tika_error->bindParam(":id", $file_id, PDO::PARAM_INT);
		$tika_error->execute();	
		
		return false;
	}
	/********************* End of Model elements ***************************************/
	
	/**
	* Extracts file level metadata using Apache Tika
	* @access private
	* @return array
	*/
	private function scrapeMetadata($file) {
		$tika_path = '';  
    
		
		$output = array();
		$command = 'java -jar ' . $tika_path . ' --metadata ' . $file;
		
		exec(escapeshellcmd($command), $output);
		
		return $output;	
	}
	
	/**
	* Extracts file type from metadata array via Apache Tika making a call to a Java jar file
	* Array value will be of Content-Type: whatever/whatever
	* @param $metadata (array)
	* @access public
	* @return string
	*/
	public function getTikaFileType(array $metadata) {
		$clean_file_type = trim(substr_replace($metadata['Content-Type'], '', 0, 1));
	
		return $clean_file_type;
	} 
	
	/**
	* Takes metadata file and creates associative array of it.
	* Metadata values come in like so, Content-Type: whatever/whatever, File-Type:text/plain so need to split this out on the :
	* and make the first part the key and the second part the array value.
	* @param $metadata (array)
	* @access public
	* @return array
	*/
	public function getMetadata($file) {
		$metadata = $this->scrapeMetadata($file);
		
		foreach($metadata as $metadata_value) {
			$field_name = trim(stristr($metadata_value, ':', true)); // returns portion of string before the colon		
			$formatted_metadata[$field_name] = trim(strrchr($metadata_value, ':')); 
		}
	
		return $formatted_metadata;
	}
	
	public function run() {
		$files = $this->getFileList();
		if(empty($files)) { exit; }
		
		foreach($files as $file) {
			$metadata = $this->getMetadata($file['temp_file_path']);
			$file_type = $this->getTikaFileType($metadata);
			$this->writeMetadata($file_type, $metadata, $file['upload_file_id'], $file['user_id']);
			$this->updateFileInfo($file['id']);
		}
	}
}
// UPDATE `files_for_download` SET `processed`=0 WHERE `processed` = 1