<?php
Yii::import('application.models.Utils');

class MetadataCommand extends CConsoleCommand {
	const PDF = 'application/pdf';
	const WORD = 'application/msword';
	const WORD2007 = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
	const PPT = 'application/vnd.ms-powerpoint';
	const PPT2007 = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
	const EXCEL = 'application/vnd.ms-excel';
	const EXCEL2007 = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	const JPEG = 'image/jpeg';
	const PNG = 'image/png';
	const GIF = 'image/gif';
	const TEXT = 'text/plain';
	
	/**
	* Retrieves a list of uploaded files that need to have their metadata extracted
	* Ignores 404 and virus check error files
	* @access public
	* @return object Yii DAO
	*/
	public function getFileList() {
		$get_file_list =  Yii::app()->db->createCommand()
			->select('id, temp_file_path, user_id, upload_file_id')
			->from('file_info')
			->where(array('and', 'metadata = 0', 
					array('or', 'temp_file_path != ""', 'temp_file_path IS NOT NULL')))
			->limit(1)
			->queryAll();
			
		return $get_file_list;
	}
	
	/**
	* Save metadata to correct metadate table
	* @param $file_type
	* @param $metadata
	* @param $file_id
	* @param $user_id
	* @access public
	* @return object Yii DAO
	*/
	public function writeMetadata($file_type, $metadata, $file_id, $user_id) {
		switch($file_type) {
			case self::PDF:
				$write = new PDF_Metadata;
				break;
			case self::WORD:
			case self::WORD2007:
				$write = new Word_Metadata;
				break;
			case self::PPT:
			case self::PPT2007:
				$write = new PPT_Metadata;
				break;
			case self::EXCEL:
			case self::EXCEL2007:
				$write = new Excel_Metadata;
				break;
			case self::GIF:
				$write = new Gif_Metadata;
				break;
			case self::JPEG:
				$write = new Jpg_Metadata;
				break;
			case self::PNG:
				$write = new Png_Metadata;
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
	* @return object Yii DAO
	*/
	public function updateFileInfo($file_id, $field = 'metadata', $file_type_id = NULL) {
		if($field == 'metadata') {
			$sql = "UPDATE file_info SET metadata = 1 WHERE id = ?";
			$values = array($file_id);
		} else {
			$sql = "UPDATE file_info SET fulltext_available = 1 WHERE id = ?";
			$values = array($file_id);
		}
		
		$metadata_processed = Yii::app()->db->createCommand($sql);
		$metadata_processed->execute($values);	
	}
	
	/**
	* Writes error to database if metadata could not be extracted from a file
	* Metadata extraction error code is 4
	* @param $file_id
	* @param $error_id
	* @access private
	* @return boolean
	*/
	private function tikaError($file_id, $error_id) {
		Utils::writeError($file_id, $error_id);	
		Utils::setProblemFile($file_id);
		
		return false;
	}
	/********************* End of Model elements ***************************************/
	
	/**
	* Extracts file level metadata using Apache Tika
	* @param $file
	* @param $extract - Options metadata is default text, html, xml other possible  values
	* @access private
	* @return array
	*/
	private function scrapeMetadata($file, $extract = 'metadata') {
		$tika_path = '';
		$tika = '/srv/local/tika-0.10/tika-app/target/tika-app-0.10.jar';
    //	$local = 'C:/"Program Files"/apache-tika-0.8/tika-app/target/tika-app-0.8.jar';
		$local = '/Users/deanfarrell/tika-app-1.0.jar';
		if(file_exists($tika)) { $tika_path = $tika; } else { $tika_path = $local; }
		
		$output = array();
		$command = 'java -jar ' . $tika_path . ' --' . $extract . ' ' . $file;
		
		exec(escapeshellcmd($command), $output);
		
		return $output;	
	}
	
	/**
	* Extracts file type from metadata array via Apache Tika making a call to a Java jar file
	* Array value will be of Content-Type: whatever/whatever
	* Code 4 Unable to extract metadata
	* Code 12 Unsupported file type
	* @param $metadata (array)
	* @access public
	* @return string
	*/
	public function getTikaFileType($metadata) {
		$constants = new ReflectionClass('MetadataCommand');
		$file_types = $constants->getConstants();
		
		if(!empty($metadata) || !is_null($metadata)) {
			$clean_file_type = $metadata['Content-Type'];
			
			if(!in_array($clean_file_type, $file_types)) {
				$clean_file_type = 12;
			}
		} else {
			$clean_file_type = 4;
		}
		
		return $clean_file_type;
	} 
	
	/**
	* Takes metadata file and creates associative array of it.
	* Metadata values come in like so, Content-Type: whatever/whatever, File-Type:text/plain so need to split this out on the :
	* and make the first part the key and the second part the array value.
	* Time/date values get truncated if using strrchr, while others, notably page count format incorrectly on stristr.
	* Hence the branching. 
	* @param $metadata (array)
	* @access public
	* @return array
	*/
	public function getMetadata($file) {
		$metadata = $this->scrapeMetadata($file);
		
		foreach($metadata as $metadata_value) {
			$field_name = trim(stristr($metadata_value, ':', true)); // returns portion of string before the colon
			if(preg_match('/(date|modified|created)/i', $metadata_value)) {
				$formatted_value = stristr($metadata_value, ':'); 
			} else {
				$formatted_value = strrchr($metadata_value, ':');
			}		
			$formatted_metadata[$field_name] = trim(substr_replace($formatted_value, '', 0, 1));
		}
		return $formatted_metadata;
	}
	
	/**
	* Extracts and writes file level metadata
	* Determine full text availability if not an audio, video or image file
	* If nothing needs to be done command exits
	* 4 code for can't grab metadata
	* 12 error code for unsupported file type
	*/
	public function run() {
		$files = $this->getFileList();
		
		if(empty($files)) { exit; }
		
		foreach($files as $file) {
			$metadata = $this->getMetadata($file['temp_file_path']);
			Utils::writeEvent($file['id'], 8);
			$file_type = $this->getTikaFileType($metadata);
			
			if($file_type == 4 || $file_type == 12) {
				$this->tikaError($file['id'], $file_type);
				$success = " Failed\r\n";
			} else {
				$this->writeMetadata($file_type, $metadata, $file['id'], $file['user_id']);
				$this->updateFileInfo($file['id'], 'metadata');
				
				if(!preg_match('/(image|audio|video)/', $file_type)) {
					$fulltext = $this->scrapeMetadata($file['temp_file_path'], 'text');
					Utils::writeEvent($file['id'], 12);
					
					if(!empty($fulltext)) {
						$this->updateFileInfo($file['id'], 'fulltext');	
					}
				} 
			
				$success = " Added\r\n";
			}
			echo $file['temp_file_path'] . $success; 
		} 
	}
}