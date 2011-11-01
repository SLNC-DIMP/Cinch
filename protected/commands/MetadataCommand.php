<?php
class MetadataCommand extends CConsoleCommand {
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
	
	public function writeMetadata(array $metadata, $file_type) {
		$sql = "UPDATE file_info SET problem_file = 4 WHERE id = :id";
		$tika_metadata = Yii::app()->db->createCommand($sql);
	}
	
	/**
	* Write file processed for metadata
	* @param $file_id
	* @access public
	*/
	public function updateFileInfo($file_id) {
		$sql = "UPDATE file_info SET metadata = 1 WHERE id = :id";
		$metadata_processed = Yii::app()->db->createCommand($sql);
		$metadata_processed->bindParam(":id", $file_id, PDO::PARAM_INT);
		$metadata_processed->execute();	
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
		$command = 'java -jar ' .$tika_path . ' --metadata ' . $file;
		
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
		
		foreach($metadata as $metadata_value):
			$field_name = trim(stristr($metadata_value, ':', true)); // returns portion of string before the colon		
			$formatted_metadata[$field_name] = trim(strrchr($metadata_value, ':')); 
		endforeach;
	
		return $formatted_metadata;
	}
	
	public function run() {
		$files = $this->getFileList();
		
		foreach($files as $file) {
			try {
				$metadata = $this->getMetadata($file['temp_file_path']);
				$file_type = $this->getTikaFileType($metadata);
				print_r($metadata) . "\r\n";
				echo $file_type . "\r\n";
				$write_metadata = $this->writeMetadata($metadata, $file_type);
				$update_filetype_metadata = $this->updateFileInfo($file_type, $file['id']);
			} catch(Exception $e) {
				$this->tikaError($file['id']);
				continue;
			}
		}
	}
}